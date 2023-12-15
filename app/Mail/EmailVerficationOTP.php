<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerficationOTP extends Mailable
{
    use Queueable, SerializesModels;
    
    public $timeout = 60;
    public $failOnTimeout = true;
    public $tries = 3;

    public $user;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.verify_otp')
        ->with($this->data)
        ->from($this->data['from'])
        ->subject('Email Verfication Code');
    }
}
