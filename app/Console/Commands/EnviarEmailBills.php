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
        $token = env('W8_LIBRENMS_TOKEN','5729ebef0827600d97e1339f2270d9b3');

        $_billsEnviar = env('W8_LIBRENMS_BILL_ENVIAR','');
        $billsEnviar = collect(explode(',', $_billsEnviar));

        $bills = \Http::
            withHeaders(['X-Auth-Token' => $token])
            ->get("$urlApi/api/v0/bills")
            ->json();

        foreach ($bills['bills'] as $bill){

            if (!$billsEnviar->contains($bill['bill_id'])){
                continue;
            }

            rescue(fn() => $this->_processaBill($bill, $urlApi, $token), function($e){
                $this->error('Erro ao processar ' . $e->getMessage());
            });

        }

        return 0;
    }

    private function _processaBill($bill, $urlApi, $token){


        if (env('W8_MAIL_PRODUCAO', false) == false)
            if ($bill['bill_id'] != 13) return;

        $this->info('Processando bill ' . $bill['bill_name']);

        if (count($bill['ports']) == 0) {
            $this->warn('Ignorando, sem portas');
            return;
        };

        $historyResponse = \Http::withHeaders(['X-Auth-Token' => $token])
            ->get("$urlApi/api/v0/bills/{$bill['bill_id']}/history")
            ->json();

        $history = $historyResponse['bill_history'];

        if (count($history) == 0){
            $this->warn('Sem historico (1)');
            return;
        }

        $history = collect($history)->sortByDesc->bill_dateto->slice(1,13)->values();

        if (count($history) == 0){
            $this->warn('Sem historico (2)');
            return;
        }

        $grpah = \Http::get("http://librenms.w8telecom.com.br/api/v0/bills/{$bill['bill_id']}/history/{$history[0]['bill_hist_id']}/graphs/bits", ['api_token'=>'5729ebef0827600d97e1339f2270d9b3', 'output' => 'base64']);

        if (!$grpah->successful()){
            $this->error('Erro ao pegar imagem');
            $this->warn($grpah->body());
            return;
        }

        $grpah = substr($grpah, 0, -66);

        $fileName = md5($history[0]['bill_hist_id']) . '.png';

        $localPath = \Storage::disk('public')->path($fileName);

        \Storage::disk('public')->put($fileName, $grpah);

        $remotePath =  \Storage::disk('public')->url($fileName);

        foreach($history as $hist){
            $this->info("\t" . $hist['bill_datefrom']);
        }

        //$this->_makeEmail($bill, $history, $localPath, $remotePath);

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
