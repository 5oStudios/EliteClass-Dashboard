<?php

namespace App\Http\Controllers;

use Cookie;
use App\BBL;
use App\Cart;
use App\Quiz;
use App\User;
use App\Order;
use App\Answer;
use App\Course;
use App\Adsense;
use App\Meeting;
use App\Setting;
use App\Currency;
use App\Question;
use App\Wishlist;
use App\QuizTopic;
use App\WhatLearn;
use Carbon\Carbon;
use App\Allcountry;
use App\Assignment;
use App\Categories;
use App\Googlemeet;
use App\QuizAnswer;
use App\Appointment;
use App\CourseClass;
use App\Installment;
use App\SubCategory;
use App\Announcement;
use App\BundleCourse;
use App\JitsiMeeting;
use App\ReportReview;
use App\ReviewRating;
use App\ChildCategory;
use App\CourseChapter;
use App\CourseInclude;
use App\PlanSubscribe;
use App\PreviousPaper;
use App\PrivateCourse;
use App\RelatedCourse;
use App\CourseProgress;
use App\OfflineSession;
use App\CoursesInBundle;
use App\secondaryCategory;
use App\SessionEnrollment;
use App\QuestionnaireCourse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\OpenGraph;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:courses.view', ['only' => ['index', 'show', 'showCourse', 'enrolledUsers', 'userCourseProgress']]);
        $this->middleware('permission:courses.create', ['only' => ['create', 'store', 'storeIntroductionVideo', 'InstallmentStore']]);
        $this->middleware('permission:courses.edit', ['only' => ['update', 'storeIntroductionVideo', 'InstallmentStore', 'duplicate', 'status', 'courcestatus']]);
        $this->middleware('permission:courses.delete', ['only' => ['destroy', 'bulk_delete']]);
    }

    public function index(Request $request)
    {
        // $searchTerm = $request->input('searchTerm');
        // if (Auth::user()->role == 'instructor') {
        //     $cor = Course::query()->where('user_id', Auth::user()->id);
        // } else {
        //     $cor = Course::query();
        // }

        // $course = $cor->orderBy('id', 'DESC')->paginate(9);

        // if ($request->searchTerm) {
        //     $course = $cor->where('title', 'LIKE', "%$searchTerm%")->where('status', '=', 1)->paginate(9);
        // }
        // if ($request->type) {
        //     if($request->type == 'paid'){
        //         $course = $cor->where('type', '=', 1)->where('discount_price', '!=', 0)->paginate(9);

        //     }else{
        //         $course = $cor->where('type', '=', 0)->orWhere('discount_price', '=', 0)->paginate(9);
        //     }
        // }
        // if ($request->featured) {
        //     $course = $cor->where('featured', '=', $request->featured ? 1 : 0)->paginate(9);
        // }
        // if ($request->status) {
        //     $course = $cor->where('status', '=', $request->status ? 1 : 0)->paginate(9);
        // }
        // if ($request->asc) {
        //     $course = $cor->orderBy('id', 'ASC')->paginate(9);
        // }
        // if ($request->desc) {
        //     $course = $cor->orderBy('id', 'DESC')->paginate(9);
        // }
        // if ($request->category_id) {
        //     $course = $cor->where('category_id', '=', $request->category_id)->paginate(9);
        // }

        // $categorys = Categories::all();

        // $coursechapter = CourseChapter::all();

        // $gsettings = Setting::first();

        // return view('admin.course.index', compact("course", 'coursechapter', 'gsettings', 'categorys'));
        // return response()->json([$course]);


        if (Auth::user()->role == 'instructor') {
            $course = Course::query()
                ->select('id', 'user_id', 'title', 'preview_image', 'installment', 'total_installments', 'price', 'discount_price', 'installment_price', 'status', 'type')
                ->where('user_id', Auth::id())
                ->with(['user:id,fname,lname', 'enrolled:id,title,user_id,course_id', 'installments:id,course_id,amount,due_date'])
                ->withCount('enrolled')
                ->latest();
        } else {
            $course = Course::query()
                ->select('id', 'user_id', 'title', 'preview_image', 'installment', 'total_installments', 'price', 'discount_price', 'installment_price', 'status', 'type')
                ->with(['user:id,fname,lname', 'enrolled:id,title,user_id,course_id', 'installments:id,course_id,amount,due_date'])
                ->withCount('enrolled')
                ->latest();
        }

        if ($request->ajax()) {
            return DataTables::of($course)
                ->addColumn('checkbox', function ($row) {
                    $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->addIndexColumn()
                ->editColumn('image', 'admin.course.datatables.image')
                ->editColumn('title', function ($row) {

                    return $row->title ?? '';
                })
                ->editColumn('instructor', function ($row) {

                    return $row->user->fname ? ($row->user->lname ? $row->user->fname . ' ' . $row->user->lname : $row->user->fname) : '';
                })
                ->editColumn('enrolled', function ($row) {

                    return "<a href='" . route('course.users', [$row->id]) . "'>" . $row->enrolled_count . "</a>";
                })
                ->editColumn('type', function ($row) {
                    if (is_null($row->discount_type)) {
                        if ($row->discount_price != 0) {
                            return __('Paid');
                        } elseif ($row->discount_price == 0) {
                            return __('Free');
                        }
                    } else {
                        if ($row->discount_type == 'percentage') {
                            if ($row->discount_price >= 100) {
                                return __('Free');
                            } else {
                                return __('Paid');
                            }
                        } elseif ($row->discount_type == 'fixed') {
                            if ($row->discount_price >= $row->price) {
                                return __('Free');
                            } else {
                                return __('Paid');
                            }
                        }
                    }
                })
                ->editColumn('status', 'admin.course.datatables.status')
                ->editColumn('action', 'admin.course.datatables.action')
                ->rawColumns(['checkbox', 'image', 'title', 'instructor', 'enrolled', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('admin.course.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userid = auth()->user()->id;
        $category = Categories::where('status', 1)->get();

        $course = Course::where('status', 1)->get();
        $coursechapter = CourseChapter::where('status', 1)->get();

        if (Auth::user()->hasRole('admin')) {
            $users = User::where('id', '!=', Auth::id())->where('role', '!=', 'user')->active()->get();
        } else {
            $users = User::where('id', Auth::id())->active()->first();
        }

        $countries = Allcountry::get();

        return view('admin.course.insert', compact("course", 'coursechapter', 'category', 'users', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'scnd_category_id' => 'required|exists:secondary_categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'childcategory_id' => 'required|array|exists:child_categories,id',
            'user_id' => 'required',
            'title' => 'required|max:100',
            'preview_image' => 'required|mimes:jpg,jpeg,png|max:10240',
            'wtsap_link' => 'nullable|max:200|url',
            'detail' => 'required',
            // 'course_tags' => 'required',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'price' => 'required_with:type|numeric|min:0',
            'price_discount' => 'sometimes|numeric|min:0',
            'discount_type' => 'sometimes|in:fixed,percentage',
            'total_installments' => 'required_if:installment,1|in:2,3,4',
        ], [
            "category_id.required" => __("Country name is required"),
            "category_id.exists" => __("The selected country name is not exist"),
            "scnd_category_id.required" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "subcategory_id.required" => __("Institute name is required"),
            "subcategory_id.exists" => __("The selected Institute name is not exist"),
            "childcategory_id.required" => __("Major name is required"),
            "childcategory_id.exists" => __("The selected major name is not exist"),
            "user_id.required" => __("Instructor name is required"),
            "title.required" => __("Course name is required"),
            "title.max" => __("Course name should not be more than 100 characters"),
            'preview_image.required' => __('Image is required'),
            'preview_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'preview_image.max' => __('Image size should not be more than 10 MB'),
            "wtsap_link.max" => __("WhatsApp link should not be more than 200 characters"),
            "wtsap_link.url" => __("WhatsApp link must be an URL"),
            "detail.required" => __("Course detail is required"),
            // "course_tags.required"=>__("Skill field is required"),
            "start_date.required" => __("Start Date is required"),
            "start_date.date_format" => __("Start Date format must be YYYY-MM-DD"),
            "start_date.after_or_equal" => __("Start Date must be greater than or equal to today's date"),
            "end_date.required" => __("End Date is required"),
            "end_date.date_format" => __("End Date format must be YYYY-MM-DD"),
            "end_date.after" => __("End Date must be greater than selected Start Date"),
            "price.required_with" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
        ]);

        $input = $request->all();

        // if (isset($request->preview_type)) {
        //     $input['preview_type'] = "video";
        //     if ($file = $request->file('video')) {

        //         $filename = time() . $file->getClientOriginalName();
        //         $file->move('video/preview', $filename);
        //         $input['video'] = $filename;
        //         $input['url'] = NULL;
        //     }
        // } else {
        //     $input['preview_type'] = "url";
        //     $input['url'] = $request->url;
        //     $input['video'] = NULL;
        // }

        // if (Auth::user()->role == 'admin') {
        //     if ($request->preview_image != null) {
        //         $input['preview_image'] = $request->preview_image;
        //     }
        // }

        // if (isset($request->duration_type)) {
        //     $input['duration_type'] = "m";
        // } else {
        //     $input['duration_type'] = "d";
        // }

        if ($file = $request->file('preview_image')) {
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/course/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['preview_image'] = $image;
        }

        $input['involvement_request'] = 0;
        $input['assignment_enable'] = 0;
        $input['appointment_enable'] = 0;
        $input['certificate_enable'] = 0;
        $input['drip_enable'] = 0;
        $input['featured'] = 0;

        $input['installment'] = isset($request->installment) ? 1 : 0;
        $input['type'] = isset($request->type) ? 1 : 0;
        $input['status'] = isset($request->status) ? 1 : 0;

        $slug = str_slug($request->title, '-');
        $input['slug'] = $slug;

        if (!isset($input['discount_price']) || !isset($input['discount_type']) || $input['discount_price'] == 0) {
            // $input['discount_price'] = null;
            $input['discount_type'] = null;
        }

        if ($input['installment'] == 1) {
            $input['status'] = 0;
        }

        // dd($input, $request);
        Course::create($input);

        Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect('course')->withInput();
    }

    public function show($id)
    {
        $cor = Course::findOrFail($id);
        $instructor_course = Course::where('id', $id)->where('user_id', Auth::user()->id)->where('status', 1)->first();

        if (Auth::user()->role != "instructor" && Auth::user()->role != "user") {
            if (!isset($instructor_course)) {
                abort(404, 'Page Not Found.');
            }
        }

        $category = Categories::where('status', 1)->get();
        $typecategory = secondaryCategory::where('status', 1)->where('category_id', $cor->category_id)->get();
        $subcategory = SubCategory::where('status', 1)->where(['category_id' => $cor->category_id, 'scnd_category_id' => $cor->scnd_category_id])->get();
        $childcategory = ChildCategory::where('status', 1)->where(['category_id' => $cor->category_id, 'scnd_category_id' => $cor->scnd_category_id, 'subcategory_id' => $cor->subcategory_id])->get();

        return view('admin.course.editcor', compact('cor', 'category', 'typecategory', 'subcategory', 'childcategory'));
    }

    public function edit(course $course)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'scnd_category_id' => 'required|exists:secondary_categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'childcategory_id' => 'required|array|exists:child_categories,id',
            'user_id' => 'required',
            'title' => 'required|max:100',
            'preview_image' => 'mimes:jpg,jpeg,png|max:10240',
            'wtsap_link' => 'nullable|max:200|url',
            'detail' => 'required',
            // 'course_tags' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'price' => 'required_with:type|numeric',
            'price_discount' => 'sometimes|numeric|min:0',
            'discount_type' => 'sometimes|in:fixed,percentage',
            'total_installments' => 'sometimes|in:2,3,4',
        ], [
            "category_id.required" => __("Country name is required"),
            "category_id.exists" => __("The selected country name is not exist"),
            "scnd_category_id.required" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "subcategory_id.required" => __("Institute name is required"),
            "subcategory_id.exists" => __("The selected Institute name is not exist"),
            "childcategory_id.required" => __("Major name is required"),
            "childcategory_id.exists" => __("The selected major name is not exist"),
            "user_id.required" => __("Instructor name is required"),
            "title.required" => __("Course name is required"),
            "title.max" => __("Course name should not be more than 100 characters"),
            "wtsap_link.max" => __("WhatsApp link should not be more than 200 characters"),
            "wtsap_link.url" => __("WhatsApp link must be an URL"),
            'preview_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'preview_image.max' => __('Image size should not be more than 10 MB'),
            "detail.required" => __("Course detail is required"),
            // "course_tags.required"=>__("Skill field is required"),
            "start_date.required" => __("Start Date is required"),
            "start_date.date_format" => __("Start Date format must be YYYY-MM-DD"),
            "end_date.required" => __("End Date is required"),
            "end_date.date_format" => __("End Date format must be YYYY-MM-DD"),
            "end_date.after" => __("End Date must be greater than selected Start Date"),
            "price.required_with" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
        ]);

        $input = $request->all();
        $oldInstallments = $course->total_installments;

        if (auth()->user()->role == 'admin') {
            if (isset($request->status)) {
                $input['status'] = 1;
            } else {
                $input['status'] = 0;
                Cart::where('course_id', $id)->delete();
                Cart::whereIn('chapter_id', $course->chapter->pluck('id'))->delete();
            }
        }

        if (isset($request->installment)) {
            $input['installment'] = 1;
            CourseChapter::where('course_id', $id)->whereNull('unlock_installment')->update([
                'unlock_installment' => 1
            ]);
        } else {
            $input['installment'] = 0;
        }

        // $input['featured'] = isset($request->featured) ? 1 : 0;
        $input['type'] = isset($request->type) ? 1 : 0;

        $slug = str_slug($request->title, '-');
        $input['slug'] = $slug;

        if ($file = $request->file('preview_image')) {
            if ($course->preview_image != null) {
                $content = @file_get_contents(public_path() . '/images/course/' . $course->preview_image);
                if ($content) {
                    unlink(public_path() . '/images/course/' . $course->preview_image);
                }
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/course/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['preview_image'] = $image;
        }

        // if (isset($request->drip_enable)) {
        //     $input['drip_enable'] = 1;
        // } else {
        //     $input['drip_enable'] = 0;
        // }

        // if (isset($request->preview_type)) {
        //     $input['preview_type'] = "video";
        //     if ($file = $request->file('video')) {
        //         if ($course->video != "") {
        //             $content = @file_get_contents(public_path() . '/video/preview/' . $course->video);
        //             if ($content) {
        //                 unlink(public_path() . '/video/preview/' . $course->video);
        //             }
        //         }

        //         $filename = time() . $file->getClientOriginalName();
        //         $file->move('video/preview', $filename);
        //         $input['video'] = $filename;
        //         $input['url'] = NULL;
        //     }
        // } else {
        //     $input['preview_type'] = "url";
        //     $input['url'] = $request->url;
        //     $input['video'] = NULL;
        // }

        // if (isset($request->duration_type)) {
        //     $input['duration_type'] = "m";
        // } else {
        //     $input['duration_type'] = "d";
        // }

        // if (isset($request->involvement_request)) {
        //     $input['involvement_request'] = 1;
        // } else {
        //     $input['involvement_request'] = 0;
        // }

        // if (isset($request->assignment_enable)) {
        //     $input['assignment_enable'] = 1;
        // } else {
        //     $input['assignment_enable'] = 0;
        // }

        // if (isset($request->appointment_enable)) {
        //     $input['appointment_enable'] = 1;
        // } else {
        //     $input['appointment_enable'] = 0;
        // }

        // if (isset($request->certificate_enable)) {
        //     $input['certificate_enable'] = 1;
        // } else {
        //     $input['certificate_enable'] = 0;
        // }

        // $input['total_installments'] = $request->total_installments;
        // $input['installment_price'] = $request->installment_price;

        // if(!isset($request->preview_type))
        // {
        //     $course->url = $request->video_url;
        //     $course->video = null;
        // }
        // else if($request->preview_type )
        // {
        //     if($file = $request->file('video'))
        //     {
        //       if($course->video != "")
        //       {
        //         $content = @file_get_contents(public_path().'/video/preview/'.$course->video);
        //         if ($content) {
        //           unlink(public_path().'/video/preview/'.$course->video);
        //         }
        //       }
        //       $filename = time().$file->getClientOriginalName();
        //       $file->move('video/preview',$filename);
        //       $input['video'] = $filename;
        //       $course->url = null;
        //     }
        // }

        $course->update($input);

        if ($course->installment == 1 && $course->installments->isNotEmpty()) {
            Cart::where(['course_id' => $id, 'installment' => 1])->update([
                'price' => $course->installments->sum('amount'),
                'offer_price' => $course->installments[0]->amount,
                'total_installments' => json_encode([$course->installments[0]->id]),
            ]);
        } else {
            Cart::where(['course_id' => $id, 'installment' => 0])->update([
                'price' => $course->price,
                'offer_price' => $course->discount_price,
            ]);
        }

        Order::where('course_id', $id)->update([
            'enroll_start' => $course->start_date,
            'enroll_expire' => $course->end_date,
            'updated_at' => DB::raw('updated_at')
        ]);

        // Update Sessions categories that are linked with course
        BBL::where('course_id', $course->id)->update([
            'main_category' => $course->category_id,
            'scnd_category_id' => $course->scnd_category_id,
            'sub_category' => $course->subcategory_id,
            'ch_sub_category' => $course->childcategory_id,
        ]);

        OfflineSession::where('course_id', $course->id)->update([
            'main_category' => $course->category_id,
            'scnd_category_id' => $course->scnd_category_id,
            'sub_category' => $course->subcategory_id,
            'ch_sub_category' => $course->childcategory_id,
        ]);

        if (isset($request->total_installments) && $request->total_installments != $oldInstallments) {
            return redirect()->to('/course')->with('success', trans('flash.UpdateTotalInstallments'));
        }

        // Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect()->to('/course')->with('success', trans('flash.UpdatedSuccessfully'));
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        $bundleCourses = BundleCourse::whereJsonContains('course_id', strval($course->id))->get();
        $bundles = CoursesInBundle::where('course_id', $id)->get();

        if ($bundleCourses->isNotEmpty() || $bundles->isNotEmpty()) {
            return back()->with('warning', trans('flash.CannotDeleteBundleCourse'));
        }

        $order = CourseProgress::where('course_id', $id)->get();

        $meetings = BBL::where('course_id', $id)->get();
        $sessions = OfflineSession::where('course_id', $id)->get();

        // if ($course->preview_image != null) {

        //     $image_file = @file_get_contents(public_path() . '/images/course/' . $course->preview_image);

        //     if ($image_file) {
        //         unlink(public_path() . '/images/course/' . $course->preview_image);
        //     }
        // }

        // if ($course->video != null) {

        //     $video_file = @file_get_contents(public_path() . '/video/preview/' . $course->video);

        //     if ($video_file != null) {
        //         unlink(public_path() . '/video/preview/' . $course->video);
        //     }
        // }

        DB::transaction(function () use ($course, $meetings, $sessions) {

            Wishlist::where('course_id', $course->id)->delete();
            Cart::where('course_id', $course->id)->delete();
            Cart::whereIn('chapter_id', $course->chapter->pluck('id'))->delete();
            Installment::where('course_id', $course->id)->delete();
            WhatLearn::where('course_id', $course->id)->delete();
            ReviewRating::where('course_id', $course->id)->delete();
            QuizTopic::where('course_id', $course->id)->delete();
            Quiz::where('course_id', $course->id)->delete();
            QuizAnswer::where('course_id', $course->id)->delete();
            Question::where('course_id', $course->id)->delete();
            Answer::where('course_id', $course->id)->delete();

            $course->update([
                'title' => '[deleted] ' . $course->title,
            ]);

            CourseChapter::where('course_id', $course->id)->update([
                'chapter_name' => DB::raw("REPLACE(JSON_SET(chapter_name, '$.en', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(chapter_name, '$.en'), JSON_EXTRACT(chapter_name, '$.ar'))), '$.ar', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(chapter_name, '$.ar'), JSON_EXTRACT(chapter_name, '$.en')))), '\\\\\"', '')"),
            ]);
            CourseClass::where('course_id', $course->id)->update([
                'title' => DB::raw("REPLACE(JSON_SET(title, '$.en', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.en'), JSON_EXTRACT(title, '$.ar'))), '$.ar', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.ar'), JSON_EXTRACT(title, '$.en')))), '\\\\\"', '')"),
            ]);

            BBL::where('course_id', $course->id)->update([
                'meetingname' => DB::raw("REPLACE(JSON_SET(meetingname, '$.en', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(meetingname, '$.en'), JSON_EXTRACT(meetingname, '$.ar'))), '$.ar', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(meetingname, '$.en'), JSON_EXTRACT(meetingname, '$.en')))), '\\\\\"', '')"),
            ]);
            OfflineSession::where('course_id', $course->id)->update([
                'title' => DB::raw("REPLACE(JSON_SET(title, '$.en', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.en'), JSON_EXTRACT(title, '$.ar'))), '$.ar', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.ar'), JSON_EXTRACT(title, '$.en')))), '\\\\\"', '')"),
            ]);

            Order::query()
                ->where('course_id', $course->id)
                ->orWhereIn('chapter_id', $course->chapter->pluck('id'))
                ->orWhereIn('meeting_id', $meetings->pluck('id'))
                ->orWhereIn('offline_session_id', $sessions->pluck('id'))
                ->update([
                    'title' => DB::raw("REPLACE(JSON_SET(title, '$.en', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.en'), JSON_EXTRACT(title, '$.ar'))), '$.ar', CONCAT('[deleted] ', IFNULL(JSON_EXTRACT(title, '$.ar'), JSON_EXTRACT(title, '$.en')))), '\\\\\"', '')"),
                    'updated_at' => DB::raw('updated_at'),
                ]);

            CourseChapter::where('course_id', $course->id)->delete();
            CourseClass::where('course_id', $course->id)->delete();
            CourseProgress::where('course_id', $course->id)->delete();

            SessionEnrollment::whereIn('meeting_id', $meetings->pluck('id'))->delete();
            SessionEnrollment::whereIn('offline_session_id', $sessions->pluck('id'))->delete();

            $meetings->each->delete();
            $sessions->each->delete();
            $course->delete();
        });

        if ($meetings->isNotEmpty() || $sessions->isNotEmpty()) {
            return back()->with('delete', trans('flash.DeleteMeetingOrSessionCourse'));
        }

        if ($order->isNotEmpty()) {
            return back()->with('delete', trans('flash.DeleteOrderCourse'));
        }

        return back()->with('delete', trans('flash.DeletedSuccessfully'));
    }

    // This function performs bulk delete action
    public function bulk_delete(Request $request)
    {
        $validator = Validator::make($request->all(), ['checked' => 'required']);

        if ($validator->fails()) {
            return back()->with('warning', __('Atleast one item is required to be checked'));
        } else {
            foreach ($request->checked as $id) {
                $this->destroy($id);
                // Course::whereIn('id', $request->checked)->delete();
                // Session::flash('delete', trans('Selected item has been deleted successfully !'));
            }
            // return back()->with('error', trans('Selected Course has been deleted.'));
            return redirect()->back();
        }
    }

    public function storeIntroductionVideo(Request $request, $id)
    {
        $request->validate([
            'video_url' => 'required',
            'iframe_url' => 'required',
            'duration' => 'required|digits_between:1,3',
        ], [
            'video_url.required' => __('Iframe video URL is required'),
            'iframe_url.required' => __('Introduction video URL is required'),
            'duration.required_if' => __('Duration is required'),
            "duration.digits_between" => __('Duration should not be more than 3 digits'),
        ]);


        $course = Course::findOrFail($id);

        $course->update($request->all());

        return back();
    }

    public function enrolledUsers(Request $request, $id)
    {
        $orders = Order::query()
            ->where('course_id', $id)
            ->whereHas('user', function ($query) {
                $query->exceptTestUser();
            })
            ->activeOrder()
            ->with('user:id,fname,lname,mobile,email')
            ->latest('id');

        if ($request->ajax()) {
            return DataTables::of($orders)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->user->fname ? ($row->user->lname ? $row->user->fname . ' ' . $row->user->lname : $row->user->fname) : '';
                })
                ->editColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <button class="btn btn-round btn-outline-primary" type="button"
                                    id="CustomdropdownMenuButton1" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false"><i
                                        class="feather icon-more-vertical-"></i></button>
                                <div class="dropdown-menu"
                                    aria-labelledby="CustomdropdownMenuButton1">

                                    <a class="dropdown-item"
                                        href="' . route("course.user.progress", ["course_id" => $row->course_id, "user_id" => $row->user_id]) . '"><i
                                            class="feather icon-bar-chart mx-2"></i>' . __("View Progress") . '</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        $course = Course::query()
            ->select('id', 'title')
            ->with('chapter', function ($q) {
                $q->with('enrolled', function ($q) {
                    $q->with('user:id,fname,lname,mobile,email');
                });
            })
            ->find($id);

        return view('admin.course.users.index', compact('course'));
    }

    public function userCourseProgress(Request $request)
    {
        try {
            $progress = CourseProgress::where(['user_id' => $request->user_id, 'course_id' => $request->course_id])->activeProgress()->first();
            $readClassIds = $progress->mark_chapter_id;

            if ($request->chapter_id) {
                $classIds = CourseClass::where('coursechapter_id', $request->chapter_id)->pluck('id')->toArray();

                $array1 = $classIds;
                $array2 = $progress->mark_chapter_id;
                $readClassIds = array_intersect($array1, $array2);
            }

            $classes = CourseClass::whereIn('id', $readClassIds);
        } catch (\Throwable $th) {
            abort(404);
        }

        if ($request->ajax()) {
            return DataTables::of($classes)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.course.users.progress', compact('progress'));
    }

    public function showCourse($id)
    {
        $cor = Course::findOrFail($id);

        $course = Course::all();

        if (Auth::user()->role == 'admin') {
            $users = User::where('id', '!=', auth()->id())->where('role', '!=', 'user')->active()->get();
        } else {
            $users = User::active()->findOrFail(auth()->id());
        }

        $courses = Course::select('id', 'title')->active()->get();
        // $courseinclude = CourseInclude::where('course_id', '=', $id)->latest()->get();
        $coursechapters = $coursechapter = CourseChapter::where('course_id', '=', $id)->with('courses')->orderBy('position', 'ASC')->get();
        $whatlearns = WhatLearn::where('course_id', '=', $id)->latest()->get();
        // $coursechapters = CourseChapter::where('course_id', '=', $id)->where('status', 1)->orderBy('position', 'ASC')->get();
        // $relatedcourse = RelatedCourse::where('main_course_id', '=', $id)->latest()->get();
        $courseclass = CourseClass::where('course_id', '=', $id)->orderBy('position', 'ASC')->get();
        // $announsments = Announcement::where('course_id', '=', $id)->get();
        $reports = ReportReview::where('course_id', '=', $id)->get();
        $questions = Question::where('course_id', '=', $id)->latest()->get();
        $quizes = Quiz::where('course_id', '=', $id)->latest()->get();
        $topics = QuizTopic::where('course_id', '=', $id)->latest()->get();
        $classquizes = QuizTopic::where('course_id', '=', $id)->doesnthave('courseclass')->get();
        // $appointment = Appointment::where('course_id', '=', $id)->get();
        $installments = $cor->installments()->get();
        $bbl_meetings = BBL::whereNotNull('link_by')->where('is_ended', '<>', 1)->where('course_id', $id)->get();
        $offline_sessions = OfflineSession::whereNotNull('link_by')->where('is_ended', '<>', 1)->where('course_id', $id)->get();

        $chapterExists = null;
        if ($cor->total_installments) {
            $chapterExists = CourseChapter::where('course_id', $cor->id)->where('unlock_installment', $cor->total_installments)->exists();
        }

        $orderExists = Order::query()
            ->where('installments', 1)
            ->where(function ($query) use ($cor) {
                $query->where('course_id', $cor->id)
                    ->OrWhereJsonContains('bundle_course_id', strval($cor->id));
            })
            ->activeOrder()
            ->exists();

        //return questionnaire here
        $questionnaires = QuestionnaireCourse::where('course_id', $id)->with('questionnaire:id,title')->get(['id', 'course_id', 'questionnaire_id', 'appointment']);
        $questionnaires = $questionnaires->map(function ($item) {
            return [
                'id' => $item->id,
                'appointment' => $item->appointment,
                'questionnaire_title' => $item->questionnaire->title,
                'course_id' => $item->course_id,
                'questionnaire_id' => $item->questionnaire->id
            ];
        });

        $allQuestionnaires = QuestionnaireCourse::with('course:id,title')
            ->with('questionnaire:id,title')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])->get();
        if($allQuestionnaires){
            $allQuestionnaires = $allQuestionnaires->toArray();
        }else{
            $allQuestionnaires = [];
        }

        // $papers = PreviousPaper::where('course_id', '=', $id)->get();
        // $countries = Allcountry::get();

        // return view('admin.course.show', compact('installments', 'cor', 'course', 'courseinclude', 'whatlearns', 'coursechapters', 'coursechapter', 'relatedcourse', 'courseclass', 'announsments', 'reports', 'questions', 'quizes', 'topics', 'classquizes', 'bbl_meetings', 'offline_sessions', 'appointment', 'papers', 'users', 'countries'));
        return view('admin.course.show', compact('installments', 'cor', 'course', 'whatlearns', 'courses', 'coursechapters', 'coursechapter', 'courseclass', 'reports', 'questions', 'quizes', 'topics', 'classquizes', 'bbl_meetings', 'offline_sessions', 'users', 'chapterExists', 'orderExists', 'questionnaires','allQuestionnaires'));
    }

    public function duplicate(Request $request, $id)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, __('User does not have the right permissions'));

        $existingOpening = Course::findOrFail($id);

        $newOpenening = $existingOpening->replicate();

        // if($existingOpening->installments){
        //     $existingInstallment = Installment::where('course_id', $existingOpening->id)->get();
        //     // $newInstallment = $existingInstallment->replicate();

        //     $newInstallment = $existingInstallment->replicate()->fill(
        //         [
        //             'course_id' => $newOpenening->id,
        //         ]
        //     );

        //     $newInstallment->save();
        // }


        if ($existingOpening->preview_image != null) {
            $oldPath = public_path('images/course/' . $existingOpening->preview_image); // publc/images/1.jpg

            $fileExtension = File::extension($oldPath);

            $newName = 'duplicate' . time() . '.' . $fileExtension;

            $newPathWithName = public_path('images/course/' . $newName);

            File::copy($oldPath, $newPathWithName);
        } else {
            $newName = null;
        }

        // if ($existingOpening->video == !NULL && @file_get_contents(public_path() . 'video/preview/' . $existingOpening->video)) {
        //     $oldPath = public_path('video/preview/' . $existingOpening->video); // publc/images/1.jpg

        //     $fileExtension = \File::extension($oldPath);

        //     $newVideo = 'duplicate' . time() . '.' . $fileExtension;

        //     $newPathWithName = public_path('video/preview/' . $newVideo);

        //     \File::copy($oldPath, $newPathWithName);
        // } else {

        //     $newVideo = NULL;
        // }

        $newOpenening = $existingOpening->replicate()->fill(
            [
                'slug' => str_slug($existingOpening->slug . '-copy-' . time() . $existingOpening->id, '-'),
                'preview_image' => $newName,
                'status' => 0,
            ]
        );

        if ($request->installment == 0) {
            $newOpenening = $existingOpening->replicate()->fill(
                [
                    'slug' => str_slug($existingOpening->slug . '-copy-' . time() . $existingOpening->id, '-'),
                    'preview_image' => $newName,
                    'status' => 0,
                    'installment' => 0,
                    'total_installments' => null,
                    'installment_price' => null
                ]
            );
            $newOpenening->save();
        } else if ($request->installment == 1) {
            $newOpenening = $existingOpening->replicate()->fill(
                [
                    'slug' => str_slug($existingOpening->slug . '-copy-' . time() . $existingOpening->id, '-'),
                    'preview_image' => $newName,
                    'status' => 0
                ]
            );
            $newOpenening->save();

            // $old_installments = Installment::where('course_id', $existingOpening->id)->get();
            foreach ($existingOpening->installments as $installment) {
                $new_installment = $installment->replicate()->fill(
                    [
                        'course_id' => $newOpenening->id,
                    ]
                );

                $new_installment->save();
            }
        }


        // $old_courseinclude = CourseInclude::where('course_id', $existingOpening->id)->get();

        // foreach ($old_courseinclude as $include) {
        //     $new_courseinclude = $include->replicate()->fill(
        //             [
        //                 'course_id' => $newOpenening->id,
        //             ]
        //     );

        //     $new_courseinclude->save();
        // }

        $old_whatlearn = WhatLearn::where('course_id', $existingOpening->id)->get();
        foreach ($old_whatlearn as $whatlearn) {
            $new_whatlearn = $whatlearn->replicate()->fill(
                [
                    'course_id' => $newOpenening->id,
                ]
            );

            $new_whatlearn->save();
        }

        $old_quizes = QuizTopic::where('course_id', $existingOpening->id)->get();
        foreach ($old_quizes as $quiz) {
            $new_quiz = $quiz->replicate()->fill(
                [
                    'course_id' => $newOpenening->id,
                ]
            );

            $new_quiz->save();

            $old_quizquestions = Quiz::where('topic_id', $quiz->id)->get();

            foreach ($old_quizquestions as $quizquestion) {
                $new_quizquestion = $quizquestion->replicate()->fill(
                    [
                        'course_id' => $newOpenening->id,
                        'topic_id' => $new_quiz->id,
                    ]
                );

                $new_quizquestion->save();
            }
        }

        $old_questions = Question::where('course_id', $existingOpening->id)->get();
        foreach ($old_questions as $question) {
            $new_question = $question->replicate()->fill(
                [
                    'course_id' => $newOpenening->id,
                ]
            );

            $new_question->save();

            $old_answers = Answer::where('question_id', $question->id)->get();

            foreach ($old_answers as $answer) {
                $new_answer = $answer->replicate()->fill(
                    [
                        'course_id' => $newOpenening->id,
                        'question_id' => $new_question->id,
                    ]
                );

                $new_answer->save();
            }
        }

        $old_chapter = CourseChapter::where('course_id', $existingOpening->id)->get();
        foreach ($old_chapter as $chapter) {
            $new_chapter = $chapter->replicate()->fill(
                [
                    'course_id' => $newOpenening->id,
                    'file' => null,
                    'unlock_installment' => $request->installment == 1 ? $chapter->unlock_installment : null
                ]
            );

            $new_chapter->save();

            $old_class = CourseClass::where('coursechapter_id', $chapter->id)->get();

            foreach ($old_class as $key => $class) {
                // if ($class->pdf == !NULL && @file_get_contents(public_path() . 'files/pdf/' . $class->pdf)) {
                if (
                    $class->file != null &&
                    ($class->type == 'pdf' || $class->type == 'zip' || $class->type == 'rar' || $class->type == 'word' || $class->type == 'excel' || $class->type == 'powerpoint') &&
                    Storage::exists("/files/$class->type/" . $class->file)
                ) {
                    $oldPathFile = Storage::path("/files/$class->type/" . $class->file);

                    // $fileExtension = File::extension($oldPathFile);

                    $newclassFile = '[duplicate]' . $key . $class->file;

                    $newPathWithFile = Storage::path("files/$class->type/" . $newclassFile);

                    File::copy($oldPathFile, $newPathWithFile);
                } else {
                    $newclassFile = null;
                }

                // if ($class->video == !NULL && @file_get_contents(public_path() . 'video/class/' . $class->video)) {

                //     $oldPathVideo = public_path('video/class/' . $class->video); // publc/images/1.jpg

                //     $fileExtension = \File::extension($oldPathVideo);

                //     $newclassVideo = 'duplicate' . time() . '.' . $fileExtension;

                //     $newPathWithVideo = public_path('video/class/' . $newclassVideo);

                //     \File::copy($oldPathVideo, $newPathWithVideo);
                // } else {

                //     $newclassVideo = NULL;
                // }


                // if ($class->zip == !NULL && @file_get_contents(public_path() . 'video/class/' . $class->zip)) {

                //     $oldPathZIP = public_path('video/class/' . $class->zip); // publc/images/1.jpg

                //     $fileExtension = \File::extension($oldPathZIP);

                //     $newclassZIP = 'duplicate' . time() . '.' . $fileExtension;

                //     $newPathWithZIP = public_path('video/class/' . $newclassZIP);

                //     \File::copy($oldPathZIP, $newPathWithZIP);
                // } else {

                //     $newclassZIP = NULL;
                // }


                // if ($class->preview_video == !NULL && @file_get_contents(public_path() . 'video/class/' . $class->preview_video)) {

                //     $oldPathPreview = public_path('video/class/preview/' . $class->preview_video); // publc/images/1.jpg

                //     $fileExtension = \File::extension($oldPathPreview);

                //     $newclassPreview = 'duplicate' . time() . '.' . $fileExtension;

                //     $newPathWithPreview = public_path('video/class/preview/' . $newclassPreview);

                //     \File::copy($oldPathPreview, $newPathWithPreview);
                // } else {

                //     $newclassPreview = NULL;
                // }


                // if ($class->audio == !NULL && @file_get_contents(public_path() . 'video/class/' . $class->audio)) {

                //     $oldPathAUDIO = public_path('video/class/' . $class->video); // publc/images/1.jpg

                //     $fileExtension = \File::extension($oldPathAUDIO);

                //     $newclassVideo = 'duplicate' . time() . '.' . $fileExtension;

                //     $newPathWithAUDIO = public_path('video/class/' . $newclassAUDIO);

                //     \File::copy($oldPathAUDIO, $newPathWithAUDIO);
                // } else {

                //     $newclassAUDIO = NULL;
                // }


                // if ($class->file == !NULL && @file_get_contents(public_path() . 'files/class/material/' . $class->file)) {

                //     $oldPathfile = public_path('files/class/material/' . $class->file); // publc/images/1.jpg

                //     $fileExtension = \File::extension($oldPathfile);

                //     $newclassfile = 'duplicate' . time() . '.' . $fileExtension;

                //     $newPathWithVideo = public_path('files/class/material/' . $newclassfile);

                //     \File::copy($oldPathfile, $newPathWithfile);
                // } else {

                //     $newclassfile = NULL;
                // }

                $new_class = $class->replicate()->fill(
                    [
                        'course_id' => $newOpenening->id,
                        'coursechapter_id' => $new_chapter->id,
                        // 'video' => $newclassVideo,
                        // 'pdf' => $newclassPDF,
                        // 'zip' => $newclassZIP,
                        // 'preview_video' => $newclassPreview,
                        // 'audio' => $newclassAUDIO,
                        'position' => (CourseClass::count() + 1),
                        'file' => $newclassFile,
                    ]
                );

                $new_class->save();
            }
        }

        return back()->with('success', trans('flash.CourseDuplicatedSuccessfully'));
    }

    public function courseClasses(Request $request)
    {
        $courseIds = $request->course_ids ?? [];
        $classes = CourseClass::select('id', 'title')
            ->whereIn('course_id', $courseIds)
            ->active()
            ->get();

        return response()->json($classes);
    }

    public function InstallmentStore(Request $r)
    {
        $course = Course::findOrFail($r->course_id);

        $this->validate($r, [
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|array|min:2|max:4',
            'amount.*' => 'required|numeric|min:1',
            'due_date' => [
                'required',
                'array',
                'min:2',
                'max:4',
                function ($attribute, $value, $fail) {
                    for ($i = 1; $i < count($value); $i++) {
                        if ($value[$i] < $value[$i - 1] && !empty($value[$i])) {
                            $fail(__('Installment date should not be less than previous installment date'));
                        }
                    }
                }
            ],
            'due_date.*' => 'required|date_format:Y-m-d|after_or_equal:' . $course->start_date . '|before_or_equal:' . $course->end_date,
        ], [
            "course_id.required" => __('Course name is required'),
            "course_id.exists" => __('The selected course name is not exist'),
            "amount.*.required" => __("All installments amount is required"),
            "amount.*.numeric" => __("Amount must be a numeric value"),
            "amount.*.min" => __("Amount should not be a zero OR a negative integer"),
            "due_date.*.required" => __("All installments due date is required"),
            "due_date.*.date_format" => __("Due date format must be YYYY-MM-DD"),
            "due_date.*.after_or_equal" => __("Due date must be greater than or equal to course start date"),
            "due_date.*.before_or_equal" => __("Due date must be less than or equal to course end date"),
        ]);

        foreach ($r->amount as $k => $m) {
            Installment::updateOrCreate([
                'course_id' => $r->course_id,
                'sort' => $k + 1,
            ], [
                'due_date' => $r->due_date[$k],
                'amount' => $m,
                'created_by' => auth()->id()
            ]);
        }

        $total = $course->installments->sum('amount');

        $course->update([
            'installment_price' => $total,
            'status' => 1,
            // 'total_installments' => 3,
        ]);

        Cart::where(['course_id' => $r->course_id, 'installment' => 1])->update([
            'price' => $total,
            'offer_price' => $r->amount[0],
            'total_installments' => json_encode([$course->installments[0]->id]),
        ]);

        return back()->with('success', __('Installment Updated'));
    }

    // abandoned
    public function InstallmentDelete($id)
    {
        $inst = Installment::findOrFail($id);
        $amount = Installment::where('course_id', $inst->course_id)->where('id', $id)->sum('amount');
        $total = Installment::where('course_id', $inst->course_id)->where('id', '<>', $id)->count();

        Course::where('id', $inst->course_id)->update([
            'installment_price' => DB::raw("installment_price - $amount"),
            'total_installments' => $total,
        ]);

        Installment::where('id', $id)->delete();
        return back()->with('delete', __('Installment deleted'));
    }

    // abandoned
    public function Installment_bulk_delete(Request $request)
    {
        $validator = Validator::make($request->all(), ['checked' => 'required']);

        if ($validator->fails()) {
            return back()->with('error', trans('Please select field to be deleted.'));
        }

        $amount = Installment::whereIn('id', $request->checked)->sum('amount');
        $inst = Installment::whereIn('id', $request->checked)->first();

        $total = Installment::where('course_id', $inst->course_id)->whereNotIn('id', $request->checked)->count();

        Course::where('id', $inst->course_id)->update([
            'installment_price' => DB::raw("installment_price - $amount"),
            'total_installments' => $total,
        ]);

        Installment::whereIn('id', $request->checked)->delete();
        return back()->with('delete', trans('Selected Installment has been deleted.'));
    }

    // abandoned
    public function courceInstallmentStatus($id)
    {
        $course = Course::findOrFail($id);
        $course->installment = !$course->installment;
        $course->save();

        if ($course->installment == 0) {
            return back()->with('delete', __('Installments changed to deactive !'));
        } else {
            return back()->with('success', __('Installments changed to active !'));
        }
    }

    public function status(Request $request)
    {
        $course = Course::findOrFail($request->id);
        $course->status = $request->status;
        $course->save();
        if (!$course->status) {
            Wishlist::where('course_id', $request->id)->delete();
            Cart::where('course_id', $request->id)->delete();
            Cart::whereIn('chapter_id', $course->chapter->pluck('id'))->delete();
        }
        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }

    public function courcestatus(Request $request)
    {
        $catstatus = Course::findOrFail($request->id);
        $catstatus->status = $request->status;
        $catstatus->save();
        if (!$catstatus->status) {
            Wishlist::where('course_id', $request->id)->delete();
            Cart::where('course_id', $request->id)->delete();
            Cart::whereIn('chapter_id', $catstatus->chapter->pluck('id'))->delete();
        }
        return response()->json(['success' => 'Status Updated Successfully']);
    }

    public function courcefeatured(Request $request)
    {
        $catfeature = Course::find($request->id);
        $catfeature->featured = $request->featured;
        $catfeature->save();
        return back()->with('success', __('Status change successfully'));
    }

    public function type_info(Request $request)
    {
        $id = $request['catId'];
        $category = Categories::where('status', 1)->findOrFail($id);
        $upload = $category->typecategory->where('category_id', $id)->where('status', 1)->pluck('title', 'id');

        return response()->json($upload);
    }

    public function upload_info(Request $request)
    {
        $id = $request['catId'];
        $type_id = $request['typeId'];

        $upload = SubCategory::where(['category_id' => $id, 'scnd_category_id' => $type_id])->where('status', 1)->pluck('title', 'id');

        return response()->json($upload);
    }

    public function gcato(Request $request)
    {
        $id = $request['catId'];
        $type_id = $request['typeId'];
        $sub_id = $request['subId'];

        $upload = ChildCategory::where(['category_id' => $id, 'scnd_category_id' => $type_id, 'subcategory_id' => $sub_id])->where('status', 1)->pluck('title', 'id');

        return response()->json($upload);
    }

    public function CourseDetailPage($id, $slug)
    {
        $course = Course::findOrFail($id);

        session()->push('courses.recently_viewed', $id);

        $courseinclude = CourseInclude::where('course_id', '=', $id)->orderBy('id', 'ASC')->get();
        $whatlearns = WhatLearn::where('course_id', '=', $id)->orderBy('id', 'ASC')->get();
        $coursechapters = CourseChapter::where('course_id', '=', $id)->orderBy('id', 'ASC')->get();
        $relatedcourse = RelatedCourse::where('status', 1)->where('main_course_id', '=', $id)->get();
        $coursereviews = ReviewRating::where('course_id', '=', $id)->get();
        $courseclass = CourseClass::orderBy('position', 'ASC')->get();
        $reviews = ReviewRating::where('course_id', '=', $id)->get();
        $bundle_check = BundleCourse::first();

        $currency = Currency::first();

        $bigblue = BBL::where('course_id', '=', $id)->get();

        $meetings = Meeting::where('course_id', '=', $id)->get();
        $googlemeetmeetings = Googlemeet::where('course_id', '=', $id)->get();
        $jitsimeetings = JitsiMeeting::where('course_id', '=', $id)->get();

        $ad = Adsense::first();

        if (Auth::check()) {
            $private_courses = PrivateCourse::where('course_id', '=', $id)->first();

            if (isset($private_courses)) {
                $user_id = array();
                array_push($user_id, $private_courses->user_id);
                $user_id = array_values(array_filter($user_id));
                $user_id = array_flatten($user_id);

                if (in_array(Auth::user()->id, $user_id)) {
                    return back()->with('delete', trans('flash.UnauthorizedAction'));
                }
            }

            $order = Order::where('refunded', 0)->where('status', 1)->where('user_id', Auth::user()->id)->where('course_id', $id)->first();
            $wish = Wishlist::where('user_id', Auth::user()->id)->where('course_id', $id)->first();
            $cart = Cart::where('user_id', Auth::user()->id)->where('course_id', $id)->first();
            $instruct_course = Course::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();

            if (!empty($bundle_check)) {
                if (Auth::user()->role == 'user') {
                    $bundle = Order::where('user_id', Auth::user()->id)->where('bundle_id', '!=', null)->where('status', 1)->get();

                    $course_id = array();

                    foreach ($bundle as $b) {
                        $bundle = BundleCourse::where('id', $b->bundle_id)->first();
                        array_push($course_id, $bundle->course_id);
                    }

                    $course_id = array_values(array_filter($course_id));

                    $course_id = array_flatten($course_id);

                    return view('front.course_detail', compact('course', 'courseinclude', 'whatlearns', 'coursechapters', 'courseclass', 'coursereviews', 'reviews', 'relatedcourse', 'course_id', 'ad', 'bigblue', 'meetings', 'googlemeetmeetings', 'jitsimeetings', 'order', 'wish', 'currency', 'cart', 'instruct_course'));
                } else {
                    return view('front.course_detail', compact('course', 'courseinclude', 'whatlearns', 'coursechapters', 'courseclass', 'coursereviews', 'reviews', 'relatedcourse', 'ad', 'bigblue', 'meetings', 'googlemeetmeetings', 'jitsimeetings', 'order', 'wish', 'currency', 'cart', 'instruct_course'));
                }
            } else {
                return view('front.course_detail', compact('course', 'courseinclude', 'whatlearns', 'coursechapters', 'courseclass', 'coursereviews', 'reviews', 'relatedcourse', 'ad', 'bigblue', 'meetings', 'googlemeetmeetings', 'jitsimeetings', 'order', 'wish', 'currency', 'cart', 'instruct_course'));
            }
        } else {
            return view('front.course_detail', compact('course', 'courseinclude', 'whatlearns', 'coursechapters', 'courseclass', 'coursereviews', 'reviews', 'relatedcourse', 'ad', 'bigblue', 'meetings', 'googlemeetmeetings', 'jitsimeetings', 'currency'));
        }
    }

    public function CourseContentPage($id, $slug)
    {
        $course = Course::where('id', $id)->with(['user', 'chapter', 'chapter.courseclass'])->first();

        $coursequestions = Question::where('course_id', '=', $id)->with('user')->get();

        $announsments = Announcement::where('course_id', '=', $id)->with('user')->get();

        $bigblue = BBL::where('course_id', '=', $id)->get();

        $meetings = Meeting::where('course_id', '=', $id)->with('user')->get();
        $googlemeetmeetings = Googlemeet::where('course_id', '=', $id)->get();
        $jitsimeetings = JitsiMeeting::where('course_id', '=', $id)->get();

        $papers = PreviousPaper::where('course_id', '=', $id)->get();

        if (Auth::check()) {
            $progress = CourseProgress::where('course_id', '=', $id)->where('user_id', Auth::user()->id)->first();

            $assignment = Assignment::where('course_id', '=', $id)->where('user_id', Auth::user()->id)->get();

            $appointment = Appointment::where('course_id', '=', $id)->where('user_id', Auth::user()->id)->get();

            return view('front.course_content', compact('course', 'coursequestions', 'announsments', 'progress', 'assignment', 'appointment', 'bigblue', 'meetings', 'googlemeetmeetings', 'jitsimeetings', 'papers'));
        }

        return Redirect::route('login')->withInput()->with('delete', trans('flash.PleaseLogin'));
    }

    public function mycoursepage()
    {
        if (Auth::check()) {
            $course = Course::all();
            $enroll = Order::where('refunded', 0)->where('status', 1)->where('user_id', Auth::user()->id)->get();

            return view('front.my_course', compact('course', 'enroll'));
        }

        return Redirect::route('login')->withInput()->with('delete', trans('flash.PleaseLogin'));
    }
}
