<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BeforeLiveSessionStart;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpcomingLiveStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1'){
        
            $meetings = \App\BBL::whereRaw("cast(start_time as date) = date(now()) and timestampdiff(minute,now(),cast(start_time as datetime)) = 5")->get();

            foreach($meetings as $p){
                if(count($p->attendee()->device_tokens) > 0 && $p->attendee()->notifications){
                    Notification::send($p->attendee(),new BeforeLiveSessionStart($p));
                }
            }
        }
    }
}
