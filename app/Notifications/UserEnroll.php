<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class UserEnroll extends Notification implements ShouldQueue
{
    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
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
            'id' => $this->order->item()->id,
            'item' => $this->order->item()->Entity,
            'action' => 'enrolled',
            'data' => 'You are Enrolled in '.$this->order->item()->Entity,
        ];
    }
    
    
     public function toOneSignal($notifiable)
    { 
        return OneSignalMessage::create()
            ->subject('Successfully buy a '.$this->order->item()->Entity)
            ->body('You are enrolled to ('.$this->order->item()->_title().')')
            ->setIcon($this->order->item()->_image() ? $this->order->item()->_image() : '')
            ->setUrl(config("app.front-end-url") .($this->order->item()->Entity == "Course Chapter" ? "/courses/".$this->order->item()->course_id : ($this->order->item()->Entity == "Course" ? "/courses/" : ($this->order->item()->Entity == "Live Session" ? "/live-sessions/" : "/packages/")).$this->order->item()->id))
            ->setImageAttachments($this->order->item()->_image());        
    }
}
