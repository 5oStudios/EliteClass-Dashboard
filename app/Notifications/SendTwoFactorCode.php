<?php

namespace App\Notifications;

use App\Services\SESService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendTwoFactorCode extends Notification implements ShouldQueue
{
    use Queueable;

    public $timeout = 60;
    public $failOnTimeout = true;
    public $tries = 3;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Aws\Result
     */
    public function toMail(User $notifiable)
    {
//        return (new MailMessage())
//            ->subject('Elite-Class Two Factor Authentication Code')
//            ->greeting("Hi, {$notifiable->fname}")
//            ->line("Your two factor code is {$notifiable->two_factor_code}")
//            ->action('Verify Here', route('verify.index',['code' => $notifiable->two_factor_code]))
//            ->line('The code will expire in 10 minutes')
//            ->line('If you have not tried to login, ignore this message.');
        $to = $notifiable->email;
        $subject = 'Elite-Class Two Factor Authentication Code';
        $bladeView = 'email.verify_otp'; // Blade template for two-factor authentication email
        $data = [
            'fname' => $notifiable->fname,
            'code' => $notifiable->two_factor_code,
            'actionUrl' => route('verify.index', ['code' => $notifiable->two_factor_code])
        ];

        $sesService = new SESService();
        return $sesService->sendEmail($to, $subject, $bladeView, $data);

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
            //
        ];
    }
}
