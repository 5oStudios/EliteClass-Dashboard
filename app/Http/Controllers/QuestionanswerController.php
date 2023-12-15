<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;
use App\User; 
use App\Course;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class QuestionanswerController extends Controller
{
    public function __construct()
    {
    
        $this->middleware('permission:question.view', ['only' => ['show']]);
        $this->middleware('permission:question.create', ['only' => [ 'store','question']]);
        $this->middleware('permission:question.edit', ['only' => [ 'update','questionanswerstatus']]);
        $this->middleware('permission:question.delete', ['only' => ['destroy', 'bulk_delete']]);
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'question' => 'required|max:300',
        ],[
            'question.required' => __('Question is required'),
            'question.max' => __('Question should not be more than 300 characters'),
        ]);

        $input = $request->all();

        $course = Course::where('id',$request->course_id)->first();

        $input['instructor_id'] = $course->user_id;
        $input['user_id'] = Auth::id();
        $input['status'] = isset($request->status)  ? 1 : 0;
        $data = Question::create($input);
        $data->save(); 

        Session::flash('success',trans('flash.AddedSuccessfully'));
        return redirect()->route('course.view',$request->course_id);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\questionanswer  $questionanswer
     * @return \Illuminate\Http\Response
     */
 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\questionanswer  $questionanswer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $que = Question::find($id);
        $user =  User::all();
        $courses = Course::all();
        return view('admin.course.questionanswer.edit',compact('que','courses','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\questionanswer  $questionanswer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request,[
            'question' => 'required|max:300',
        ],[
            'question.required' => __('Question is required'),
            'question.max' => __('Question should not be more than 300 characters'),
        ]);

        $input = $request->all();
        $data = Question::findorfail($id);
        
        $input['status'] = isset($request->status) ? '1' : '0';
        $data->update($input);
        
        Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect()->route('course.view',$request->course_id);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\questionanswer  $questionanswer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('questions')->where('id',$id)->delete();
        DB::table('answers')->where('question_id',$id)->delete();
        return back();
    }

    public function question(Request $request, $id)
    {
        $this->validate($request,[
            'question' => 'required',
        ],[
            'question.required' => __('Question is required'),
        ]);

        $input = $request->all();
        $course = Course::find($id);
        $input['instructor_id'] = $course->user_id;
        $input['user_id'] = Auth::id();
        $input['status'] = isset($request->status)  ? 1 : 0;
        $data = Question::create($input);

        return back()->with('success',trans('flash.AddedSuccessfully'));
    }
    public function bulk_delete(Request $request)
    {
     
           $validator = Validator::make($request->all(), ['checked' => 'required']);
           if ($validator->fails()) {
            return back()->with('error',trans('Please select field to be deleted.'));
           }
           Question::whereIn('id',$request->checked)->delete();

          return back()->with('error',trans('Selected Question has been deleted.'));

          
   }

   public function questionanswerstatus($id)
   {
       $status = Question::findorfail($id);

       if($status->status == 0)
       {
           DB::table('questions')->where('id','=',$id)->update(['status' => "1"]); 
           return back()->with('success', __('Status changed to active !'));
       }
       else
       {
           DB::table('questions')->where('id','=',$id)->update(['status' => "0"]);
           return back()->with('delete', __('Status changed to deactive !'));
       }
   }
}
