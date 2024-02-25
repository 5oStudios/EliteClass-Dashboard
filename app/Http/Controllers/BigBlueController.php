<?php

namespace App\Http\Controllers;

use App\BBL;
use Session;
use App\Cart;
use App\User;
use App\Order;
use App\Course;
use App\Setting;
use App\Wishlist;
use Notification;
use Carbon\Carbon;
use App\Attandance;
use App\Categories;
use App\SubCategory;
use App\CourseClass;
use App\ChildCategory;
use App\CourseChapter;
use App\secondaryCategory;
use App\SessionEnrollment;
use Illuminate\Http\Request;
use BigBlueButton\BigBlueButton;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\LiveStreamingStart;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;

class BigBlueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:meetings.big-blue.view', ['only' => ['index']]);
        $this->middleware('permission:meetings.big-blue.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:meetings.big-blue.edit', ['only' => ['edit', 'update', 'status']]);
        $this->middleware('permission:meetings.big-blue.delete', ['only' => ['destroy', 'delete']]);
        $this->middleware('permission:meetings.big-blue.recorded', ['only' => ['getrecordings']]);
        $this->middleware('permission:meetings.big-blue.settings', ['only' => ['setting']]);
    }


    public function index(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $meetings = BBL::query()
                // where('is_ended', '!=', 1)
                // ->whereDate('start_time', '>=', Carbon::now())
                ->latest()
                ->with(['chapters']);
        } else if (Auth::user()->hasRole('instructor')) {
            $meetings = BBL::query()
                ->where('instructor_id', Auth::id())
                // ->where('is_ended', '!=', 1)->whereDate('start_time', '>=', Carbon::now())
                ->latest()
                ->with(['chapters']);
        }

        if ($request->ajax()) {
            return DataTables::of($meetings)
                ->addColumn('checkbox', function ($row) {
                    $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->addIndexColumn()
                ->editColumn('image', 'bbl.datatables.image')
                ->editColumn('meetingID', function ($row) {

                    return $row->meetingid ?? '';
                })
                ->editColumn('detail', 'bbl.datatables.detail')
                ->editColumn('action', 'bbl.datatables.action')
                ->rawColumns(['checkbox', 'image', 'meetingID', 'detail', 'action'])
                ->make(true);
        }

        return view('bbl.index');
    }


    public function create()
    {
        if (Auth::user()->role == "admin") {
            $course = Course::with('installments')
                ->active()
                ->get();
            $users = User::query()
                ->where('id', '!=', Auth::user()->id)
                ->where('role', '!=', 'user')
                ->active()
                ->get();
        } else {
            $course = Course::with('installments')
                ->where('user_id', Auth::user()->id)
                ->active()
                ->get();
            $users = User::query()
                ->where('id', Auth::user()->id)
                ->active()
                ->first();
        }

        $category = Categories::where('status', 1)->get();
        return view('bbl.create', compact('category', 'users', 'course'));
    }


    public function edit($meetingid)
    {
        $meeting = BBL::findOrFail($meetingid);

        if (Auth::user()->role == "admin") {
            $course = Course::with('installments')->get();
            $users = User::query()
                ->where('id', '!=', Auth::user()->id)
                ->where('role', '!=', 'user')
                ->active()
                ->get();
        } else {
            $course = Course::with('installments')->where('user_id', Auth::user()->id)->get();
            $users = User::where('id', Auth::user()->id)->first();
        }

        $category = Categories::where('status', 1)->get();
        $typecategory = secondaryCategory::where('status', 1)->where('category_id', $meeting->main_category)->get();
        $subcategory = SubCategory::where('status', 1)->where(['category_id' => $meeting->main_category, 'scnd_category_id' => $meeting->scnd_category_id])->get();
        $childcategory = ChildCategory::where('status', 1)->where(['category_id' => $meeting->main_category, 'scnd_category_id' => $meeting->scnd_category_id, 'subcategory_id' => $meeting->sub_category])->get();

        return view('bbl.edit', compact('category', 'users', 'course', 'meeting', 'category', 'typecategory', 'subcategory', 'childcategory'));
    }


    public function store(Request $request)
    {

        $date = Carbon::createFromFormat('Y-m-d H:i:s', now(), 'UTC');
        $date->setTimezone(Auth::user()->timezone);

        $request->validate([
            'main_category' => 'required_without:link_by',
            'scnd_category_id' => 'required_with:main_category|exists:secondary_categories,id',
            'sub_category' => 'required_with:scnd_category_id|exists:sub_categories,id',
            'ch_sub_category' => 'required_with:sub_category|array|exists:child_categories,id',
            'meetingname' => 'required|max:100',
            'meetingid' => 'required|regex:/^[a-zA-Z0-9]+$/|unique:bigbluemeetings,meetingid|max:30',
            'detail' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
            'instructor_id' => 'required',
            'start_time' => 'required|date_format:Y-m-d h:i a|after_or_equal:' . $date->format('Y-m-d h:i a'),
            'expire_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d', strtotime($request->start_time)),
            'duration' => 'required|digits_between:1,3',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'sometimes|numeric|min:0',
            'discount_type' => 'sometimes|string|in:fixed,percentage',
            'setMaxParticipants' => 'required|numeric|min:1',
            'welcomemsg' => 'max:250',
            'unlock_installment' => 'sometimes|integer|min:1|max:5'
        ], [
            "main_category.required_without" => __("Country name is required"),
            "scnd_category_id.required_with" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "sub_category.required_with" => __("Institute name is required"),
            "sub_category.exists" => __("The selected Institute name is not exist"),
            "ch_sub_category.required_with" => __("Major name is required"),
            "ch_sub_category.exists" => __("The selected major name is not exist"),
            "meetingname.required" => __("Live Streaming name is required"),
            "meetingname.max" => __("Live Streaming name should not be more than 20 characters"),
            "meetingid.required" => __("Live Streaming ID is required"),
            "meetingid.regex" => __("Live Streaming ID must be alpha-numeric characters"),
            "meetingid.unique" => __("This Live Streaming ID is already exist, try to add different live streaming ID"),
            "meetingid.max" => __("Live Streaming ID should not be more than 30 characters"),
            "detail.required" => __("Live Strreaming detail is required"),
            'image.required' => __('Image is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
            "instructor_id.required" => __("Instructor name is required"),
            "start_time.required" => __("Start time is required"),
            "start_time.date_format" => __("Start time format must be YYYY-MM-DD hh:mm am/pm"),
            "start_time.after_or_equal" => __("Start datetime must be greater than or equal to selected timezone (" . Auth::user()->timezone . ") datetime i.e. " . $date->format('Y-m-d h:i a')),
            "expire_date.required" => __("Session expire date is required"),
            "expire_date.date_format" => __("Expire date format must be YYYY-MM-DD"),
            "expire_date.after_or_equal" => __("Expire date must be greater than or equal to presentation start date"),
            "duration.required" => __("Live Streaming duration is required"),
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

        $input = $request->all();

        $allmeeting = BBL::query()
            ->active()
            ->get();

        foreach ($allmeeting as $key => $met) {
            if ($request->meetingid == $met->meetingid) {
                return back()->with('delete', __('Live Streaming is already active with this name !'))->withInput();
            }
        }

        // if($request->modpw == $request->attendeepw){
        //     return back()->with('delete','Attandee password and moderator password cannot be same !')->withInput();
        // }

        $input['modpw'] = config('app.modpw');
        $input['attendeepw'] = config('app.attendeepw');

        if (isset($request->setMuteOnStart)) {
            $input['setMuteOnStart'] = 1;
        } else {
            $input['setMuteOnStart'] = 0;
        }

        if (isset($request->allow_record) && $request->allow_record == 'on') {
            $input['allow_record'] = 1;
        } else {
            $input['allow_record'] = 0;
        }

        if ($request->setMaxParticipants == '') {
            $input['setMaxParticipants'] = '-1';
        }

        if (isset($request->disable_chat)) {
            $input['disable_chat'] = 1;
        } else {
            $input['disable_chat'] = 0;
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
            $path = 'images/bg/';

            if (!file_exists(public_path() . '/' . $path)) {
                File::makeDirectory(public_path() . '/' . $path, 0777, true);
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/bg/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['image'] = $image;
        }

        // $input['start_time'] = Carbon::parse($request->start_time)->toRfc3339String();
        $input['start_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->start_time, Auth::user()->timezone);
        $input['start_time']->setTimezone('UTC');

        $input['owner_id'] = Auth::user()->id;


        if (!isset($input['discount_price']) || $input['discount_price'] == 0) {
            $input['discount_price'] = null;
            $input['discount_type'] = null;
        }

        $newmeeting = BBL::create($input);

        //create the meeting as a chapter in the course
        if ($input['link_by'] == 'course') {
            $chapter = CourseChapter::create([
                'course_id' => $input['course_id'],
                'chapter_name' => $request->meetingname,
                'detail' => $request->detail,
                'price' => $request->price,
                'type' => 'live-streaming',
                'type_id' => $newmeeting->id,
                'discount_price' => $request->discount_price,
                'user_id' => $request->instructor_id,
                'position' => (CourseChapter::count() + 1),
                'status' => 1,
                'unlock_installment' => $request->unlock_installment ?? null
            ]);

            // $courseclass = new CourseClass();
            // $courseclass->course_id = $input['course_id'];
            // $courseclass->coursechapter_id = $chapter->id;
            // $courseclass->title = $request->meetingname;
            // $courseclass->status = 1;
            // $courseclass->user_id = $request->instructor_id;
            // $courseclass->position = (CourseClass::where('course_id', $input['course_id'])->count() + 1);
            // $courseclass->type = 'Meeting';
            // $courseclass->duration = $input['duration'];
            // $courseclass->meeting_id = $input['meetingid'];
            // $courseclass->save();
        }

        return redirect()->route('bbl.all.meeting')->with('success', trans('flash.CreatedSuccessfully'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'main_category' => 'required_without:link_by',
            'scnd_category_id' => 'required_with:main_category',
            'sub_category' => 'required_with:scnd_category_id',
            'ch_sub_category' => 'required_with:sub_category|array',
            'meetingname' => 'required|max:100',
            'meetingid' => 'required|regex:/^[a-zA-Z0-9]+$/|max:30|unique:bigbluemeetings,meetingid,' . $id,
            'detail' => 'required',
            'image' => 'mimes:jpg,jpeg,png|max:10240',
            'instructor_id' => 'required',
            'start_time' => 'required|date_format:Y-m-d h:i a',
            'expire_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d', strtotime($request->start_time)),
            'duration' => 'required|digits_between:1,3',
            'setMaxParticipants' => 'required|numeric|min:1',
            'welcomemsg' => 'max:250',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'sometimes|numeric|min:0',
            'discount_type' => 'sometimes|string|in:fixed,percentage',
            'unlock_installment' => 'sometimes|integer|min:1|max:5'
        ], [
            "main_category.required_without" => __("Country name is required"),
            "scnd_category_id.required_with" => __("Type of institute field is required"),
            "scnd_category_id.exists" => __("The selected Type of institute is not exist"),
            "sub_category.required_with" => __("Institute name is required"),
            "sub_category.exists" => __("The selected Institute name is not exist"),
            "ch_sub_category.required_with" => __("Major name is required"),
            "ch_sub_category.exists" => __("The selected major name is not exist"),
            "meetingname.required" => __("Live Streaming name is required"),
            "meetingname.max" => __("Live Streaming name should not be more than 20 characters"),
            "meetingid.required" => __("Live Streaming ID is required"),
            "meetingid.regex" => __("Live Streaming ID must be alpha-numeric characters"),
            "meetingid.unique" => __("This Live Streaming ID is already exist, try to add different live streaming ID"),
            "meetingid.max" => __("Live Streaming ID should not be more than 30 characters"),
            "detail.required" => __("Live Streaming detail is required"),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
            "instructor_id.required" => __("Instructor name is required"),
            "start_time.required" => __("Start time is required"),
            "start_time.date_format" => __("Start time format must be YYYY-MM-DD hh:mm am/pm"),
            "start_time.after_or_equal" => __("Start time must be greater than or equal to today's date"),
            "expire_date.required" => __("Session expire date is required"),
            "expire_date.date_format" => __("Expire date format must be YYYY-MM-DD"),
            "expire_date.after_or_equal" => __("Expire date must be greater than or equal to presentation start date"),
            "duration.required" => __("Live Streaming duration is required"),
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


        $meeting = BBL::findOrFail($id);
        $input = $request->all();

        $streamingChapter = CourseChapter::where('course_id', $meeting->course_id)
            ->where(['type' => 'live-streaming', 'type_id' => $meeting->id])
            ->first();

        if ($meeting->course_id != $request->course_id && ($streamingChapter)) {
            return back()->with('warning', __("You can't unlink Live Streaming from the course because it's added in the Course Chapter"));
        }

        // if($request->modpw == $request->attendeepw){
        //     return back()->with('delete','Attandee password and moderator password cannot be same !')->withInput();
        // }

        if (isset($request->setMuteOnStart)) {
            $input['setMuteOnStart'] = 1;
        } else {
            $input['setMuteOnStart'] = 0;
        }

        if (isset($request->allow_record) && $request->allow_record == 'on') {
            $input['allow_record'] = 1;
        } else {
            $input['allow_record'] = 0;
        }


        if ($request->setMaxParticipants == '') {
            $input['setMaxParticipants'] = '-1';
        }

        if (isset($request->disable_chat)) {
            $input['disable_chat'] = 1;
        } else {
            $input['disable_chat'] = 0;
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
            if ($meeting->image != null) {
                $content = @file_get_contents(public_path() . '/images/bg/' . $meeting->image);
                if ($content) {
                    unlink(public_path() . '/images/bg/' . $meeting->image);
                }
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path('/images/bg/');
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['image'] = $image;
        }

        // $input['start_time'] = Carbon::parse($request->start_time)->toRfc3339String();
        $input['start_time'] = Carbon::createFromFormat('Y-m-d H:i a', $request->start_time, Auth::user()->timezone);
        $input['start_time']->setTimezone('UTC');

        Cart::where(['meeting_id' => $id, 'installment' => '0'])
            ->update([
                'price' => $request->price,
                'offer_price' => $request->discount_price,
            ]);

        if (!isset($input['discount_price']) || $input['discount_price'] == 0) {
            $input['discount_price'] = null;
            $input['discount_type'] = null;
        }

        $meeting->update($input);

        \App\Order::where('meeting_id', $id)->update([
            'enroll_start' => date('Y-m-d', strtotime($meeting->start_time)),
            'enroll_expire' => date('Y-m-d', strtotime($meeting->start_time)),
            'updated_at' => DB::raw('updated_at')
        ]);

        return redirect()->route('bbl.all.meeting')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function enrolledUser($meetingid)
    {
        $meeting = BBL::findOrFail($meetingid);

        $enrolled = SessionEnrollment::query()
            ->where('meeting_id', $meetingid)
            ->whereHas('user', function ($query) {
                $query->exceptTestuser();
            })
            ->with('user:id,fname,lname,mobile,email')
            ->latest('id')
            ->get();

        return view('bbl.enrolled.users', compact('meeting', 'enrolled'));
    }


    public function delete($meetingid)
    {
        $meeting = BBL::findOrFail($meetingid);
        $orders = Order::where('meeting_id', $meetingid)->allActiveInactiveOrder()->get();
        $sessionEnrollments = SessionEnrollment::where('meeting_id', $meetingid)->get();

        if ($orders->isNotEmpty() || $sessionEnrollments->isNotEmpty()) {
            return back()->with('delete', trans('flash.MeetingCannotDelete'));
        } elseif (isset($meeting)) {
            Wishlist::where('meeting_id', $meetingid)->delete();
            Cart::where('meeting_id', $meetingid)->delete();
            $meeting->delete();
            return back()->with('delete', trans('flash.DeletedSuccessfully'));
        } else {
            return back()->with('delete', __('Live Streaming not found !'));
        }
    }


    public function setting(Request $request)
    {
        $env_update = $this->changeEnv([
            'BBB_SECURITY_SALT' => $request->BBB_SECURITY_SALT,
            'BBB_SERVER_BASE_URL' => $request->BBB_SERVER_BASE_URL
        ]);

        if ($env_update) {
            return back()->with('success', __('Settings Updated Successfully !'));
        } else {
            return back()->with('deleted', __('Oops ! Please try again'));
        }
    }


    public function apiCreate($id)
    {
        $bbb = new BigBlueButton();
        $m = BBL::find($id);

        $userid = Crypt::encrypt(Auth::user()->id);
        $meetingid = Crypt::encrypt($m->meetingid);
        $urlLogout = url('/bigblue/api/callback?meetingID=' . $meetingid . '&user=' . $userid);
        $RecordingReadyCallbackUrl = url('/bigblue/api/recordingcallback?meetingID=' . $meetingid . '&user=' . $userid);
        $createMeetingParams = new CreateMeetingParameters($m->meetingid, $m->meetingname);
        $createMeetingParams->setAttendeePassword($m->attendeepw);
        $createMeetingParams->setModeratorPassword($m->modpw);
        $createMeetingParams->setDuration($m->duration);
        $createMeetingParams->setMaxParticipants($m->setMaxParticipants);
        $createMeetingParams->setMuteOnStart($m->setMuteOnStart == 0 ? false : true);
        $createMeetingParams->setCopyright(date('Y') . ' | ' . config('app.name'));
        $createMeetingParams->setAllowModsToEjectCameras(true);
        $createMeetingParams->setBreakoutRoomsEnabled(true);
        $createMeetingParams->setBreakoutRoomsRecord(true);
        $createMeetingParams->setBreakoutRoomsPrivateChatEnabled(true);
        $createMeetingParams->setMeetingLayout("Smart Layout");

        if ($m->welcomemsg != '') {
            $createMeetingParams->setWelcomeMessage($m->welcomemsg);
        }

        $createMeetingParams->setWebcamsOnlyForModerator(true);

        $createMeetingParams->setRecord($m->allow_record == 0 ? false : true);
        $createMeetingParams->setAllowStartStopRecording($m->allow_record == 0 ? false : true);
        $createMeetingParams->setAutoStartRecording($m->allow_record == 0 ? false : true);
        $createMeetingParams->setLogoutUrl($urlLogout);
        $createMeetingParams->setEndCallbackUrl($urlLogout);
        $createMeetingParams->setRecordingReadyCallbackUrl($RecordingReadyCallbackUrl);

        $response = $bbb->createMeeting($createMeetingParams);
        if ($response->getReturnCode() == 'FAILED') {
            return __("Can't create room! please contact our administrator");
        } else {
            $joinMeetingParams = new JoinMeetingParameters($m->meetingid, $m->meetingname, $m->modpw);
            // $joinMeetingParams->setUsername($m->presen_name);
            $joinMeetingParams->setUsername(Auth::user()->fname . ' ' . Auth::user()->lname);
            $joinMeetingParams->setRedirect(true);
            $url = $bbb->getJoinMeetingURL($joinMeetingParams);
            $m->is_started = date('Y-m-d H:i:s');
            $m->save();
            //TODO: Fix the notifications ASAP
            //https://github.com/berkayk/laravel-onesignal/issues/134#issuecomment-692845175
            //Notification::send($m->attendee(), new LiveStreamingStart($m));
            return redirect($url);
        }
    }


    public function logout(Request $request)
    {
        $userid = Crypt::decrypt($request->user);
        $meetingid = Crypt::decrypt($request->meetingID);

        Log::debug("BigblueMeeting Logout ==> \nDecrpytUserId: $request->user, userid: $userid\nDecryptMeetingId: $request->meetingID, meetingid: $meetingid");

        $findmeeting = BBL::where('meetingid', $meetingid)->first();

        if (isset($findmeeting)) {
            Log::debug("BigblueMeeting found successfully");

            $userid = Cookie::get('user_selection');
            $user = User::find($userid);

            if (isset($user)) {
                $login = $user->id;
            } else {
                $login = Auth::check() ? Auth::user()->id : null;
            }

            if ($findmeeting->instructor_id == $login) {
                $findmeeting->is_ended = 1;
                $findmeeting->reco_status = 1;
                $findmeeting->save();

                $bbb = new BigBlueButton();

                $endMeetingParams = new EndMeetingParameters($findmeeting->meetingid, $findmeeting->modpw);

                $response = $bbb->endMeeting($endMeetingParams);
                if (Auth::check() && Auth::user()->role == "admin") {
                    return redirect()->route('admin.index')->with('success', __('Live Streaming ended successfully !'));
                } elseif (Auth::check() && Auth::user()->role == "instructor") {
                    return redirect()->route('instructor.index')->with('success', __('Live Streaming ended successfully !'));
                }
            } else {
                if (Auth::check() && Auth::user()->role == "admin") {
                    $findmeeting->is_ended = 1;
                    $findmeeting->reco_status = 1;
                    $findmeeting->save();
                    $bbb = new BigBlueButton();

                    $endMeetingParams = new EndMeetingParameters($findmeeting->meetingid, $findmeeting->modpw);
                    $response = $bbb->endMeeting($endMeetingParams);
                    return redirect()->route('admin.index');
                } else {
                    $url = config('app.front-end-url') . '/live-sessions/' . $findmeeting->id . '?userLeftLiveSession=1&message=You%20logout%20from%20live%20streaming%20successfully';
                    return Redirect::to($url);
                }
            }
        } else {
            Log::debug("BigblueMeeting failed to find");
            return redirect('/')->with('delete', __('No Live Streaming exist with this id'));
        }
    }


    public function recordingReady(Request $request)
    {
        $meetingid = Crypt::decrypt($request->meetingID);
        $findmeeting = BBL::where('meetingid', '=', $meetingid)->first();

        if (isset($findmeeting)) {
            $findmeeting->reco_status = 2;
            $findmeeting->save();
        }
    }


    public function joinview($meetingid)
    {
        $m = BBL::where('meetingid', $meetingid)->first();
        if ($m) {
            return view('bbl.joinmeeting', compact('m'));
        } else {
            return back()->with('deleted', __('404 Live Streaming Not found !'));
        }
    }


    public function apiJoin(Request $request)
    {
        $bbb = new BigBlueButton();
        $m = BBL::where('meetingid', $request->meetingid)->first();

        if ($m) {
            if ($request->meetingid != $m->meetingid) {
                return back()->with('delete', __('The Live Streaming ID that you supplied did not match any existing live streamings'))->withInput($request->except('password'));
            }

            if ($request->password != $m->attendeepw) {
                return back()->with('delete', __('Invalid password Please try again !'))->withInput($request->except('password'));
            }

            if ($m->is_ended == 1) {
                return back()->with('delete', __('Live Streaming is already ended !'))->withInput($request->except('password'));
            }

            $gsetting = Setting::first();

            if ($gsetting->attandance_enable == 1) {
                $date = Carbon::now();
                //Get date
                $date->toDateString();

                $courseAttandance = Attandance::where('user_id', Auth::user()->id)->where('date', '=', $date->toDateString())->first();

                if (!$courseAttandance) {
                    $attanded = Attandance::create([
                        'user_id' => Auth::user()->id,
                        'bbl_id' => $m->id,
                        'instructor_id' => $m->instructor_id,
                        'date' => $date->toDateString(),
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]);
                }
            }



            $joinMeetingParams = new JoinMeetingParameters($m->meetingid, $m->meetingname, $request->password);
            $joinMeetingParams->setUsername($request->name);
            $joinMeetingParams->setRedirect(true);
            $url = $bbb->getJoinMeetingURL($joinMeetingParams);

            Cookie::queue('user_selection', Auth::user()->id, 100);
            return redirect($url);
        } else {
            return back()->with('delete', __('Live Streaming not found !'));
        }
    }


    public function detailpage(Request $request, $id)
    {
        $bbl = BBL::where('id', $id)->where('is_ended', '!=', 1)->first();
        if (!$bbl) {
            return redirect('/')->with('delete', __('Live Streamign is ended !'));
        }
        return view('front.bbl_detail', compact('bbl'));
    }


    public function getrecordings(Request $request)
    {
        if (env('BBB_SECURITY_SALT') != null && env('BBB_SERVER_BASE_URL') != null) {
            $recordingParams = new GetRecordingsParameters();
            //$recordingParams->setMeetingId('fztrain-30-06-2022');
            $bbb = new BigBlueButton();
            $response = $bbb->getRecordings($recordingParams);
            if ($response->getReturnCode() == 'SUCCESS') {
                foreach ($response->getRawXml()->recordings as $recording) {
                    $all_recordings = $recording;
                }
            } else {
                return view('bbl.setting')->with('delete', __('Recordings not found !'));
            }

            foreach ($all_recordings->recording as $meeting) {
                $exist = BBL::where('meetingid', $meeting->meetingID)->first();
                if ($exist) {
                    $existChapter = CourseChapter::where('type_id', $exist->id)->first();
                }
                if ($existChapter) {
                    $meeting->course = $existChapter->course_id;
                } else {
                    $meeting->course = -1;
                }
            }
            return view('bbl.recordings', compact('all_recordings'));
        }

        return view('bbl.setting')->with('delete', __('Update your settings !'));
    }

    protected function changeEnv($data = array())
    {
        if (count($data) > 0) {
            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);
            ;

            // Loop through given data
            foreach ((array) $data as $key => $value) {
                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {
                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }

    public function getUnlinkedRecordings(Request $request)
    {
        if (env('BBB_SECURITY_SALT') != null && env('BBB_SERVER_BASE_URL') != null) {
            $recordingParams = new GetRecordingsParameters();
            //$recordingParams->setMeetingId('fztrain-30-06-2022');
            $bbb = new BigBlueButton();
            $response = $bbb->getRecordings($recordingParams);
            if ($response->getReturnCode() == 'SUCCESS') {
                foreach ($response->getRawXml()->recordings as $recording) {
                    $all_recordings = $recording;
                }
            } else {
                return view('bbl.setting')->with('delete', __('Recordings not found !'));
            }
            $unlinkedRecordings = [];
            foreach ($all_recordings->recording as $meeting) {
                $exist = BBL::where('meetingid', $meeting->meetingID)->first();
                if ($exist) {
                    $existChapter = CourseChapter::where('type_id', $exist->id)->first();
                }
                if (!$existChapter) {
                    $unlinkedRecordings[] = $meeting;
                }
            }
            return view('bbl.unlinkedRecordings', compact('unlinkedRecordings'));
        }

        return view('bbl.setting')->with('delete', __('Update your settings !'));
    }

    public function linkRecordingsToCourse($meeting_id)
    {
        if (Auth::user()->role == "admin") {
            $course = Course::with('installments')
                ->active()
                ->get();
        } else {
            $course = Course::with('installments')
                ->where('user_id', Auth::user()->id)
                ->active()
                ->get();
        }

        return view('bbl.linkToCourse', compact('course', 'meeting_id'));
    }


    public function linkRecordingToCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'meeting_id' => 'required|exists:bigbluemeetings,meetingid',
            'price' => 'sometimes|numeric',
            // 'discount_price' => 'sometimes|numeric',
            // 'discount_type' => 'sometimes|string|in:fixed,percentage',
        ]);

        $meeting = BBL::where('meetingid', $request->meeting_id)->first();

        CourseChapter::create([
            'course_id' => $request->course_id,
            'price' => $request->price ?? 0,
            'discount_price' => $request->price ?? 0,
            'type' => 'live-streaming',
            'status' => 1,
            'type_id' => $meeting->id,
            'user_id' => $meeting->instructor_id,
            'position' => (CourseChapter::count() + 1),
            'chapter_name' => $meeting->meetingname,
            'detail' => $meeting->detail,
            'unlock_installment' => $request->unlock_installment ?? null,
        ]);

        return view('bbl.unlinkedRecordings')->with('success', trans('flash.CreatedSuccessfully'));
    }


}
