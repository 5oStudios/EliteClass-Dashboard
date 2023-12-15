<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\LogSendinggMail;

class LogSendingMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
          $message = $event->message;

          LogSendinggMail::create([
            'to' => json_encode($message->getTo()),
            'from' => json_encode($message->getFrom()),
            'sender' => json_encode($message->getSender()),
            'date' => json_encode($message->getDate()),
            'subject' => json_encode($message->getSubject()),
            'body' => json_encode($message->getBody()),
          ]);
          
        //   Log::channel('mail')->debug(get_class_methods($message));
    }
}
