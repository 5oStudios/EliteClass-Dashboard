<?php

namespace App\Http\Controllers;

use Image;
use App\BBL;
use App\Cart;
use App\Order;
use App\Course;
use App\CourseClass;
use App\Installment;
use App\CourseChapter;
use App\CourseProgress;
use App\OfflineSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CoursechapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:course-chapter.view', ['only' => ['index','show']]);
        $this->middleware('permission:course-chapter.create', ['only' => [ 'store', 'sort']]);
        $this->middleware('permission:course-chapter.edit', ['only' => [ 'update','coursechapterstatus', 'duplicate', 'copyCourseClasses', 'sprt']]);
        $this->middleware('permission:course-chapter.delete', ['only' => ['destroy', 'bulk_delete']]);
    }


    public function index()
    {
        $coursechapter = CourseChapter::all();
        return view('admin.course.coursechapter.index', compact("coursechapter"));
    }


    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'chapter_name' => 'required|max:100',
            'price' => 'required_with:is_purchasable|numeric|min:0',
        ], [
            "course_id.required" => __('Course name is required'),
            "course_id.exists" => __('The selected course name is not exist'),
            "chapter_name.required" => __("Chapter name is required"),
            "chapter_name.max" => __("Chapter name should not be more than 100 characters"),
            "price.required" => __("Chapter price is required"),
            "price.numeric" => __("price must be a numeric value"),
            "price.min" => __("Price should not be a negative number"),
        ]);

        $input = $request->all();
        $input['price'] = $request->price ?? '0';
        $input['discount_price'] = $request->price ?? '0';

        $input['is_purchasable'] = $request->is_purchasable ? '1' : '0';
        $input['status'] = $request->status ? '1' : '0';

        if (isset($request->type_id)) {
            $session = json_decode($request->type_id);
            $input['type_id'] = $session->id;
        }

        // if(isset($request->status))
        // {
        //   $input['status'] = "1";
        // }
        // else
        // {
        //   $input['status'] = "0";
        // }

        // if($file = $request->file('file'))
        // {
        //   $filename = time().$file->getClientOriginalName();
        //   $file->move('files/material',$filename);
        //   $input['file'] = $filename;
        // }

        // if($request->drip_type == "date")
        // {
        //     $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
        //     $input['drip_date'] = $start_time;
        //     $input['drip_days'] = null;

        // }
        // elseif($request->drip_type == "days"){

        //     $input['drip_days'] = $request->drip_days;
        //     $input['drip_date'] = null;

        // }
        // else{

        //     $input['drip_days'] = null;
        //     $input['drip_date'] = null;

        // }

        $input['position'] = (CourseChapter::count() + 1);
        $input['user_id'] = Auth::user()->id;

        $data = CourseChapter::create($input);

        $data->save();

        return redirect()->route('course.view', $request->course_id)->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show($id)
    {
        $cate = CourseChapter::findOrFail($id);
        $courses = Course::all();
        $installments = $cate->courses->installments()->get();
        $bbl_meetings = BBL::whereNotNull('link_by')->where('is_ended', '<>', 1)->where('course_id', $cate->course_id)->get();
        $offline_sessions = OfflineSession::whereNotNull('link_by')->where('is_ended', '<>', 1)->where('course_id', $cate->course_id)->get();

        return view('admin.course.coursechapter.edit', compact('cate', 'courses', 'installments', 'bbl_meetings', 'offline_sessions'));
    }


    public function edit(coursechapter $coursechapter)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'chapter_name' => 'required|max:100',
            'price' => 'required_with:is_purchasable|numeric|min:0',
        ], [
            "course_id.required" => __('Course name is required'),
            "course_id.exists" => __('The selected course name is not exist'),
            "chapter_name.required" => __("Chapter name is required"),
            "chapter_name.max" => __("Chapter name should not be more than 100 characters"),
            "price.required" => __("Chapter price is required"),
            "price.numeric" => __("price must be a numeric value"),
            "price.min" => __("Price should not be a negative number"),
        ]);

        $coursechapter = CourseChapter::find($id);
        $input = $request->all();
        $input['price'] = $request->price ?? '0';
        $input['discount_price'] = $request->price ?? '0';

        $input['is_purchasable'] = $request->is_purchasable ? '1' : '0';

        // if($request->drip_type == "date")
        // {
        //     $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
        //     $input['drip_date'] = $start_time;
        //     $input['drip_days'] = null;
        // }
        // elseif($request->drip_type == "days"){

        //     $input['drip_days'] = $request->drip_days;
        //     $input['drip_date'] = null;
        // }
        // else{
        //     $input['drip_days'] = null;
        //     $input['drip_date'] = null;
        // }

        if (isset($request->type_id)) {
            $session = json_decode($request->type_id);
            $input['type_id'] = $session->id;
        }

        if (isset($request->status)) {
            $input['status'] = "1";
        } else {
            $input['status'] = "0";
            Cart::where('chapter_id', $id)->delete();
        }

        // if($file = $request->file('file'))
        // {
        //     if($data->file != "")
        //     {
        //         $chapter_file = @file_get_contents(public_path().'/files/material/'.$data->file);

        //         if($chapter_file)
        //         {
        //             unlink('files/material/'.$data->file);
        //         }
        //     }
        //     $name = time().$file->getClientOriginalName();
        //     $file->move('files/material', $name);
        //     $input['file'] = $name;
        // }

        Cart::where(['chapter_id' => $id, 'installment' => '0'])
                ->update([
                    'price' => $request->price,
                    'offer_price' => $request->price,
        ]);

        $coursechapter->update($input);

        if (!$request->status) {
            $this->updatecourseprogress($coursechapter->course_id); // update enrolled course progress
        }

        Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect()->route('course.view', $request->course_id);
    }


    public function destroy($id)
    {
        $coursechapter = CourseChapter::findOrFail($id);

        // if ($coursechapter->file != null)
        //   {

        //     $image_file = @file_get_contents(public_path().'/files/material/'.$coursechapter->file);

        //     if($image_file)
        //     {
        //         unlink(public_path().'/files/material/'.$coursechapter->file);
        //     }
        // }

        $order = Order::where('chapter_id', $id)->allActiveInactiveOrder()->get();

        if ($order->isNotEmpty()) {
            return back()->with('delete', trans('flash.ChapterCannotDelete'));
        }

        Cart::where('chapter_id', $id)->delete();
        $coursechapter->delete();

        CourseClass::where('coursechapter_id', $id)->delete();

        $course_id = $coursechapter->courses->id;

        $this->coursecredithours($course_id); // update course credit hours on chapter delete

        $enroll = \App\CourseProgress::where('course_id', $course_id)->get();
        if (isset($enroll)) {
            foreach ($enroll as $progress) {
                $read_count = 0;
                $chapters = CourseClass::select('id', 'status')->where('course_id', $course_id)->get();
                $course_return = (array)$progress->mark_chapter_id;

                $offset = array_diff($course_return, $chapters->pluck('id')->toArray());
                if ($offset) {
                    foreach (array_keys($offset) as $remove) {
                        unset($course_return[$remove]);
                    }
                }

                $total_count = count($chapters->where('status', 1));

                foreach ($course_return as $read_lesson) {
                    $lesson = CourseClass::where([['id', $read_lesson],['status', 1]])->first();
                    if ($lesson) {
                        $read_count++;
                    }
                }
                // $read_count = count($course_return);

                $total_count == 0 ? $progres = 0 : $progres = ($read_count / $total_count) * 100;

                $progress->update([
                            'progress' => $progres,
                            'mark_chapter_id' => array_values($course_return),
                            'all_chapter_id' => $chapters->pluck('id'),
                ]);
            }
        }

        return back()->with('delete', trans('flash.DeletedSuccessfully'));
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
        // CourseChapter::whereIn('id',$request->checked)->delete();

        return back()->with('error', trans('Selected CourseChapter has been deleted.'));
    }


    public function duplicate($id)
    {
        $existingChapter = CourseChapter::findOrFail($id);

        $newChapter = $existingChapter->replicate();

        $newChapter = $existingChapter->replicate()->fill([
            'chapter_name' => 'duplicate - ' . $existingChapter->chapter_name,
        ]);

        $newChapter->save();

        $existingClasses = CourseClass::where('coursechapter_id', $existingChapter->id)->get();

        foreach ($existingClasses as $key => $class) {
            if (
                $class->file != null &&
                ($class->type == 'pdf' ||  $class->type == 'zip' || $class->type == 'rar' || $class->type == 'word' || $class->type == 'excel' || $class->type == 'powerpoint') &&
                Storage::exists("/files/$class->type/" . $class->file)
            ) {
                $oldPathFile = Storage::path("/files/$class->type/" . $class->file);

                $newclassFile = $key . $class->file;

                $newPathWithFile = Storage::path("files/$class->type/" . $newclassFile);

                File::copy($oldPathFile, $newPathWithFile);
            } else {
                $newclassFile = null;
            }

            $new_class = $class->replicate()->fill([
                'course_id' => $existingChapter->course_id,
                'coursechapter_id' => $newChapter->id,
                'position' => (CourseClass::count() + 1),
                'file' => $newclassFile,
            ]);
    
            $new_class->save();
        }
        
        $this->coursecredithours($newChapter->course_id); // update course credit hours on chapter status change
        $this->updatecourseprogress($newChapter->course_id); // update enrolled course progress

        return back()->with('success', trans('flash.ChapterDuplicateSuccessfully'));
    }

    public function copyCourseClasses(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'chapter_name' => 'required|max:100',
            'class_ids' => 'required|array|exists:course_classes,id|min:1',
            'price' => 'required_with:is_purchasable|numeric|min:0',
        ], [
            "course_id.required" => __('Course name is required'),
            "course_id.exists" => __('The selected course name is not exist'),
            "chapter_name.required" => __("Chapter name is required"),
            "chapter_name.max" => __("Chapter name should not be more than 300 characters"),
            "price.required" => __("Chapter price is required"),
            "price.numeric" => __("Price must be a numeric value"),
            "price.min" => __("Price should not be a negative number"),
        ]);

        $input = $request->all();
        $input['price'] = $request->price ?? 0;
        $input['discount_price'] = $request->price ?? 0;

        $input['is_purchasable'] = $request->is_purchasable ? 1 : 0;
        $input['status'] = $request->status ? 1 : 0;

        $input['position'] = (CourseChapter::count() + 1);
        $input['user_id'] = Auth::user()->id;

        $chapter = CourseChapter::create($input);

        $chapter->save();

        $existingClasses = CourseClass::find($request->class_ids);

        foreach ($existingClasses as $key => $class) {
            if (
                $class->file != null &&
                ($class->type == 'pdf' ||  $class->type == 'zip' || $class->type == 'rar' || $class->type == 'word' || $class->type == 'excel' || $class->type == 'powerpoint') &&
                Storage::exists("/files/$class->type/" . $class->file)
            ) {
                $oldPathFile = Storage::path("/files/$class->type/" . $class->file);

                $newclassFile = $key . $class->file;

                $newPathWithFile = Storage::path("files/$class->type/" . $newclassFile);

                File::copy($oldPathFile, $newPathWithFile);
            } else {
                $newclassFile = null;
            }

            $new_class = $class->replicate()->fill([
                'course_id' => $chapter->course_id,
                'coursechapter_id' => $chapter->id,
                'position' => (CourseClass::count() + 1),
                'file' => $newclassFile,
            ]);

            $new_class->save();
        }

        $this->coursecredithours($chapter->course_id); // update course credit hours on chapter status change
        $this->updatecourseprogress($chapter->course_id); // update enrolled course progress

        return back()->with('success', trans('flash.ChapterCreatedSuccessfully'));
    }


    public function sort(Request $request)
    {
        $posts = CourseChapter::all();

        foreach ($posts as $post) {
            foreach ($request->order as $order) {
                if ($order['id'] == $post->id) {
                    CourseChapter::find($post->id)->update(['position' => $order['position']]);
                }
            }
        }

        return response()->json(__('Update Successfully'), 200);
    }


    public function coursechapterstatus($id)
    {
        $coursechapter = CourseChapter::findOrFail($id);

        if ($coursechapter->status == '0') {
            DB::table('course_chapters')->where('id', '=', $id)->update(['status' => "1"]);
            $this->coursecredithours($coursechapter->course_id); // update course credit hours on chapter status change
            $this->updatecourseprogress($coursechapter->course_id); // update enrolled course progress

            Cart::where('chapter_id', $id)->delete();

            return response()->json('success', 200);
        } else {
            DB::table('course_chapters')->where('id', '=', $id)->update(['status' => "0"]);
            $this->coursecredithours($coursechapter->course_id); // update course credit hours on chapter status change
            $this->updatecourseprogress($coursechapter->course_id); // update enrolled course progress

            return response()->json('success', 200);
        }
    }


    public function coursecredithours($course_id)
    {
        // $classes = CourseClass::where([['course_id', $course->id],['status', 1]])->get();
        $classes = CourseClass::whereHas('coursechapters')->where([['course_id', $course_id],['status', 1]])->get();
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
                $chapters = CourseClass::select('id', 'status')->whereHas('coursechapters')->where('course_id', $course_id)->get();
                $total_count = count($chapters->where('status', 1));

                foreach ($course_return as $read_lesson) {
                    $lesson = CourseClass::whereHas('coursechapters')->where([['id', $read_lesson],['status', 1]])->first();
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
}
