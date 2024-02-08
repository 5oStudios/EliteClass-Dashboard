<?php

namespace App\Http\Controllers\Api;

use DB;
use Hash;
use Mail;
use App\BBL;
use App\Cart;
use App\User;
use App\Order;
use App\Answer;
use App\Coupon;
use App\Course;
use App\Setting;
use App\Currency;
use App\Question;
use App\QuizTopic;
use App\QuizAnswer;
use App\CourseClass;
use App\BundleCourse;
use App\ReviewRating;
use App\CourseChapter;
use App\ReviewHelpful;
use App\CourseProgress;
use App\CoursesInBundle;
use App\Helpers\Is_wishlist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AnswerAddedNotification;

class CourseController extends Controller
{

    public function getAllchaptersWithLessons(Request $request)
    {

        if ($request->is_bundle === true || $request->is_bundle === 'true') {
            $request->validate([
                'course_id' => 'required'
            ], [
                'course_id.required' => __('Course not selected'),
            ]);

            $exist = BundleCourse::whereJsonContains('course_id', strval($request->course_id))->where('status', '1')->where('end_date', '>=', date('Y-m-d'))->exists();

            if (!$exist) {
                return response()->json(['errors' => ['message' => [__('Course not exist OR may have been disabled')]]], 422);
            }

        } else {
            $request->validate([
                'course_id' => [
                    'required',
                    Rule::exists('courses', 'id')->where(function ($query) {
                        return $query->where('status', '1')
                            ->where('end_date', '>=', date('Y-m-d'));
                    })
                ],
            ], [
                'course_id.required' => __("Course not selected"),
                'course_id.exists' => __('Course not exist OR may have been disabled'),
            ]);
        }

        $orders = collect();
        $is_chapter_carted = NULL;

        $user = Auth::guard('api')->user();
        $course = Course::find($request->course_id);

        if ($user) {
            $orders = Order::where('user_id', $user->id)
                ->where('enroll_expire', '>=', date('Y-m-d'))
                // ->where('course_id',$course->id)
                ->activeOrder()
                ->get();

            // Check user course progress
            $progress = CourseProgress::where('course_id', $course->id)->where('user_id', $user->id)->activeProgress()->first();

            $course_order = [];
            $course_order = $orders->where('course_id', $course->id);

            foreach ($orders as $order) {
                $exist = in_array($course->id, $order->bundle_course_id ?? []) ? $order : null;
                if ($exist) {
                    $course_order[] = $order;
                }
            }

            $order = $course_order->first(function ($item) {
                return $item['total_amount'] == ($item['paid_amount'] + $item['coupon_discount']);
            });

            if (!$order) {
                $order = $course_order->sortBy([['paid_amount', 'desc']])->first();
            }

            if ($order) {
                if ($order->total_amount == ($order->paid_amount + $order->coupon_discount)) {
                    $un_lock = 4;
                } elseif ($order->installments && (($order->paid_amount + $order->coupon_discount) > 0)) {
                    $paid = $order->paid_amount + $order->coupon_discount;
                    $p_inst = $order->installments_list ? $order->installments_list->count() : 0;
                    $un_lock = 0;
                    foreach ($order->payment_plan as $i) {
                        if ($paid > 0) {
                            $un_lock++;
                        }
                        $paid -= $i->amount;
                    }
                    if ($p_inst > $un_lock) {
                        $un_lock = $p_inst;
                    }
                } else {
                    $un_lock = 0;
                }
            } else {
                $un_lock = 0;
            }

            // Check any chapter of course is added to cart
            $usercart = Cart::select('chapter_id')->where('user_id', $user->id)->whereNotNull('chapter_id')->pluck('chapter_id')->toArray();
            foreach ($usercart as $chapter_id) {
                if (in_array($chapter_id, $course->chapter()->pluck('id')->toArray())) {
                    $is_chapter_carted = true;
                }
            }

        } else {
            $un_lock = 0;
        }

        $chapter_order = [];
        $chapters = [];

        foreach ($course->chapter as $chapter) {

            //Check session chapter exist and not being expired IF(Session expired than it will show on chapters list) ELSE (skip this session chapter from chapters list)
            // if ($chapter->type && !$chapter->session) {
            //     continue;

            // } else
            if ($chapter->type && $chapter->session) {
                if ((($un_lock > 0 && $chapter->unlock_installment <= $un_lock) ? false : ($chapter_order ? false : true)) == true) {

                    continue;
                }
            }

            $classes = $chapter->courseclass;
            $chapter_order = $orders->where('chapter_id', $chapter->id)->toArray();

            $time = 0;
            foreach ($classes as $c) {
                $time += $c->type == 'quiz' ? $c->quiz->timer : ($c->type == 'meeting' ? $c->meeting->duration : $c->duration);
            }

            // $chapterlessons = CourseClass::where('coursechapter_id', $chapter->id)->where('status', 1)->orderByRaw('position asc')->get();
            $lessons = [];
            foreach ($chapter->courseclass as $c) {
                $lessons[] = [
                    'class_id' => $c->id,
                    'type_id' => $c->type == 'quiz' ? $c->url : ($c->type == 'meeting' ? $c->meeting_id : ($c->type == 'offline_session' ? $c->offline_session_id : null)),
                    // 'quiz_id' => $c->type == 'quiz' ? $c->url : null,
                    'meeting_id' => $c->type == 'Meeting' ? $c->meeting_id : null,
                    // 'offline_session_id' => $c->type == 'offline_session' ? $c->offline_session_id : null,
                    'type' => ($c->type == 'pdf' || $c->type == 'zip' || $c->type == 'rar' || $c->type == 'word' || $c->type == 'excel' || $c->type == 'powerpoint') ? pathinfo($c->file, PATHINFO_EXTENSION) : $c->type,
                    'iframe_url' => $c->type == 'video' ? $c->iframe_url ?? NULL : NULL,
                    'video_url' => $c->type == 'video' ? $c->video_url ?? NULL : NULL,
                    "title" => $c->type == 'quiz' ? $c->quiz->title : ($c->type == 'meeting' ? $c->meeting->meetingname : $c->title),
                    "duration" => $c->type == 'quiz' ? ($c->quiz->timer ?? 0) : ($c->type == 'meeting' ? $c->meeting->duration : ($c->type == 'offline_session' ? $c->offlinesession->duration : $c->duration)),
                    "is_complete" => isset($progress) && in_array($c->id, $progress->mark_chapter_id) ? true : false,

                ];
            }

            $chapters[] = [
                'id' => $chapter->id,
                'name' => $chapter->chapter_name,
                'type' => $chapter->type,
                'type_id' => $chapter->type_id,
                'total_time' => $time,
                'is_purchasable' => $chapter->is_purchasable == '1' ? true : false,
                "is_lock" => ($un_lock > 0 && $chapter->unlock_installment <= $un_lock) ? false : ($chapter_order ? false : true),
                'is_cart' => $user ? ($user->cartType('chapter', $chapter->id)->exists() ? true : false) : false,
                'video' => $classes ? $classes->where('type', 'video')->count() : 0,
                'pdf' => $classes ? $classes->where('type', 'pdf')->count() : 0,
                'text' => $classes ? $classes->where('type', 'text')->count() : 0,
                'quiz' => $classes ? $classes->where('type', 'quiz')->count() : 0,
                'meetings' => $classes ? $classes->where('type', 'meeting')->count() : 0,
                'offline_sessions' => $classes ? $classes->where('type', 'offline_session')->count() : 0,
                'price' => $chapter->price ?? 0,
                'chapter_lessons' => $lessons,
            ];
        }

        $resp = [
            'course_id' => $course->id,
            'name' => $course->title,
            'instructor_name' => $course->user->fname . ' ' . $course->user->lname,
            'price' => $course->price,
            'discount_price' => $course->discount_price,
            'is_chapter_carted' => $is_chapter_carted ?? false,
            'is_cart' => $user ? ($user->cartType('course', $course->id)->exists() ? true : false) : false,
            'introduction' => [
                'title' => 'Introduction',
                'video_url' => $course->video_url,
                'iframe_url' => $course->iframe_url,
                'duration' => $course->duration,
            ],
            'chapters' => $chapters,
        ];

        return response()->json($resp, 200);
    }

