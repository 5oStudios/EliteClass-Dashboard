<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $timeout = 60;
    public $failOnTimeout = true;
    public $tries = 3;
    
    public $order, $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $data)
    {
        $this->order = $order;
        $this->data = $data;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.testmail')->subject('UPayment WebhookURL Trigger');
    }
}
