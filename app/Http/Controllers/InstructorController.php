<?php

namespace App\Http\Controllers;

use App\BBL;
use App\User;
use App\Order;
use App\Answer;
use App\Course;
use App\Question;
use Carbon\Carbon;
use App\CompletedPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dashboard.manage', ['only' => ['index']]);
    }

    public function index()
    {
        // return auth()->user()->getRoleNames()->toArray();

        if (Auth::User()->role == "instructor") {
            $userenroll = array(
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '01')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //January
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '02')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //Feb
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '03')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //March
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '04')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //April
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '05')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //May
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '06')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //June
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '07')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //July
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '08')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //August
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '09')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //September
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '10')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //October
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '11')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //November
                Order::query()
                        ->where('instructor_id', Auth::id())
                        ->whereMonth('created_at', '12')
                        ->whereYear('created_at', date('Y'))
                        ->whereHas('user', function ($q) {
                            $q->exceptTestuser();
                        })
                        ->activeOrder()
                        ->count(), //December
            );

            // $completed = array(
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '01')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //January
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '02')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //Feb
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '03')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //March
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '04')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //April
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '05')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //May
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '06')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //June
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '07')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //July
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '08')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //August
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '09')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //September
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '10')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //October
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '11')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //November
            //     CompletedPayout::where('user_id', Auth::id())->whereMonth('created_at', '12')->where('pay_status', '1')
            //         ->whereYear('created_at', date('Y'))
            //         ->count(), //December
            // );

            // User::select(DB::raw("COUNT(*) as count"))
            // ->where('created_at', '>', Carbon::today()->subDays(6))
            // ->groupBy(DB::raw("Date(created_at)"))
            // ->pluck('count');

            // $users =   CompletedPayout::select(DB::raw("COUNT(*) as count"))
            // ->whereYear('created_at',date('Y'))
            // ->groupBy(DB::raw("Month(created_at)"))
            // ->pluck('count');

            // $months =  CompletedPayout::select(DB::raw("Month(created_at) as month"))
            //         ->whereYear('created_at',date('Y'))
            //         ->groupBy(DB::raw("Month(created_at)"))
            //         ->pluck('month');

            // $datas = [0,0,0,0,0,0,0,0,0,0,0,0];
            // foreach($months as $index => $month)
            // {
            //     $datas[$month-1] = $users[$index];
            // }

            $users =    Order::select(DB::raw("COUNT(*) as count"))
                                ->whereYear('created_at', date('Y'))
                                ->whereHas('user', function ($q) {
                                    $q->exceptTestuser();
                                })
                                ->activeOrder()
                                ->groupBy(DB::raw("Month(created_at)"))
                                ->pluck('count');

            $months =   Order::select(DB::raw("Month(created_at) as month"))
                                ->whereYear('created_at', date('Y'))
                                ->whereHas('user', function ($q) {
                                    $q->exceptTestuser();
                                })
                                ->activeOrder()
                                ->groupBy(DB::raw("Month(created_at)"))
                                ->pluck('month');

            $datas1 = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach ($months as $index => $month) {
                $datas1[$month - 1] = $users[$index];
            }

            $course_count = Course::where('user_id', Auth::id())->active()->count();
            $student_count = Order::query()
                            ->where('instructor_id', Auth::id())
                            ->whereNotNull('course_id')
                            ->whereHas('user', function ($q) {
                                $q->exceptTestuser();
                            })
                            ->activeOrder()
                            ->count();
            $question_count = Question::where('instructor_id', Auth::id())->count();
            $answer_count = Answer::query()
                                ->where('instructor_id', Auth::id())
                                ->count();
            $meeting_count = BBL::query()
                            ->where('instructor_id', Auth::id())
                            ->active()
                            ->count();


            // return view('instructor.dashboard', compact('userEnrolled', 'course_count', 'student_count', 'question_count', 'answer_count', 'meeting_count', 'payout','datas','datas1'));
            return view('instructor.dashboard', compact('course_count', 'student_count', 'question_count', 'answer_count', 'meeting_count', 'datas1'));
        } else {
            return back()->with('success', trans('flash.NotFound'));
        }
    }
}
