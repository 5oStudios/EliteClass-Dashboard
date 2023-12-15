<?php

namespace App\Jobs;

use App\BBL;
use App\Wishlist;
use App\OfflineSession;
use App\SessionEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class EnrolledUserOnExpiredSession implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

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
        // Get only linked with course Live streaming and order of those courses with enrollments i.e (the users already enrolled in this session after purchasing course)
        $meetings = BBL::query()
                    ->where('expire_date', '<', date('Y-m-d'))
                    ->whereNotNull('course_id')
                    ->whereHas('course', function ($query) {
                        $query->whereHas('order');
                    })
                    ->with('course', function ($query) {
                        $query->select('id', 'title')
                                ->with('order:id,course_id,user_id');
                    })
                    ->with('enrollments')
                    // ->groupBy('course_id')
                    ->get();


        foreach ($meetings as $meeting) {
            $course_orders = [];

            // Get course order ID in ascending
            foreach ($meeting->course->order as $order) {
                $course_orders[] = $order->user_id;
                sort($course_orders);
            }

            // Filters orders id that user already enrolled
            foreach ($course_orders as $user_id) {
                foreach ($meeting->enrollments as $enrolled) {
                    if ($user_id == $enrolled->user_id) {
                        $key = array_search($user_id, $course_orders);
                        unset($course_orders[$key]);

                        break;
                    }
                }
            }

            // Enrolled users into sessions that did not enroll and course got expired
            foreach (array_values($course_orders) as $userid) {
                SessionEnrollment::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $userid,
                    'status' => '1',
                ]);

                Wishlist::where(['meeting_id' => $meeting->id,'user_id' => $userid])->delete();
                BBL::find($meeting->id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order
            }
        }


        // Get only linked with course In-Person session and order of those courses with enrollments i.e (the users already enrolled in this session after purchasing course)
        $sessions = OfflineSession::query()
                    ->where('expire_date', '<', date('Y-m-d'))
                    ->whereNotNull('course_id')
                    ->whereHas('course', function ($query) {
                        $query->whereHas('order');
                    })
                    ->with('course', function ($query) {
                        $query->select('id', 'title')
                                ->with('order:id,course_id,user_id');
                    })
                    ->with('enrollments')
                    // ->groupBy('course_id')
                    ->get();

        foreach ($sessions as $session) {
            $course_orders = [];

            // Get course order ID in ascending
            foreach ($session->course->order as $order) {
                $course_orders[] = $order->user_id;
                sort($course_orders);
            }

            // Filters orders id that user already enrolled
            foreach ($course_orders as $user_id) {
                foreach ($session->enrollments as $enrolled) {
                    if ($user_id == $enrolled->user_id) {
                        $key = array_search($user_id, $course_orders);
                        unset($course_orders[$key]);

                        break;
                    }
                }
            }

            // Enrolled users into sessions that did not enroll and course got expired
            foreach (array_values($course_orders) as $userid) {
                SessionEnrollment::create([
                    'offline_session_id' => $session->id,
                    'user_id' => $userid,
                    'status' => '1',
                ]);

                Wishlist::where(['offline_session_id' => $session->id,'user_id' => $userid])->delete();
                OfflineSession::find($session->id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order
            }
        }
    }
}