    // It was being used before now its abandoned
    public function getAllchapter(Request $request)
    {

        $this->validate($request, [
            'course_id' => 'required|exists:course_chapters,course_id',
        ]);

        $coursechapter = CourseChapter::where('course_id', $request->course_id)->where('status', 1)->get();

        $result = array();
        $progress = false;
        if (Auth::guard('api')->check()) {
            $progress = CourseProgress::where('course_id', $request->course_id)->where('user_id', Auth::guard('api')->user()->id)->activeProgress()->first();
            $chap_comp = true;
        } else {
            $chap_comp = false;
        }
        foreach ($coursechapter as $data) {
            $classes = $data->courseclass;
            $time = 0;
            $comp = true;
            foreach ($classes as $c) {
                $time += $c->type == 'quiz' ? $c->quiz->timer : ($c->type == 'meeting' ? $c->meeting->duration : $c->duration);
                if ($chap_comp && $progress && $comp) {
                    $comp = in_array($c->id, $progress->mark_chapter_id) ? true : false;
                }
            }
            if ($chap_comp && !$comp) {
                $chap_comp = false;
            }
            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'chapter_name' => $data->chapter_name,
                'total_time' => $time,
                'is_complete' => $chap_comp,
                'video' => $classes ? $classes->where('type', 'video')->count() : 0,
                'pdf' => $classes ? $classes->where('type', 'pdf')->count() : 0,
                'text' => $classes ? $classes->where('type', 'text')->count() : 0,
                'quiz' => $classes ? $classes->where('type', 'quiz')->count() : 0,
                'meetings' => $classes ? $classes->where('type', 'meeting')->count() : 0,
            );
        }

