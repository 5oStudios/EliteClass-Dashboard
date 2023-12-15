<?php

namespace App\Http\Controllers\Api;

use App\BBL;
use App\User;
use App\Answer;
use App\Course;
use App\BundleCourse;
use App\CourseChapter;
use App\OfflineSession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller {

    public function allnotification(Request $request) {
        $user = Auth::guard('api')->user();
        $notifications = DatabaseNotification::where('notifiable_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        $notifications->getCollection()->transform(function ($n) {
            $d = $n->data;
            if ($d['action'] && $d['action'] == 'earned_referral') {
                $user = User::find($d['id']);
                $d['deleted'] = $user ? false:true;
                $d['title'] = $user ? $user->fname . ' ' . $user->lname:null;
                $d['image'] = $user ? ($user->user_img ? url('images/user_img/' . $user->user_img) : null):null;
            } elseif ($d['action'] && $d['action'] == 'enrolled') {
                $course = null;
                if ($d['item'] == 'Course') {
                    $course = Course::find($d['id']);
                } elseif ($d['item'] == 'Course Package') {
                    $course = BundleCourse::find($d['id']);
                } elseif ($d['item'] == 'Live Session') {
                    $course = BBL::find($d['id']);
                } elseif ($d['item'] == 'Course Chapter') {
                    $course = CourseChapter::find($d['id']);
                } elseif ($d['item'] == 'Offline Session') {
                    $course = OfflineSession::find($d['id']);
                }
                $d['deleted'] = $course ? false:true;
                $d['title'] =  $course ? $course->_title(): null;
                $d['image'] = $course ? $course->_image():null;
            } elseif ($d['action'] && $d['action'] == 'answer_added') {
                $answer = Answer::find($d['id']);
                if($answer){
                    $user = $answer->user;                    
                }else{
                    $user = isset($d['ans_user_id']) ?  \App\User::find($d['ans_user_id']):null;
                }
                    $d['deleted'] = $answer ? false:true;
                    $d['course_id'] = $answer ? $answer->course_id:null;
                    $d['title'] =  $user ? $user->fname . ' ' . $user->lname:null;
                    $d['image'] = $user ? ($user->user_img ? url('images/user_img/' . $user->user_img) : null):null;
                    $length = $answer ? Str::length($answer->answer):null;
                    $d['data'] = $answer ? ($length > 50 ? Str::limit($answer->answer,50):$answer->answer):null;
               
            }
            // $d['date'] = $n->created_at->diffForHumans();
            // $d['read_at'] = $n->read_at ? date('d-m-Y h:i a', strtotime($n->read_at)) : null;
            $d['deleted'] = isset($d['deleted'])?$d['deleted']:false;
            $d['date'] = $n->created_at;
            $d['read_at'] = $n->read_at ?? null;
            $d['notification_id'] = $n->id;
            return $d;
        });
        $this->readallnotification($request);
        return response()->json($notifications, 200);
    }

    public function notificationread(Request $request, $id) {

        $userunreadnotification = Auth::guard('api')->user()->unreadNotifications->findOrFail($id);

        if ($userunreadnotification) {
            $userunreadnotification->markAsRead();
            return response()->json(array('1'), 200);
        } else {
            return response()->json(array("errors"=>["message"=>[__('error')]]), 401);
        }
    }

    public function readallnotification(Request $request) {


        $notifications = auth()->User()->unreadNotifications()->count();

        if ($notifications > 0) {

            $user = auth()->User();

            foreach ($user->unreadNotifications as $unnotification) {
                $unnotification->markAsRead();
            }

            return response()->json(array('1'), 200);
        } else {
            return response()->json(array("errors"=>["message"=>['Notification already marked as read']]), 401);
        }
    }
    public function onOffNotification(Request $request) {


            $user = auth()->User();
            $user->notifications = !$user->notifications;
            $user->save();
            
            return response()->json(__('notifications').' '.($user->notifications?__('on'):__('off')).' '.__("successfully"), 200);
        
    }

}
