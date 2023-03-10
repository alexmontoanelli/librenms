<?php

namespace App\Console\Commands;

use App\Mail\BillingMail;
use App\Models\Storage;
use Illuminate\Console\Command;

class EnviarEmailBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w8:enviarEmailBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Coletando bills');

        $urlApi =  env('W8_LIBRENMS_URL', 'http://librenms.w8telecom.com.br');
        $token = env('W8-LIBRENMS_TOKEN','5729ebef0827600d97e1339f2270d9b3');


        $bills = \Http::
            withHeaders(['X-Auth-Token' => $token])
            ->get("$urlApi/api/v0/bills")
            ->json();

        foreach ($bills['bills'] as $bill){

            $this->info('Processando bill ' . $bill['bill_name']);

            if (env('W8_MAIL_PRODUCAO', false) == false)
                if ($bill['bill_id'] != 13) continue;

            if (count($bill['ports']) == 0) continue;

            $historyResponse = \Http::withHeaders(['X-Auth-Token' => $token])
                ->get("$urlApi/api/v0/bills/{$bill['bill_id']}/history")
                ->json();

            $history = $historyResponse['bill_history'];

            array_shift($history);

            $history = array_slice($history, 0, 13);

            $grpah = \Http::get("http://librenms.w8telecom.com.br/api/v0/bills/{$bill['bill_id']}/history/{$history[0]['bill_hist_id']}/graphs/bits", ['api_token'=>'5729ebef0827600d97e1339f2270d9b3', 'output' => 'base64'])
                ->body();

            $grpah = substr($grpah, 0, -66);

            $fileName = md5($history[0]['bill_hist_id']) . '.png';

            $localPath = \Storage::disk('public')->path($fileName);

            \Storage::disk('public')->put($fileName, $grpah);

            $remotePath =  \Storage::disk('public')->url($fileName);

            $this->_makeEmail($bill, $history, $localPath, $remotePath);

        }

        return 0;
    }



    private function _makeEmail($bill, $history, $localPath, $remotePath){

        $emails = collect(explode(',', env('W8_EMAILS', '')));

        if ($emails->count() == 0){
            $this->warn('Email não fornecido');
            return;
        }

        $mailable = new BillingMail($bill, $history, $localPath, $remotePath);

        $emails->each(function($email) use ($mailable){
            $mailable->to($email);
        });

        \Mail::send($mailable);

    }
}
