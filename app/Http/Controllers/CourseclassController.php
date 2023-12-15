<?php

namespace App\Http\Controllers;

use Auth;
use File;
use App\BBL;
use App\User;
use App\Order;
use Exception;
use App\Course;
use App\Subtitle;
use Notification;
use App\QuizTopic;
use App\CourseClass;
use App\Installment;
use App\CourseChapter;
use App\CourseProgress;
use App\OfflineSession;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\CourseNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\PostTooLargeException;

class CourseclassController extends Controller
{
    private $files = [
        'pdf' => 'pdf',
        'zip' => 'zip',
        'rar' => 'rar',
        'word' => 'doc,docx',
        'excel' => 'xls,xlsx',
        'powerpoint' => 'ppt,pptx',
        'text' => '',
        'video' => '',
        'quiz' => '',
    ];

    public function __construct()
    {
        $this->middleware('permission:course-class.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:course-class.create', ['only' => ['store', 'uploadVideoToVimeo', 'uploadVideoToBunnyCDN', 'uploadVideoToBunnyCDNzk', 'sort']]);
        $this->middleware('permission:course-class.edit', ['only' => ['update', 'courseclassstatus', 'sort']]);
        $this->middleware('permission:course-class.delete', ['only' => ['destroy', 'bulk_delete']]);
    }


    public function uploadVideoToVimeo()
    {
        return view('admin.course.courseclass.uploadVideoToVimeo');
    }


    public function uploadVideoToBunnyCDN()
    {
        return view('admin.course.courseclass.uploadVideoToBunnyCDN');
    }


    public function uploadVideoToBunnyCDNzk()
    {
        return view('admin.course.courseclass.uploadVideoToBunnyCDNzk');
    }


