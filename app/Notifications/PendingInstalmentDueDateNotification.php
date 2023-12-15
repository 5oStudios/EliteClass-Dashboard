<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use DateTime;

class PendingInstalmentDueDateNotification extends Notification {

    use Queueable;

    private $inst;
    private $currency;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($inst) {
        $this->inst = $inst;
        $this->currency = \App\Currency::where('default', '=', '1')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return [OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
                        ->line('The introduction to the notification.')
                        ->action('Notification Action', url('/'))
                        ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        
        return [
            'id' => $this->inst->id,
            'item' => "pending_installment",
            'action' => 'pending_installment_duedate_alert',
            'data' => "You have a pending installment where DueDate: ".$this->inst->due_date." and Amount: ".$this->inst->amount." ".$this->currency->icon,
            'extra' => ["url"=>'/user/invoices?id='.$this->inst->id."&amount=".$this->inst->amount."&title=".$this->inst->order->title],
        ];
    }

    public function toOneSignal($notifiable) {
        
        return OneSignalMessage::create()
                        ->subject('Pending Installment DueDate')
                        ->body( "You have a pending installment where DueDate: ".$this->inst->due_date." and Amount: ".$this->inst->amount." ".$this->currency->icon)
                        ->setIcon('')
                        ->setUrl(config("app.front-end-url").'/user/invoices?id='.$this->inst->id."&amount=".$this->inst->amount."&title=".$this->inst->order->title)
                        ->setImageAttachments("");
    }

}
