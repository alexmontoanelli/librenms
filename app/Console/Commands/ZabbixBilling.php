<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ZabbixBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w8:ZabbixBilling';

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

        $bills = \Http::
        withHeaders(['X-Auth-Token' => $token])
            ->get("$urlApi/api/v0/bills")
            ->json();


        $discovery = collect();
        $data = collect();
        foreach ($bills['bills'] as $bill){

            if (count($bill['ports']) == 0) continue;

            $discovery[] = ['{#BILL_ID}' => $bill['bill_id'], '{#BILL_NAME}' => $bill['bill_name']];

            $data[$bill['bill_id']] = $bill;

        }

        \Storage::disk('public')->put('_discovery.json', $discovery->toJson());
        \Storage::disk('public')->put('_data.json', $data->toJson());

        return 0;
    }
}