    public function index(Request $request, $id)
    {
        $chap = CourseChapter::findOrFail($id);

        $sum = $chap->courseclass->sum('duration');
        foreach ($chap->courseclass as $courseclass) {
            if ($courseclass->meeting) {
                $sum += $courseclass->meeting->duration;
            } elseif ($courseclass->quiz) {
                $sum += $courseclass->quiz->timer;
            }
        }
        $duration = round(($sum / 60), 2);

        $courseclasses = CourseClass::select('id', 'course_id', 'coursechapter_id', 'meeting_id', 'offline_session_id', 'url', 'title', 'position', 'type', 'status')
                                        ->where('coursechapter_id', $id)
                                        ->with('coursechapters:id,chapter_name')
                                        ->orderBy('position', 'ASC');

        if ($request->ajax()) {
            return DataTables::of($courseclasses)
                ->setRowClass('sortable row1')
                ->setRowAttr([
                    'data-id' => '{{$id}}',
                ])
                ->addColumn('checkbox', function ($row) {

                    $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->addIndexColumn()
                ->editColumn('coursechapter', function ($row) {

                    return $row->coursechapters->chapter_name ?? '';
                })
                ->editColumn('type', function ($row) {

                    return $row->type ?? '';
                })
                ->editColumn('title', function ($row) {
                    
                    if ($row->type == 'quiz')
                        return $row->quiz->title;
                    elseif($row->type == 'meeting')
                        return $row->meeting->meetingname;
                    elseif($row->type == 'offline_session')
                        return $row->offlinesession->title;
                    else
                        return $row->title;
                })
                ->editColumn('status', function ($row) {
                    return $row->status ?? '';
                })
                ->editColumn('status', 'admin.course.courseclass.datatables.status')
                ->editColumn('action', 'admin.course.courseclass.datatables.action')
                ->orderColumn('position', 'desc')
                ->rawColumns(['checkbox', 'coursechapter', 'type', 'title', 'status', 'action'])
                ->make(true);
        }

        return view('admin.course.courseclass.index', compact(['duration','chap']));
    }


    public function create($id, $chap_id)
    {
        $cor = Course::findOrFail($id);
        $coursechapt = CourseChapter::findOrFail($chap_id);
        $installments = Installment::where('course_id', '=', $id)->get();
        $topics = QuizTopic::where('course_id', '=', $id)->get();
        $classquizes = QuizTopic::where('course_id', '=', $id)->doesnthave('courseclass')->get();
        $bbl_meetings = BBL::where('link_by', '!=', null)->where('is_ended', '!=', 1)->where('course_id', $id)->get();
        $offline_sessions = OfflineSession::where('link_by', '!=', null)->where('is_ended', '!=', 1)->where('course_id', $id)->get();

        return view('admin.course.courseclass.add', compact('installments', 'cor', 'coursechapt', 'classquizes', 'bbl_meetings', 'offline_sessions', 'chap_id'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'course_chapters' => 'required',
            'title' => 'required_if:type,text,pdf,video,zip,rar,word,excel,powerpoint|max:100',
            'type' => 'required|in:text,video,quiz,pdf,zip,rar,word,excel,powerpoint',
            'file' => 'required_if:type,pdf,zip,rar,word,excel,powerpoint|file|mimes:' . $this->files[$request->type] . '|max:102400',
            'long_text' => 'required_if:type,text',
            // 'meeting_id'=> 'required_if:type,meeting',
            // 'offline_session_id'=> 'required_if:type,offline_session',
            'iframe_url' => 'required_if:type,video',
            'url' => 'required_if:type,quiz',
            'duration' => 'required_if:type,video,text,pdf,zip,rar,word,excel,powerpoint|nullable|digits_between:1,3',
            // 'video' => 'mimes:mp4,avi,wmv',
        ], [
            'course_chapters' => __('Chapter name is required'),
            'title.required_if' => __('Title is required'),
            'title.max' => __('Title should not be more than 100 characters'),
            'type.required' => __('Type is required'),
            'file.required_if' => __('File is required'),
            'file.mimes' => __('File should be ' . $this->files[$request->type]),
            'file.max' => __('File size should not be more than 100MB'),
            'long_text.required_if' => __('Text is required'),
            // 'meeting_id.required_if' => __('Live Streaming selection is required'),
            // 'offline_session_id.required_if' => __('Offline Session selection is required'),
            'iframe_url.required_if' => __('Content is required'),
            'video_url.required_if' => __('Iframe video URL is required'),
            'url.required_if' => __('Quiz selection is required'),
            'duration.required_if' => __('Duration is required'),
            "duration.digits_between" => __('Duration should not be more than 3 digits'),
        ]);

        set_time_limit(0);
        ini_set('max_execution_time', 400);
        ini_set('memory_limit', '-1');

        $courseclass = new CourseClass();
        $courseclass->course_id = $request->course_id;
        $courseclass->coursechapter_id = $request->course_chapters;
        $courseclass->title = $request->title;
        $courseclass->duration = $request->duration;

        $courseclass->status = isset($request->status) ? '1' : '0';
        $courseclass->downloadable = isset($request->downloadable) ? '1' : '0';
        $courseclass->printable = isset($request->printable) ? '1' : '0';
        // $courseclass->status = $request->status;
        // $courseclass->featured = $request->featured;
        // $courseclass->video = $request->video;
        // $courseclass->image = $request->image;
        // $courseclass->zip = $request->zip;
        // $courseclass->pdf = $request->pdf;
        // $courseclass->size = $request->size;
        // $courseclass->url = $request->url;
        // $courseclass->detail = $request->detail;
        // $courseclass->date_time = $request->date_time;
        // $courseclass->unlock_installment = $request->unlock_installment;

        $courseclass->user_id = auth()->user()->id;

        $courseclass->position = (CourseClass::where('course_id', $request->course_id)->count() + 1);

        // if ($request->drip_type == "date") {
        //     $courseclass->drip_type = $request->drip_type;
        //     $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
        //     $courseclass->drip_date = $start_time;
        //     $courseclass->drip_days = null;
        // } elseif ($request->drip_type == "days") {

        //     $courseclass->drip_type = $request->drip_type;
        //     $courseclass->drip_days = $request->drip_days;
        //     $courseclass->drip_date = null;
        // } else {

        //     $courseclass->drip_days = null;
        //     $courseclass->drip_date = null;
        // }

        // if (isset($request->featured)) {
        //     $courseclass->featured = '1';
        // } else {
        //     $courseclass->featured = '0';
        // }

        if ($request->type == "text") {
            $courseclass->type = "text";
            $courseclass->long_text = $request->long_text;
        } elseif ($request->type == "quiz") {
            $courseclass->type = "quiz";
            $courseclass->url = $request->url;
        }
        // elseif ($request->type == "meeting") {
        //     $courseclass->type = "meeting";
        //     $courseclass->meeting_id = $request->meeting_id;
        //     $bbl = BBL::find($request->meeting_id);

        //     $courseclass->title = $bbl->meetingname;
        // }
        // elseif ($request->type == "offline_session") {
        //     $courseclass->type = "offline_session";
        //     $courseclass->offline_session_id = $request->offline_session_id;
        //     $session = OfflineSession::find($request->offline_session_id);
        //     $courseclass->title = $session->title;
        // }
        elseif ($request->type == "video") {
            $courseclass->type = "video";
            $courseclass->iframe_url = $request->iframe_url;
            $courseclass->video_url = $request->video_url ?? null;
        } elseif ($file = $request->file('file')) {
            $courseclass->type = $request->type;
            try {
                $name = time() . $file->getClientOriginalName();
                $file->storeAs('files/' . $courseclass->type, $name);
                $courseclass->file = $name;
                $courseclass->url = null;
            } catch (\Exception $e) {
                /** If any error then return back to old view with exception message */
                Session::flash('error', $e->getMessage());
            }
        }

        // if (!isset($request->preview_type)) {
        //     $courseclass['preview_url'] = $request->url;
        //     $courseclass['preview_type'] = "url";
        // } else {
        //     if ($file = $request->file('video')) {

        //         $filename = time() . $file->getClientOriginalName();
        //         $file->move('video/class/preview', $filename);
        //         $courseclass['preview_video'] = $filename;
        //     }
        //     $courseclass['preview_type'] = "video";
        // }


        // if ($request->type == "image") {
        //     $courseclass->type = "image";

        //     if ($request->checkImage == "url") {
        //         $courseclass->url = $request->imgurl;
        //         $courseclass->image = null;
        //     } else if ($request->checkImage == "uploadimage") {
        //         if ($file = $request->file('image')) {
        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('images/class', $name);
        //             $courseclass->image = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }


        // if ($request->type == "zip") {
        //     $courseclass->type = "zip";

        //     if ($request->checkZip == "zipURLEnable") {
        //         $courseclass->url = $request->zipurl;
        //         $courseclass->zip = null;
        //     } else if ($request->checkZip == "zipEnable") {
        //         if ($file = $request->file('uplzip')) {
        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('files/zip', $name);
        //             $courseclass->zip = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }

        // if ($request->type == "audio") {
        //     $courseclass->type = "audio";

        //     if ($request->checkAudio == "audiourl") {
        //         $courseclass->url = $request->audiourl;
        //         $courseclass->audio = null;
        //     } elseif ($request->checkAudio == "uploadaudio") {
        //         if ($file = $request->file('audioupload')) {
        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('files/audio', $name);
        //             $courseclass->audio = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }

        // if ($file = $request->file('file')) {

        //     $path = 'files/class/material/';

        //     if (!file_exists(public_path() . '/' . $path)) {

        //         $path = 'files/class/material/';
        //         File::makeDirectory(public_path() . '/' . $path, 0777, true);
        //     }

        //     $filename = time() . $file->getClientOriginalName();
        //     $file->move('files/class/material', $filename);
        //     $courseclass['file'] = $filename;
        // }


        // Notification when course class add
        // $cor = Course::find($request->course_id);

        // $course = [
        //     'title' => $cor->title,
        //     'image' => $cor->preview_image,
        // ];

        // $enroll = Order::where('course_id', $request->course_id)->get();

        // if (!$enroll->isEmpty()) {
        //    foreach ($enroll as $enrol) {
        //        if ($courseclass->save()) {
        //            $user = User::where('id', $enrol->user_id)->get();
        //            Notification::send($user, new CourseNotification($course));
        //        }
        //    }
        // } else {
        //     $courseclass->save();
        // }

        // Subtitle
        // if ($request->has('sub_t')) {
        //     foreach ($request->file('sub_t') as $key => $image) {
        //         $name = $image->getClientOriginalName();
        //         $image->move(public_path() . '/subtitles/', $name);

        //         $form = new Subtitle();
        //         $form->sub_lang = $request->sub_lang[$key];
        //         $form->sub_t = $name;
        //         $form->c_id = $courseclass->id;
        //         $form->save();
        //     }
        // }

        $courseclass->save();

        $this->coursecredithours($request->course_id); // update course credit hours on status changed
        $this->updatecourseprogress($request->course_id); // update enrolled course progress

        return redirect()->route('chapterclasses', $courseclass->coursechapter_id)->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show($id)
    {
        $subtitles = Subtitle::where('c_id', $id)->get();
        $cate = CourseClass::findOrFail($id);
        $coursechapt = CourseChapter::find($cate->coursechapter_id);
        $installments = Installment::where('course_id', '=', $cate->course_id)->get();
        $topics = QuizTopic::where('course_id', '=', $cate->course_id)->get();

        $datetimevalue = strtotime($cate->date_time);
        $formatted = date('Y-m-d', $datetimevalue);

        $pd = $cate['date_time'];
        $live_date = str_replace(" ", "T", $pd);

        $bbl_meetings = BBL::where('link_by', '!=', null)->where('is_ended', '!=', 1)->where('course_id', $cate->course_id)->get();
        $offline_sessions = OfflineSession::where('link_by', '!=', null)->where('is_ended', '!=', 1)->where('course_id', $cate->course_id)->get();

        return view('admin.course.courseclass.edit', compact('installments', 'cate', 'coursechapt', 'subtitles', 'live_date', 'topics', 'offline_sessions', 'bbl_meetings'));
    }


    public function edit(courseclass $courseclass)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $request->validate([
          //  'coursechapter_id' => 'required',
            'title' => 'required_if:type,text,pdf,video,zip,rar,word,excel,powerpoint|max:100',
            'type' => 'required',
            'file' => 'nullable|file|mimes:' . $this->files[$request->type] . '|max:102400',
            // 'meeting_id'=> 'required_if:type,meeting',
            // 'offline_session_id'=> 'required_if:type,offline_session',
            'long_text' => 'required_if:type,text',
            'iframe_url' => 'required_if:type,video',
            'url' => 'required_if:type,quiz',
            'duration' => 'required_if:type,video,text,pdf,zip,rar,word,excel,powerpoint|nullable|digits_between:1,3',
        ], [
            'course_chapters' => __('Chapter name is required'),
            'title.required_if' => __('Title is required'),
            'title.max' => __('Title should not be more than 100 characters'),
            'type.required' => __('Type is required'),
            'long_text.required_if' => __('Text is required'),
            // 'meeting_id.required_if' => __('Live Streaming selection is required'),
            // 'offline_session_id.required_if' => __('Offline Session selection is required'),
            'iframe_url.required_if' => __('Content is required'),
            'video_url.required_if' => __('Iframe video URL is required'),
            'url.required_if' => __('Quiz selection is required'),
            'file.required_if' => __('File is required'),
            'file.mimes' => __('File should be ' . $this->files[$request->type]),
            'file.max' => __('File size should not be more than 100MB'),
            'duration.required_if' => __('Duration is required'),
            "duration.digits_between" => __('Duration should not be more than 3 digits'),
        ]);

        ini_set('max_execution_time', 400);

        $courseclass = CourseClass::findOrFail($id);

        // $courseclass->unlock_installment = $request->unlock_installment;
        // $courseclass->coursechapter_id = $request->coursechapter_id;
        $courseclass->title = $request->title;
        $courseclass->duration = $request->duration;

        $courseclass->status = isset($request->status) ? '1' : '0';
        $courseclass->downloadable = isset($request->downloadable) ? '1' : '0';
        $courseclass->printable = isset($request->printable) ? '1' : '0';
        // $courseclass->featured = $request->featured;
        // $courseclass->size = $request->size;
        // $courseclass->date_time = $request->date_time;
        // $courseclass->detail = $request->detail;

        // if ($request->drip_type == "date") {
        //     $courseclass->drip_type = $request->drip_type;
        //     $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
        //     $courseclass->drip_date = $start_time;
        //     $courseclass->drip_days = null;
        // } elseif ($request->drip_type == "days") {

        //     $courseclass->drip_type = $request->drip_type;
        //     $courseclass->drip_days = $request->drip_days;
        //     $courseclass->drip_date = null;
        // } else {

        //     $courseclass->drip_days = null;
        //     $courseclass->drip_date = null;
        // }

        if ($request->type == "text") {
            $courseclass->type = "text";
            $courseclass->long_text = $request->long_text;
        } elseif ($request->type == "quiz") {
            $courseclass->type = "quiz";
            $courseclass->url = $request->url;
        } elseif ($request->type == "video") {
            $courseclass->type = "video";
            $courseclass->iframe_url = $request->iframe_url;
            $courseclass->video_url = $request->video_url ?? null;
        }
        // elseif ($request->type == "meeting") {
        //     $courseclass->type = "meeting";
        //     $courseclass->meeting_id = $request->meeting_id;
        //     $meeting = BBL::where('id', $request->meeting_id)->first();

        //     $courseclass->title = $meeting->meetingname;
        // }
        // elseif ($request->type == "offline_session") {
        //     $courseclass->type = "offline_session";
        //     $courseclass->offline_session_id = $request->offline_session_id;
        //     $session = OfflineSession::where('id', $request->offline_session_id)->first();

        //     $courseclass->title = $session->title;
        // }
        elseif ($file = $request->file('file')) {
            $courseclass->type = $request->type;

            try {
                $content = Storage::exists("/files/$courseclass->type/" . $courseclass->file);

                if ($content) {
                    unlink(storage_path("app/files/$courseclass->type/" . $courseclass->file));
                }

                $name = time() . $file->getClientOriginalName();
                $file->storeAs("files/$courseclass->type", $name);
                $courseclass->file = $name;
                $courseclass->url = null;
            } catch (Exception $e) {

                /** If any error then return back to old view with exception message */
                Session::flash('error', $e->getMessage());
            }
        }

        // if ($request->type == "audio") {
        //     $courseclass->type = "audio";

        //     if ($request->checkAudio == "audiourl") {
        //         $courseclass->url = $request->audiourl;
        //         $courseclass->audio = null;
        //     } else if ($request->checkAudio == "uploadaudio") {
        //         if ($file = $request->file('audio')) {
        //             if ($courseclass->audio != "") {
        //                 $content = @file_get_contents(public_path() . '/files/audio/' . $courseclass->audio);

        //                 if ($content) {
        //                     unlink(public_path() . '/files/audio/' . $courseclass->audio);
        //                 }
        //             }

        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('files/audio', $name);
        //             $courseclass->audio = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }

        // if ($request->type == "image") {
        //     $courseclass->type = "image";

        //     if ($request->checkImage == "url") {
        //         $courseclass->url = $request->imgurl;
        //         $courseclass->image = null;
        //     } else if ($request->checkImage == "uploadimage") {
        //         if ($file = $request->file('image')) {
        //             if ($courseclass->image != "") {
        //                 $content = @file_get_contents(public_path() . '/images/class/' . $courseclass->image);

        //                 if ($content) {
        //                     unlink(public_path() . '/images/class/' . $courseclass->image);
        //                 }
        //             }

        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('images/class', $name);
        //             $courseclass->image = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }

        // if ($request->type == "zip") {

        //     $courseclass->type = "zip";

        //     if ($request->checkZip == "zipURLEnable") {
        //         $courseclass->url = $request->zipurl;
        //         $courseclass->zip = null;
        //     } else if ($request->checkZip == "zipEnable") {
        //         if ($file = $request->file('uplzip')) {
        //             $content = @file_get_contents(public_path() . '/files/zip/' . $courseclass->zip);

        //             if ($content) {
        //                 unlink(public_path() . '/files/zip/' . $courseclass->zip);
        //             }

        //             $name = time() . $file->getClientOriginalName();
        //             $file->move('files/zip', $name);
        //             $courseclass->zip = $name;
        //             $courseclass->url = null;
        //         }
        //     }
        // }

        // if (isset($request->preview_type)) {
        //     $courseclass['preview_type'] = "video";
        // } else {
        //     $courseclass['preview_type'] = "url";
        // }

        // if (!isset($request->preview_type)) {
        //     $courseclass->preview_url = $request->preview_url;
        //     $courseclass->preview_video = null;
        //     $courseclass['preview_type'] = "url";
        // } else {

        //     if ($file = $request->file('video')) {
        //         // return $request;
        //         if ($courseclass->preview_video != "") {
        //             $content = @file_get_contents(public_path() . '/video/class/preview/' . $courseclass->preview_video);
        //             if ($content) {
        //                 unlink(public_path() . '/video/class/preview/' . $courseclass->preview_video);
        //             }
        //         }

        //         $filename = time() . $file->getClientOriginalName();
        //         $file->move('video/class/preview', $filename);
        //         $courseclass['preview_video'] = $filename;
        //         $courseclass->preview_url = null;

        //         $courseclass['preview_type'] = "video";
        //     }
        // }

        // if ($file = $request->file('file')) {
        //     $path = 'files/class/material/';

        //     if (!file_exists(public_path() . '/' . $path)) {

        //         $path = 'files/class/material/';
        //         File::makeDirectory(public_path() . '/' . $path, 0777, true);
        //     }

        //     if ($courseclass->file != "") {
        //         $class_file = @file_get_contents(public_path() . '/files/class/material/' . $courseclass->file);

        //         if ($class_file) {
        //             unlink('files/class/material/' . $courseclass->file);
        //         }
        //     }
        //     $name = time() . $file->getClientOriginalName();
        //     $file->move('files/class/material', $name);
        //     $courseclass['file'] = $name;
        // }

        // if (isset($request->featured)) {
        //     $courseclass['featured'] = '1';
        // } else {
        //     $courseclass['featured'] = '0';
        // }

        $courseclass->save();

        if (!$request->status) {
            $this->updatecourseprogress($courseclass->course_id); // update enrolled course progress
        }

        $this->coursecredithours($courseclass->course_id); // update course credit hours on courseclass add

        Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect()->route('chapterclasses', $courseclass->coursechapter_id);
    }


    public function destroy($id)
    {
        $courseclass = CourseClass::findOrFail($id);

        // if ($courseclass->type == "video") {

        //     $video_file = @file_get_contents(public_path() . '/video/class/' . $courseclass->video);

        //     if ($video_file) {
        //         unlink(public_path() . '/video/class/' . $courseclass->video);
        //     }
        // }

        // if ($courseclass->type == "audio") {

        //     $video_file = @file_get_contents(public_path() . '/files/audio/' . $courseclass->audio);

        //     if ($video_file) {
        //         unlink(public_path() . '/files/audio/' . $courseclass->audio);
        //     }
        // }

        // if ($courseclass->type == "image") {

        //     $image_file = @file_get_contents(public_path() . '/images/class/' . $courseclass->image);

        //     if ($image_file) {
        //         unlink(public_path() . '/images/class/' . $courseclass->image);
        //     }
        // }

        // if ($courseclass->type == "zip") {

        //     $zip_file = @file_get_contents(public_path() . '/files/zip/' . $courseclass->zip);

        //     if ($zip_file) {
        //         unlink(public_path() . '/files/zip/' . $courseclass->zip);
        //     }
        // }

        if ($courseclass->type == "pdf" || $courseclass->type == "zip" || $courseclass->type == "rar" || $courseclass->type == "word" || $courseclass->type == "excel" || $courseclass->type == "powerpoint") {
            $file = Storage::exists("/files/$courseclass->type/" . $courseclass->file);
            ;

            if ($file) {
                unlink(storage_path("app/files/$courseclass->type/" . $courseclass->file));
            }
        }

        // if ($courseclass->preview_type = "video") {
        //     $content = @file_get_contents(public_path() . '/video/class/preview/' . $courseclass->preview_video);
        //     if ($content) {
        //         unlink(public_path() . '/video/class/preview/' . $courseclass->preview_video);
        //     }
        // }

        $course_id = $courseclass->course_id;
        $courseclass->delete();

        $this->coursecredithours($course_id); // update course credit hours on courseclass delete

        $enroll = \App\CourseProgress::where('course_id', $course_id)->get();
        if (isset($enroll)) {
            foreach ($enroll as $progress) {
                $course_return = (array)$progress->mark_chapter_id;
                $offset = array_search($id, $course_return);
                if (is_numeric($offset)) {
                    // array_splice($course_return, $offset);
                    unset($course_return[$offset]);
                }

                $read_count = 0;
                $chapters = CourseClass::select('id', 'status')->where('course_id', $course_id)->get();
                $total_count = count($chapters->where('status', 1));

                foreach ($course_return as $read_lesson) {
                    $lesson = CourseClass::where([['id', $read_lesson],['status', 1]])->first();
                    if ($lesson) {
                        $read_count++;
                    }
                }

                $total_count == 0 ? $progres = 0 : $progres = ($read_count / $total_count) * 100;

                $progress->update([
                            'progress' => $progres,
                            'mark_chapter_id' => $course_return,
                            'all_chapter_id' => $chapters->pluck('id'),
                            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }
        }

        return back()->with('success', __('Deleted Successfully'));
    }


    public function sort(Request $request)
    {
        $posts = CourseClass::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if ($order['id'] == $post->id) {
                    CourseClass::find($post->id)->update(['position' => $order['position']]);
                }
            }
        }
        return response()->json('Update Successfully.', 200);
    }


    public function bulk_delete(Request $request)
    {
        $validator = Validator::make($request->all(), ['checked' => 'required']);
        if ($validator->fails()) {
            return back()->with('error', trans('Please select field to be deleted.'));
        }
        foreach ($request->checked as $id) {
            $this->destroy($id);
        }
        // $courseclass = CourseClass::whereIn('id', $request->checked)->first();
        // $course_id = $courseclass->course_id;
        // CourseClass::whereIn('id', $request->checked)->delete();
        // DB::statement("update courses set credit_hours = (select if(duration IS NULL, 0.00,  round((sum(duration) / 60),2)) from course_classes where status = 1 and course_id = $course_id) where id = $course_id");

        return back()->with('error', trans('Selected CourseClass has been deleted.'));
    }


    public function coursecredithours($course_id)
    {
        $classes = CourseClass::where([['course_id', $course_id],['status', 1]])->get();

        $sum = 0;
        foreach ($classes as $class) {
            $sum += $class->duration ?? 0;
            $class->quiz ? $sum += $class->quiz->timer : 0;
            $class->meeting ? $sum += $class->meeting->duration : 0;
            $class->offlineSession ? $sum += $class->offlineSession->duration : 0;
        }

        DB::statement("update courses set credit_hours =  round($sum/60, 2) where id = '$course_id'");
    }


    public function updatecourseprogress($course_id)
    {
        $enroll = CourseProgress::where('course_id', $course_id)->get();
        if (isset($enroll)) {
            foreach ($enroll as $progress) {
                $course_return = $progress->mark_chapter_id;

                $read_count = 0;
                $chapters = CourseClass::select('id', 'status')->where('course_id', $course_id)->get();
                $total_count = count($chapters->where('status', 1));

                foreach ($course_return as $read_lesson) {
                    $lesson = CourseClass::where([['id', $read_lesson],['status', 1]])->first();
                    if ($lesson) {
                        $read_count++;
                    }
                }

                $read_count = count($course_return);
                $total_count == 0 ? $progres = 0 : $progres = ($read_count / $total_count) * 100;

                $progress->update([
                            'progress' => $progres,
                            'mark_chapter_id' => $course_return,
                            'all_chapter_id' => $chapters->pluck('id'),
                            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);
            }
        }
    }


    public function courseclassstatus($id)
    {
        $courseclass = CourseClass::findorfail($id);

        if ($courseclass->status == 0) {
            DB::table('course_classes')->where('id', '=', $id)->update(['status' => "1"]);
            $this->coursecredithours($courseclass->course_id); // update course credit hours on status changed
            $this->updatecourseprogress($courseclass->course_id); // update enrolled course progress

            return response()->json('success', 200);
        } else {
            DB::table('course_classes')->where('id', '=', $id)->update(['status' => "0"]);
            $this->coursecredithours($courseclass->course_id); // update course credit hours on status changed
            $this->updatecourseprogress($courseclass->course_id); // update enrolled course progress

            return response()->json('success', 200);
        }
    }


    public function courseclassfeatured($id)
    {
        $courseclass = CourseClass::findorfail($id);

        if ($courseclass->featured == 0) {
            DB::table('course_classes')->where('id', '=', $id)->update(['featured' => "1"]);
            return back()->with('success', __('Status changed to active !'));
        } else {
            DB::table('course_classes')->where('id', '=', $id)->update(['featured' => "0"]);
            return back()->with('delete', __('Status changed to deactive !'));
        }
    }
}
