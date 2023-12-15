<?php

namespace App\Http\Controllers;

use App\Cart;
use App\User;
use App\Order;
use Exception;
use App\Course;
use App\Wishlist;
use Carbon\Carbon;
use App\Categories;
use App\SubCategory;
use App\ChildCategory;
use App\CourseChapter;
use App\OfflineSession;
use App\secondaryCategory;
use App\SessionEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class OfflineSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:in-person-session.view', ['only' => ['index', 'enrolledUser']]);
        $this->middleware('permission:in-person-session.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:in-person-session.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:in-person-session.delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $sessions = OfflineSession::query()
                                        // where('is_ended', '!=', 1)
                                        // ->whereDate('start_time', '>=', Carbon::now())
                                        ->latest()
                                        ->with(['chapters']);
        } else if (Auth::user()->hasRole('instructor')) {
            $sessions = OfflineSession::query()
                                        ->where('instructor_id', Auth::id())
                                        // ->where('is_ended', '!=', 1)->whereDate('start_time', '>=', Carbon::now())
                                        ->latest()
                                        ->with(['chapters']);
        }

        if ($request->ajax()) {
            return DataTables::of($sessions)
                ->addColumn('checkbox', function ($row) {
                    $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->addIndexColumn()
                ->editColumn('image', 'admin.offlinesession.datatables.image')
                ->editColumn('detail', 'admin.offlinesession.datatables.detail')
                ->editColumn('action', 'admin.offlinesession.datatables.action')
                ->rawColumns(['checkbox', 'image', 'detail', 'action'])
                ->make(true);
        }
        
        return view('admin.offlinesession.index');
    }


    public function create()
    {
        if (auth()->user()->role == "admin") {
            $course = Course::where('status', true)->get();
            $users = User::where('status', true)->where('id', '!=', auth()->user()->id)->where('role', '!=', 'user')->get();
        } else {
            $course = Course::where('status', true)->where('user_id', auth()->user()->id)->get();
            $users = User::where('status', true)->where('id', auth()->user()->id)->first();
        }

        $category = Categories::where('status', true)->get();
        return view('admin.offlinesession.create', compact('category', 'users', 'course'));
    }


    public function store(Request $request)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', now(), 'UTC');
        $date->setTimezone(auth()->user()->timezone);

        $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
            'main_category' => 'required_without:link_by',
            'scnd_category_id' => 'required_with:main_category|exists:secondary_categories,id',
            'sub_category' => 'required_with:scnd_category_id|exists:sub_categories,id',
            'ch_sub_category' => 'required_with:sub_category|array|exists:child_categories,id',
            'title' => 'required|max:100',
            'detail' => 'required',
            'instructor_id' => 'required',
            'start_time' => 'required|date_format:Y-m-d h:i a|after_or_equal:' . $date->format('Y-m-d h:i a'),
            'expire_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d', strtotime($request->start_time)),
            'duration' => 'required|digits_between:1,3',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'setMaxParticipants' => 'required|numeric|min:1',
            'welcomemsg' => 'max:250',
        ], [
            'image.required' => __('Image is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should be not more than 10 MB'),
            "main_category.required_without" => __("Country name is required"),
            "scnd_category_id.required_with" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "sub_category.required_with" => __("Institute name is required"),
            "sub_category.exists" => __("The selected Institute name is not exist"),
            "ch_sub_category.required_with" => __("Major name is required"),
            "ch_sub_category.exists" => __("The selected major name is not exist"),
            "title.required" => __("In-person session name is required"),
            "title.max" => __("In-person session name should not be more than 100 characters"),
            "detail.required" => __("In-person session detail is required"),
            "instructor_id.required" => __("Instructor name is required"),
            "start_time.required" => __("Start time is required"),
            "start_time.date_format" => __("Start time format must be YYYY-MM-DD hh:mm am/pm"),
            "start_time.after_or_equal" => __("Start datetime must be greater than or equal to selected timezone (" . auth()->user()->timezone . ") datetime i.e. " . $date->format('Y-m-d h:i a')),
            "expire_date.required" => __("Session expire date is required"),
            "expire_date.date_format" => __("Expire date format must be YYYY-MM-DD"),
            "expire_date.after_or_equal" => __("Expire date must be greater than or equal to presentation start date"),
            "duration.required" => __("Duration field is required"),
            "duration.digits_between" => __("Duration should not be more than 3 digits"),
            "price.required" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
            "price.min" => __("Price should not be a negitive number"),
            "discount_price.required" => __("Discount Price is required"),
            "discount_price.numeric" => __("Discount Price must be in numeric"),
            "discount_price.min" => __("Discount Price should not be a negitive number"),
            "setMaxParticipants.required" => __("Maximum participants field is required"),
            "setMaxParticipants.numeric" => __("Maximum participants must be in numeric"),
            "welcomemsg.max" => __("Welcome messsage should not be more than 250 characters"),
        ]);

        $offlinesession = new OfflineSession();
        $input = $request->all();

        $sessions = OfflineSession::where('is_ended', '!=', 1)->get();

        foreach ($sessions as $session) {
            if ($request->title == $session->title) {
                return back()->with('delete', __('In-person session is already active with this name !'))->withInput();
            }
        }

        if ($request->setMaxParticipants == '') {
            $input['setMaxParticipants'] = '-1';
        }

        if (isset($request->link_by)) {
            $input['link_by'] = 'course';
            $input['course_id'] = $request['course_id'];

            $course = Course::find($request->course_id);
            $input['main_category'] = $course->category_id;
            $input['scnd_category_id'] = $course->scnd_category_id;
            $input['sub_category'] = $course->subcategory_id;
            $input['ch_sub_category'] = $course->childcategory_id;
        } else {
            $input['link_by'] = null;
            $input['course_id'] = null;
        }

        if ($file = $request->file('image')) {
            $path = 'images/offlinesession/';

            if (!file_exists(public_path() . '/' . $path)) {
                File::makeDirectory(public_path() . '/' . $path, 0777, true);
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/offlinesession/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['image'] = $image;
        }

        // $input['start_time'] = Carbon::parse($request->start_time)->toRfc3339String();
        $input['start_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->start_time, auth()->user()->timezone);
        $input['start_time']->setTimezone('UTC');

        $input['owner_id'] = auth()->user()->id;

        $offlinesession->create($input);

        return redirect()->route('offline.sessions.index')->with('success', trans('flash.CreatedSuccessfully'));
    }


    public function show(OfflineSession $offlineSession)
    {
        //
    }


    public function edit(OfflineSession $session)
    {
        if (auth()->user()->role == "admin") {
            $course = Course::all();
            $users = User::where('status', true)->where('id', '!=', auth()->user()->id)->where('role', '!=', 'user')->get();
        } else {
            $course = Course::where('user_id', auth()->user()->id)->get();
            $users = User::where('id', auth()->user()->id)->first();
        }

        $category = Categories::where('status', true)->get();
        $typecategory = secondaryCategory::where('status', true)->where('category_id', $session->main_category)->get();
        $subcategory = SubCategory::where('status', true)->where(['category_id' => $session->main_category, 'scnd_category_id' => $session->scnd_category_id])->get();
        $childcategory = ChildCategory::where('status', true)->where(['category_id' => $session->main_category, 'scnd_category_id' => $session->scnd_category_id, 'subcategory_id' => $session->sub_category])->get();

        return view('admin.offlinesession.edit', compact('category', 'users', 'course', 'session', 'category', 'typecategory', 'subcategory', 'childcategory'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'mimes:jpg,jpeg,png|max:10240',
            'main_category' => 'required_without:link_by',
            'scnd_category_id' => 'required_with:main_category',
            'sub_category' => 'required_with:scnd_category_id',
            'ch_sub_category' => 'required_with:sub_category|array',
            'title' => 'required|max:100',
            'detail' => 'required',
            'instructor_id' => 'required',
            'start_time' => 'required|date_format:Y-m-d h:i a',
            'expire_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d', strtotime($request->start_time)),
            'duration' => 'required|digits_between:1,3',
            'setMaxParticipants' => 'required|numeric|min:1',
            'welcomemsg' => 'max:250',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
        ], [
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
            "main_category.required_without" => __("Country name is required"),
            "scnd_category_id.required_with" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "sub_category.required_with" => __("Institute name is required"),
            "sub_category.exists" => __("The selected Institute name is not exist"),
            "ch_sub_category.required_with" => __("Major name is required"),
            "ch_sub_category.exists" => __("The selected major name is not exist"),
            "title.required" => __("In-person session name is required"),
            "title.max" => __("In-person session name should not be more than 100 characters"),
            "detail.required" => __("In-person session detail is required"),
            "instructor_id.required" => __("Instructor name is required"),
            "start_time.required" => __("Start time is required"),
            "start_time.date_format" => __("Start time format must be YYYY-MM-DD hh:mm am/pm"),
            "start_time.after_or_equal" => __("Start time must be greater than or equal to current datetime"),
            "expire_date.required" => __("Session expire date is required"),
            "expire_date.date_format" => __("Expire date format must be YYYY-MM-DD"),
            "expire_date.after_or_equal" => __("Expire date must be greater than or equal to presentation start date"),
            "duration.required" => __("In-person session duration is required"),
            "duration.digits_between" => __("Duration should not be more than 3 digits"),
            "price.required" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
            "price.min" => __("Price should not be a negitive number"),
            "discount_price.required" => __("Discount Price is required"),
            "discount_price.numeric" => __("Discount Price must be in numeric"),
            "discount_price.min" => __("Discount Price should not be a negitive number"),
            "setMaxParticipants.required" => __("Maximum participants field is required"),
            "setMaxParticipants.numeric" => __("Maximum participants must be in numeric"),
            "welcomemsg.max" => __("Welcome messsage should not be more than 250 characters"),
        ]);

        $offlineSession = OfflineSession::findOrFail($id);
        $input = $request->all();

        $sessionChapter = CourseChapter::where('course_id', $offlineSession->course_id)
                                        ->where(['type' => 'in-person-session', 'type_id' => $offlineSession->id])
                                        ->first();

        if ($offlineSession->course_id != $request->course_id && ($sessionChapter)) {
            return back()->with('warning', __("You can't unlink In-Person Session from the course because it's added in the Course Chapter"));
        }

        if ($request->setMaxParticipants == '') {
            $input['setMaxParticipants'] = '-1';
        }

        if (isset($request->link_by)) {
            $input['link_by'] = 'course';
            $input['course_id'] = $request['course_id'];

            $course = Course::find($request->course_id);
            $input['main_category'] = $course->category_id;
            $input['scnd_category_id'] = $course->scnd_category_id;
            $input['sub_category'] = $course->subcategory_id;
            $input['ch_sub_category'] = $course->childcategory_id;
        } else {
            $input['link_by'] = null;
            $input['course_id'] = null;
        }

        if ($file = $request->file('image')) {
            if ($offlineSession->image != null) {
                $content = @file_get_contents(public_path() . '/images/offlinesession/' . $offlineSession->image);
                if ($content) {
                    unlink(public_path() . '/images/offlinesession/' . $offlineSession->image);
                }
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path('/images/offlinesession/');
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['image'] = $image;
        }

        // $input['start_time'] = Carbon::parse($request->start_time)->toRfc3339String();
        $input['start_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->start_time, auth()->user()->timezone);
        $input['start_time']->setTimezone('UTC');

        Cart::where(['offline_session_id' => $id, 'installment' => '0'])
                ->update([
                    'price' => $request->price,
                    'offer_price' => $request->discount_price,
        ]);

        $offlineSession->update($input);

        \App\Order::where('offline_session_id', $offlineSession->id)->update([
            'enroll_start' => date('Y-m-d', strtotime($offlineSession->start_time)),
            'enroll_expire' => date('Y-m-d', strtotime($offlineSession->start_time)),
            'updated_at' => DB::raw('updated_at')
        ]);
        return redirect()->route('offline.sessions.index')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function enrolledUser($sessionid)
    {
        $session = OfflineSession::find($sessionid);

        $enrolled = SessionEnrollment::query()
                    ->where('offline_session_id', $sessionid)
                    ->with('user:id,fname,lname,mobile,email')
                    ->latest('id')
                    ->get();

        return view('admin.offlinesession.enrolled.users', compact('session', 'enrolled'));
    }


    public function destroy(OfflineSession $session)
    {
        $orders = Order::where('offline_session_id', $session->id)->allActiveInactiveOrder()->get();
        $sessionEnrollments = SessionEnrollment::where('offline_session_id', $session->id)->get();

        if ($orders->isNotEmpty() || $sessionEnrollments->isNotEmpty()) {
            return back()->with('delete', trans('flash.SessionCannotDelete'));
        } elseif (isset($session)) {
            Wishlist::where('offline_session_id', $session->id)->delete();
            Cart::where('offline_session_id', $session->id)->delete();
            $session->delete();
            return back()->with('delete', trans('flash.DeletedSuccessfully'));
        } else {
            return back()->with('delete', __('In-person session not found !'));
        }
    }
}