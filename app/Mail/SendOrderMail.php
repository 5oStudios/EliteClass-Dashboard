<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $timeout = 60;
    public $failOnTimeout = true;
    public $tries = 3;
    
    public $x, $order, $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($x, $order, $data)
    {
        $this->x = $x;
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
        return $this->markdown('email.orderslip')->subject('Elite-Class Payment Status');
    }
}
