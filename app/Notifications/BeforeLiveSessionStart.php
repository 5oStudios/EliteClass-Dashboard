<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use DateTime;

class BeforeLiveSessionStart extends Notification {

    use Queueable;

    private $livestream;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($livestream) {
        $this->livestream = $livestream;
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
        $datetime1 = new DateTime($this->livestream->start_time);
        $datetime2 = new DateTime(now());
        $interval = $datetime1->diff($datetime2);
        return [
            'id' => $this->livestream->item()->id,
            'item' => $this->livestream->item()->Entity,
            'action' => 'upcoming_live',
            'data' => 'Live Stream (' . $this->livestream->_title() . ') will start after ' . $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes",
        ];
    }

    public function toOneSignal($notifiable) {
        $datetime1 = new DateTime($this->livestream->start_time);
        $datetime2 = new DateTime(now());
        $interval = $datetime1->diff($datetime2);
        return OneSignalMessage::create()
                        ->subject('Upcoming Live Stream')
                        ->body('Live Stream (' . $this->livestream->_title() . ') will start after ' . $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes")
                        ->setIcon($this->livestream->_image() ? $this->livestream->_image() : '')
                        ->setUrl(config("app.front-end-url").'/live-sessions/'.$this->livestream->id)
                        ->setImageAttachments($this->livestream->_image());
    }

}
