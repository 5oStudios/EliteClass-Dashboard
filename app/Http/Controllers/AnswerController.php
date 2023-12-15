<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Answer;
use App\Course;
use App\Question;
use Session;
use Spatie\Permission\Models\Role;

class AnswerController extends Controller
{
    public function __construct()
    {
    
        $this->middleware('permission:answer.view', ['only' => ['show']]);
        $this->middleware('permission:answer.create', ['only' => ['store']]);
        $this->middleware('permission:answer.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:answer.delete', ['only' => ['destroy']]);
    
    }
    public function index()
    {

    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'answer' => 'required|max:300',
            'question_id' => 'required'
        ],[
            'answer.required' => __('Answer is required'),
            'answer.max' => __('Answer should not be more than 300 characters'),
            'question_id.required' => __('Question is required'),
        ]);

        $input = $request->all();
        $input['status'] = isset($request->status)  ? 1 : 0;

        $data = Answer::create($input);
        $data->save(); 

        Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect()->route('course.view',$request->course_id);
    }

    public function show($id)
    {
        $que = Question::find($id);
        // $questions = Question::where('id',$id)->get();
        $answers = Answer::where('question_id',$id)->get();
        return view('admin.course.answer.index',compact('answers', 'que'));

    }

    public function edit($id)
    {
        $show= Answer::find($id);
        return view('admin.course.answer.edit', compact('show'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'answer' => 'required|max:300',
        ],[
            'answer.required' => __('Answer is required'),
            'answer.max' => __('Answer should not be more than 300 characters'),
        ]);
        $data = Answer::findorfail($id);
        $input = $request->all();
        $input['status'] = isset($request->status)  ? 1 : 0;
        
        $data->update($input);

        return redirect()->route('course.view',$request->course_id);
    }

    public function destroy($id)
    {
        DB::table('answers')->where('id',$id)->delete();
        return back()->with('delete', trans('flash.DeletedSuccessfully'));
    }

    public function answer(Request $request, $id)
    {
        $input = $request->all();
        $data = Answer::create($input);
        $data->save();
         
        Session::flash('success', trans('flash.AddedSuccessfully'));
        return back();
    }
}
