<?php

namespace App\Notifications;

use \App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;

class userEarnedRefralNotification extends Notification implements ShouldQueue
{    
    use Queueable;

    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
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
    public function toArray($notifiable)
    {
        return [
            
            'id' => $this->user['id'],
            'item' => 'user',
            'action' => 'earned_referral',
            'data' => 'You earned a referral',
        ];
    }
    
     public function toOneSignal($notifiable)
    {
        $user = User::find($this->user['id']);
           return OneSignalMessage::create()
            ->subject('You earned a referral')
            ->body('('.$user->fname . ' ' . $user->lname.') added you as referral')
            ->setIcon($user->user_img ? url('images/user_img/' . $user->user_img) : '')
            ->setUrl(config("app.front-end-url")."/user/wallet")
            ->setImageAttachments('');        
    }
}
