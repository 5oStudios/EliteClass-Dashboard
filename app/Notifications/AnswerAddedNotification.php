<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Support\Facades\Log;

class AnswerAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $answer;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($answer)
    {
        $this->answer = $answer;
        Log::info("run in AnswerAddedNotification");
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
            'id' => $this->answer->id,
            'question_id' => $this->answer->question_id,
            'ans_user_id' => $this->answer->ans_user_id,
            'ques_user_id' => $this->answer->ques_user_id,
            'item' => 'answer',
            'action' => 'answer_added',
            'data' => "",//$this->answer->answer,
        ];
    }
    
    public function toOneSignal($notifiable)
    {
        $answer = $this->answer;
        $user = $answer->user;
//        $d['title'] = $user->fname . ' ' . $user->lname;
//        $d['image'] = $user->user_img ? url('images/user_img/' . $user->user_img) : null;
//        $length = Str::length($answer->answer);
//        $d['data'] = $length > 50 ? Str::limit($answer->answer,50):$answer->answer;
//        $d['date'] = $n->created_at->diffForHumans();
//        $d['read_at'] = $n->read_at ? date('d-m-Y h:i a', strtotime($n->read_at)) : null;
//        $d['notification_id'] = $n->id;

            return OneSignalMessage::create()
            ->subject('New Answer Added')
            ->body($answer->answer)
            ->setIcon($user->user_img ? url('images/user_img/' . $user->user_img) : '')
            ->setUrl(config("app.front-end-url").'/courses/'.$answer->course_id."?activeTab=3&QuestionID={$answer->question_id}&AnswerId={$answer->id}&course_id={$answer->course_id}")
            ->setImageAttachments('');

        
        
    }
}
