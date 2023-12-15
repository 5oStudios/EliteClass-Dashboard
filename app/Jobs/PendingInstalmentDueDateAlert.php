<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Notifications\PendingInstalmentDueDateNotification;

class PendingInstalmentDueDateAlert implements ShouldQueue
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
            
            $inst = \App\OrderPaymentPlan::where('due_date',date('Y-m-d',strtotime('tomorrow')))->get();

            foreach($inst as $p){
                if(count($p->user->device_tokens) > 0 && $p->user->notifications){
                    Notification::send($p->user,new PendingInstalmentDueDateNotification($p));
                }
            }
        }
    }
}