        return response()->json(array('data' => $result), 200);
    }

    // It was being used before now its abandoned
    public function Lessons(Request $request)
    {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
            'chapter_id' => ['required', Rule::exists('course_chapters', 'id')->where('status', '1')],
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
            'chapter_id.required' => __("Chapter not selected"),
            'chapter_id.exists' => __("Chapter not exist OR may have been disabled"),
        ]);

        $coursechapter = CourseChapter::find($request->chapter_id);
        if (Auth::guard('api')->check()) {

            $course = Course::find($coursechapter->course_id);

            $total_inst = $course->total_installments;
            $user_id = Auth::guard('api')->user()->id;

            $orders = Order::where('user_id', $user_id)
                ->where('enroll_expire', '>=', date('Y-m-d'))
                ->activeOrder()
                // ->whereRaw(" ((find_in_set( ? ,REPLACE(REPLACE(REPLACE(bundle_course_id,'[','') ,'\"', ''), ']','')) or (course_id = ?))", [$request->course_id, $request->course_id])
                ->get();

            $progress = CourseProgress::where('course_id', $course->id)->where('user_id', $user_id)->activeProgress()->first();

            $f_order = [];
            $f_order = $orders->where('course_id', $coursechapter->course_id);

            foreach ($orders as $o) {
                $f = in_array($coursechapter->course_id, $o->bundle_course_id ?? []) ? $o : null;
                if ($f) {
                    $f_order[] = $o;
                }
            }

            $order = $f_order->first(function ($item) {
                return $item['total_amount'] == ($item['paid_amount'] + $item['coupon_discount']);
            });

            if (!$order) {
                $order = $f_order->sortBy([['paid_amount', 'desc']])->first();
            }

            if ($order) {
                if ($order->total_amount == ($order->paid_amount + $order->coupon_discount)) {
                    $un_lock = 3;
                } elseif ($order->installments && ($order->paid_amount > 0)) {
                    $paid = $order->paid_amount;
                    $p_inst = $order->installments_list ? $order->installments_list->count() : 0;
                    $un_lock = 0;
                    foreach ($order->payment_plan as $i) {
                        if ($paid > 0) {
                            $un_lock++;
                        }
                        $paid -= $i->amount;
                    }
                    if ($p_inst > $un_lock) {
                        $un_lock = $p_inst;
                    }
                } else {
                    $un_lock = 0;
                }
            } else {
                $un_lock = 0;
            }
        } else {
            $un_lock = 0;
        }
        $classes = CourseClass::where('coursechapter_id', $coursechapter->id)->where('status', 1)->orderByRaw('position asc')->get();
        $data = [];
        foreach ($classes as $c) {
            $data[] = [
                'class_id' => $c->id,
                'quiz_id' => $c->type == 'quiz' ? $c->url : null,
                'meeting_id' => $c->type == 'meeting' ? $c->meeting_id : null,
                'type' => $c->type,
                "title" => $c->type == 'quiz' ? $c->quiz->title : ($c->type == 'meeting' ? $c->meeting->meetingname : $c->title),
                "duration" => $c->type == 'quiz' ? ($c->quiz->quizquestion->count() ?? 0) : ($c->type == 'meeting' ? $c->meeting->duration : $c->duration),
                "is_lock" => ($un_lock > 0 && $c->unlock_installment <= $un_lock) ? false : true,
                "is_complete" => isset($progress) && in_array($c->id, $progress->mark_chapter_id) ? true : false,
            ];
        }

        return [
            "data" => $data,
            "total" => $classes->count(),
            "current_page" => 1,
            "per_page" => $classes->count(),
            "next_page" => false,
            "previous_page" => false,
        ];
    }

    public function lessonContent(Request $request)
    {

        $this->validate($request, [
            'class_id' => ['nullable', Rule::exists('course_classes', 'id')->where('status', 1)->whereNull('deleted_at')],
        ], [
            'class_id.exists' => __("class not exist OR may have been disabled"),
        ]);

        $user_id = Auth::guard('api')->user()->id;
        $classes = CourseClass::find($request->class_id);

        $orders = Order::where('user_id', $user_id)
            ->where([['enroll_start', '<=', date('Y-m-d')], ['enroll_expire', '>=', date('Y-m-d')]])
            ->activeOrder()
            ->get();

        $f_order = [];
        $chapter_order = [];
        $f_order = $orders->where('course_id', $classes->course_id);

        foreach ($orders as $o) {
            $f = in_array($classes->course_id, $o->bundle_course_id ?? []) ? $o : null;
            if ($f) {
                $f_order[] = $o;
            }
        }

        $order = $f_order->first(function ($item) {
            return $item['total_amount'] == ($item['paid_amount'] + $item['coupon_discount']);
        });

        if (!$order) {
            $order = $f_order->sortBy([['paid_amount', 'desc']])->first();
        }

        if ($order) {
            if ($order->total_amount == ($order->paid_amount + $order->coupon_discount)) {
                $un_lock = 4;
            } elseif ($order->installments && (($order->paid_amount + $order->coupon_discount) > 0)) {
                $paid = $order->paid_amount + $order->coupon_discount;
                $p_inst = $order->installments_list ? $order->installments_list->count() : 0;
                $un_lock = 0;
                foreach ($order->payment_plan as $i) {
                    if ($paid > 0) {
                        $un_lock++;
                    }
                    $paid -= $i->amount;
                }
                if ($p_inst > $un_lock) {
                    $un_lock = $p_inst;
                }
            } else {
                return response()->json(array("errors" => ["message" => [__("instalment not paid or course not started")]]), 422);
            }
        } else {

            $chapter_order = $orders->where('chapter_id', $classes->coursechapter_id)->toArray();

            if (!$chapter_order) {
                return response()->json(array("errors" => ["message" => [__("chapter not bought or not started")]]), 422);
            }
        }

        if (($order && $un_lock > 0 && $classes->coursechapters->unlock_installment <= $un_lock) || $chapter_order) {
            return [
                "class_id" => $classes->id,
                "title" => $classes->type == 'quiz' ? $classes->quiz->title : ($classes->type == 'meeting' ? $classes->meeting->meetingname : $classes->title),
                "duration" => $classes->type == 'quiz' ? $classes->quiz->timer : ($classes->type == 'meeting' ? $classes->meeting->duration : $classes->duration),
                'type' => ($classes->type == 'pdf' || $classes->type == 'zip' || $classes->type == 'rar' || $classes->type == 'word' || $classes->type == 'excel' || $classes->type == 'powerpoint') ? pathinfo($classes->file, PATHINFO_EXTENSION) : $classes->type,
                'content' => ($classes->meeting_id ? $classes->bbmeeting() : null) ??
                    (($classes->type == 'pdf' || $classes->type == 'zip' || $classes->type == 'rar' || $classes->type == 'word' || $classes->type == 'excel' || $classes->type == 'powerpoint') ? "courseclass/file/$classes->id/url" : NULL) ??
                    NULL,
                'is_downloadable' => $classes->downloadable == '1' ? true : false,
                'download_link' => $classes->downloadable == '1' ? "courseclass/file/$classes->id/download" : null,
                'is_printable' => $classes->printable == '1' ? true : false,
                "preview_video" => ($classes->preview_video ? asset('video/class/preview/' . $classes->preview_video) : null) ?? $classes->preview_url,
                "preview_type" => $classes->preview_type,
                "date_time" => $classes->date_time,
                "detail" => $classes->detail,
                "long_text" => $classes->long_text,
                "aws_upload" => $classes->aws_upload,
                "file" => $classes->file ? asset('files/class/material/' . $classes->file) : null,
            ];

        } else {

            return response()->json(array("errors" => ["message" => [__("you did not buy this chapter")]]), 422);
        }

    }

    public function previewFileURL($id)
    {
        $courseclass = CourseClass::findOrFail($id);
        $pathToFile = "/files/$courseclass->type/" . $courseclass->file;

        if (Storage::exists($pathToFile)) {
            $disk = Storage::disk('local');
            return $disk->temporaryUrl($pathToFile, now()->addMinutes(5));
        }
    }

    public function previewFile()
    {
        // abort_unless(request()->hasValidSignature(), 404);
        // return Storage::get('public/classOne.pdf');

        if (request()->has('path') && Storage::exists(request()->query('path'))) {
            ob_end_clean();
            ob_start();

            $filename = urldecode(pathinfo(request()->query('path'), PATHINFO_BASENAME));
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            if ($extension == 'pdf') {
                return Storage::get(request()->query('path'));
            } else {
                return Storage::download(request()->query('path'));
            }

        } else {
            abort(404);
        }
    }

    public function allowFileDownloadOrPrint(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:course_classes,id',
            'type' => 'required|in:download,print',
        ], [
            "class_id.required" => __("Class ID is required"),
            "course_id.exists" => __("Class not exist OR may have been disabled"),
            "type.required" => __("Type is required"),
            "type.in" => __("Type should be download OR print only"),
        ]);

        $courseClass = CourseClass::find($request->class_id);

        $now = now();
        $user = auth()->user();

        if ($request->type == 'download') {

            if ($courseClass->downloadable === '1') {

                $queryParams = "user_id=$user->id&time=$now";
                $expiree = encryptData($queryParams);
                return response()->json(['URL' => env('API_URL') . "/courseclass/file/$request->class_id/download?key=$expiree"]);
            }

            return response()->json(['errors' => ['message' => [__('File download, not allowed')]]], 422);

        } else if ($request->type == 'print') {

            if ($courseClass->printable === '1') {

                $queryParams = "user_id=$user->id&time=$now";
                $expiree = encryptData($queryParams);
                return response()->json(['URL' => env('API_URL') . "/courseclass/file/$request->class_id/print?key=$expiree"]);
            }

            return response()->json(['errors' => ['message' => [__('File print, not allowed')]]], 422);
        }

    }

    public function printFile($id)
    {
        $courseclass = CourseClass::find($id);
        $pathToFile = "/files/$courseclass->type/" . $courseclass->file;

        if ($courseclass->printable == '1') {
            if (Storage::exists($pathToFile)) {
                ob_end_clean();
                ob_start();

                return Storage::get($pathToFile);
                // return response()->download(storage_path('app/'.$pathToFile));
            }

        } else {
            abort('403');
        }

        abort('404');
    }

    public function downloadFile($id)
    {
        $courseclass = CourseClass::find($id);
        $pathToFile = "/files/$courseclass->type/" . $courseclass->file;

        if ($courseclass->downloadable == '1') {
            if (Storage::exists($pathToFile)) {

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                return response()->download(Storage::path($pathToFile), null, $headers);
            }

        } else {
            abort('403');
        }

        abort('404');
    }


    public function question(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required|exists:courses,id',
            'question' => 'required|min:10|max:300',
        ], [
            'question.required' => __("question is empty"),
            'question.min' => __("question minimum chracters length is 10"),
            'question.max' => __("question maximum chracters length is 300"),
            'course_id.required' => __("course not selected"),
            'course_id.exists' => __("course not found"),
        ]);

        $auth = Auth::guard('api')->user();

        $course = Course::where('id', $request->course_id)->first();

        $question = Question::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'status' => 1,
            'question' => $request->question,
        ]);

        return response()->json(array('question' => $question), 200);
    }

    public function DeleteQuestion(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $this->validate($request, [
            'question_id' => ['required', Rule::exists('questions', 'id')->where('user_id', $auth->id)],
        ], [
            'question_id.required' => __("question not selected to delete"),
            'question_id.exists' => __("you can not delete this question"),
        ]);

        Answer::where(['question_id' => $request->question_id])->delete();
        Question::where(['id' => $request->question_id])->delete();
        return response()->json(array('message' => __('Question Deleted')), 200);
    }

    public function DeleteAnswer(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $this->validate($request, [
            'answer_id' => ['required', Rule::exists('answers', 'id')->where('ans_user_id', $auth->id)],
        ], [
            'answer_id.required' => __("answer not selected to delete"),
            'answer_id.exists' => __("you can not delete this Answer"),
        ]);

        $ans = Answer::find($request->answer_id);
        $question_id = $ans->question_id;
        $ans->delete();
        $ans_count = Answer::where(['question_id' => $question_id])->count();
        return response()->json(array('message' => __('Answer Deleted'), 'total_answer' => $ans_count), 200);
    }

    public function deleteReview(Request $request)
    {
        $auth = Auth::guard('api')->user();
        $this->validate($request, [
            'review_id' => ['required', Rule::exists('review_ratings', 'id')->where('user_id', $auth->id)],
        ], [
            'review_id.required' => __("Review not selected to delete"),
            'review_id.exists' => __("you can not delete these Reviews"),
        ]);

        $ans = ReviewRating::find($request->review_id);
        $ans->delete();
        return response()->json(array('message' => __('Reviews Deleted')), 200);
    }

    public function answer(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required|exists:courses,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|min:2|max:300',
        ], [
            'course_id.required' => __("course not selected"),
            'course_id.exists' => __("course not found"),
            'question_id.required' => __("question not selected"),
            'question_id.exists' => __("question removed"),
            'answer.required' => __("answer is empty"),
            'answer.min' => __("answer minimum chracters length is 2"),
            'answer.max' => __("answer maximum chracters length is 300"),
        ]);

        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        $question = Question::where('id', $request->question_id)->first();

        $ans = Answer::create([
            'ans_user_id' => $auth->id,
            'ques_user_id' => $question->user_id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'question_id' => $question->id,
            'status' => 1,
            'answer' => $request->answer,
        ]);

        if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1') {

            if ($question->user_id != $auth->id && count($question->user->device_tokens) > 0) {
                Notification::send($question->user, new AnswerAddedNotification($ans));
            }
        }

        return response()->json(array('message' => __('Answer Submitted'), 'total_answer' => $question->answers->count() ?? 0), 200);
    }

    public function CourseQuestions(Request $r)
    {
        $this->validate($r, [
            'course_id' => 'required|exists:courses,id',
        ], [
            'course_id.required' => __("course not selected"),
            'course_id.exists' => __("course has been deleted"),
        ]);

        $questions = Question::
            where('course_id', $r->course_id)
            ->where('status', 1)
            ->latest()
            ->paginate(10);

        $question = array();

        $questions->getCollection()->transform(function ($ques) {
            $roles = $ques->user->getRoleNames();
            return [
                'question_id' => $ques->id,
                'user' => $ques->user->fname . ' ' . $ques->user->lname,
                'user_id' => $ques->user->id,
                'role' => count($roles) ? ucfirst($roles[0]) : "User",
                'imagepath' => $ques->user->user_img ? url('images/user_img/' . $ques->user->user_img) : null,
                'question' => strip_tags($ques->question),
                'created_at' => $ques->created_at->diffForHumans(),
                'timestamp' => $ques->created_at,
                'answer' => $ques->answers->count() ?? 0,
            ];
        });
        return $questions;
        $page = $questions->currentPage();
        return [
            "data" => $question,
            "total" => $questions->total(),
            "current_page" => $questions->currentPage(),
            "per_page" => $questions->perPage(),
            "next_page" => $questions->hasMorePages() ? ++$page : false,
            "previous_page" => $questions->currentPage() > 1 ? --$page : false,
        ];
    }

    public function answers(Request $r)
    {
        $this->validate($r, [
            'question_id' => 'required',
        ], [
            'question_id.required' => __("question not selected"),
        ]);

        $questions = Answer::
            where('question_id', $r->question_id)
            ->where('status', 1)
            ->latest()
            ->paginate(10);

        $question = array();

        $questions->getCollection()->transform(function ($ques) use ($r) {
            $roles = $ques->user->getRoleNames();
            return [
                'question_id' => $r->question_id,
                'answer_id' => $ques->id,
                'user' => $ques->user->fname . ' ' . $ques->user->lname,
                'role' => count($roles) ? ucfirst($roles[0]) : "User",
                'user_id' => $ques->user->id,
                'imagepath' => $ques->user->user_img ? url('images/user_img/' . $ques->user->user_img) : null,
                'answer' => strip_tags($ques->answer),
                'created_at' => $ques->created_at->diffForHumans(),
            ];
        });
        return $questions;
        $page = $questions->currentPage();
        return [
            "data" => $question,
            "total" => $questions->total(),
            "current_page" => $questions->currentPage(),
            "per_page" => $questions->perPage(),
            "next_page" => $questions->hasMorePages() ? ++$page : false,
            "previous_page" => $questions->currentPage() > 1 ? --$page : false,
        ];
    }

    public function discovercourse(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required|in:newest,popular,recommended'
        ], [
            'filter.required' => __("Filetr not selected"),
            'filter.in' => __("Filter not valid"),
        ]);

        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }


        if ($request->filter == 'newest') {

            $courses = Course::where([
                'category_id' => $category_id,
                'scnd_category_id' => $scnd_category_id,
                'subcategory_id' => $sub_category_id,
                'childcategory_id' => $child_sub_category,
                'status' => true
            ])
                ->whereDoesntHave('progress')
                ->where('end_date', '>=', date('Y-m-d'))
                ->latest()
                ->paginate(10);
        } elseif ($request->filter == 'popular') {

            $courses = Course::select('courses.*')->where([
                'category_id' => $category_id,
                'scnd_category_id' => $scnd_category_id,
                'subcategory_id' => $sub_category_id,
                'childcategory_id' => $child_sub_category,
            ])
                ->whereDoesntHave('progress')
                ->where('end_date', '>=', date('Y-m-d'))
                ->join('orders', 'orders.course_id', '=', 'courses.id')
                ->groupBy('orders.course_id')
                ->paginate(10);
        } elseif ($request->filter == 'recommended') {

            $courses = Course::where([
                'category_id' => $category_id,
                'scnd_category_id' => $scnd_category_id,
                'subcategory_id' => $sub_category_id,
                'childcategory_id' => $child_sub_category,
                'status' => true
            ])
                ->whereDoesntHave('progress')
                ->where('end_date', '>=', date('Y-m-d'))
                ->paginate(10);
        }

        $courses->getCollection()->transform(function ($c) {
            $data = [
                'id' => $c->id,
                'title' => $c->title,
                'image' => url('/images/course/' . $c->preview_image),
                'instructor' => $c->user->fname . ' ' . $c->user->lname,
                'lessons' => $c->courseclass->count(),
                'rating' => round($c->review->avg('avg_rating'), 2),
                'reviews_by' => $c->review->count() ?? 0
            ];
            return $data;
        });

        if ($user) {
            return response()->json($courses, 200);
        } else {
            return response()->json(array("errors" => ["message" => ["you are not login"]]), 401);
        }
    }

    public function inprogresscourse(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required'
        ], [
            'filter.required' => __("Filter not selected"),
            'filter.in' => __("Filter not valid"),
        ]);

        $pro = [];
        $user = Auth::user();

        // $orders = Order::whereRaw("(chapter_id is not null or course_id is not null or bundle_course_id is not null)")->where('user_id', $user->id)
        $orders = Order::where('user_id', $user->id)
            ->where('enroll_expire', '>=', date('Y-m-d'))
            ->activeOrder()
            ->get();

        foreach ($orders as $o) {
            if ($o->course_id) {
                $pro = CourseProgress::where(['course_id' => $o->course_id, 'user_id' => $user->id])->activeProgress()->get();
            } elseif ($o->chapter_id) {
                $pro = CourseProgress::where(['course_id' => $o->chapter->course_id, 'user_id' => $user->id])->activeProgress()->get();
            } elseif ($o->bundle_id) {
                $pro = CourseProgress::whereIn('course_id', $o->bundle_course_id)->where(['user_id' => $user->id])->activeProgress()->get();
            }

            foreach ($pro as $p) {
                if (!$p->end_date || $p->end_date < $o->enroll_expire) {
                    $p->start_date = $o->enroll_start;
                    $p->end_date = $o->enroll_expire;
                    $p->save();
                }
            }
        }
        $order_by = $request->filter == 'newest' ? 'created_at' : 'updated_at';
        $sort = $request->filter == 'newest' ? 'desc' : 'desc';

        $enroll = CourseProgress::where(['user_id' => $user->id, ['end_date', '>=', date('Y-m-d')]]) //TODO: Remove check on progress itself - This is also supposed to be fixed
            ->whereHas('courses', function ($query) {
                $query->active();
            })
            ->activeProgress()
            ->orderBy($order_by, $sort)
            ->paginate(10);

        $enroll->getCollection()->transform(function ($p) {
            $completed = [
                'course_id' => $p->courses->id,
                'title' => $p->courses->title,
                'image' => url('/images/course/' . $p->courses->preview_image),
                'progress' => $p->progress > 100 ? 100 : $p->progress     // TODO: @NoumanSarwar fix this please, this is a temporary patch...
            ];
            return $completed;
        });
        return response()->json($enroll, 200);
    }

    public function completedcourse(Request $request)
    {
        $this->validate($request, [
            'filter' => 'required'
        ], [
            'filter.required' => __("Filetr not selected"),
            'filter.in' => __("Filter not valid"),
        ]);

        $user = Auth::guard('api')->user();

        $order_by = $request->filter == 'newest' ? 'created_at' : 'updated_at';
        $sort = $request->filter == 'newest' ? 'desc' : 'desc';

        $enroll = CourseProgress::where(['user_id' => $user->id, 'progress' => 100])
            ->orderBy($order_by, $sort)
            ->paginate(10);

        $enroll->getCollection()->transform(function ($p) {
            $completed = [
                'course_id' => $p->courses->id,
                'title' => $p->courses->title,
                'image' => url('/images/course/' . $p->courses->preview_image),
                'progress' => 100
            ];
            return $completed;
        });
        return response()->json($enroll, 200);
    }

}
