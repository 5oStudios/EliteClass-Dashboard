<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EnrollExpire implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        //Get User Wallet
        $todayDate = date('Y-m-d');
        $user = Auth::guard('api')->user();

        if ($user) {
            foreach ($user->orders as $order) {
                if ($order->status) {
                    if ($order->enroll_expire != null && $order->enroll_expire != '') {
                        if ($todayDate >= date('Y-m-d', strtotime($order->enroll_expire))) {
                            DB::table('orders')->where('enroll_expire', '<', $todayDate)->delete();
                        }
                    }
                }
            }
        }
    }
}
