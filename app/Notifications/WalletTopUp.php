<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class WalletTopUp extends Notification implements ShouldQueue
{
    use Queueable;

    private $trans;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($trans)
    {
        $this->trans = $trans;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database',OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
       
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->trans->id,
            'item' => "wallet",
            'action' => 'topup',
            'data' => "Your wallet topup successfully with ".$this->trans->total_amount." ".$this->trans->currency,
        ];
    }
    
    
     public function toOneSignal($notifiable)
    {
           return OneSignalMessage::create()
            ->subject('Wallet TopUp')
            ->body("Your wallet topup successfully with ".$this->trans->total_amount." ".$this->trans->currency)
            ->setIcon("")
            ->setUrl(config("app.front-end-url")."/user/wallet")
            ->setImageAttachments("");        
    }
}
