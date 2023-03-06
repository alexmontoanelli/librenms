<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;
    public $history;
    public $localPath;
    public $remotePath;

    public $percentil;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bill, $history, $localPath, $remotePath)
    {

        $this->bill = $bill;
        $this->history = $history;
        $this->localPath = $localPath;
        $this->remotePath = $remotePath;

        require_once 'includes/billing.php';

        $this->percentil   = getPercentilEscolhido($bill);

        $dates = substr($history[0]['bill_datefrom'], 0, 7);

        $this->subject(sprintf("Billing %s, ref: %s (%s)", $bill['bill_name'], $dates, $bill['bill_id']));


    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('w8.billing-email');
    }
}
