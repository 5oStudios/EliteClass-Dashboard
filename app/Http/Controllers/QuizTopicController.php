<?php

namespace App\Http\Controllers;

use Auth;
use App\Quiz;
use App\User;
use App\Order;
use App\QuizTopic;
use App\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class QuizTopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:quiz-topic.view', ['only' => ['index', 'show', 'view']]);
        $this->middleware('permission:quiz-topic.create', ['only' => ['store']]);
        $this->middleware('permission:quiz-topic.edit', ['only' => ['update', 'quiztopicstatus', 'quiztopicagainstatus']]);
        $this->middleware('permission:quiz-topic.delete', ['only' => ['destroy', 'delete', 'bulk_delete']]);
        $this->middleware('permission:report.quiz-report.manage', ['only' => ['quizreport', 'view']]);
    }


    public function index()
    {
        $topics = QuizTopic::latest()->get();
        return view('admin.course.quiztopic.index', compact('topics'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|max:300',
            'per_q_mark' => 'required|digits_between:1,3',
            'p_percent' => 'required|digits_between:1,3',
            'timer' => 'required|digits_between:1,3',
        ], [
            'title.required' => __('Quiz title is required'),
            'title.string' => __('Quiz title must be in string'),
            'title.max' => __('Quiz title should not be more than 100 charaters'),
            'description.required' => __('Quiz description is required'),
            'description.max' => __('Quiz description should not be more than 300 characters'),
            'per_q_mark.required' => __('Quiz marks is required'),
            'per_q_mark.digits_between' => __('Quiz marks should not be more than 3 digits'),
            'p_percent.required' => __('Passing percentage is required'),
            'p_percent.digits_between' => __('Passing percentage should not be more than 3 digits'),
            'timer.required' => __('Quiz timer is required'),
            'timer.digits_between' => __('Quiz timer should not be more than 3 digits'),
        ]);
        $input = $request->all();

        if (isset($request->quiz_price)) {
            $request->validate([
                'amount' => 'required',
            ]);
        }

        // if (isset($request->type)) {
        //     $input['type'] = '1';
        // } else {
        //     $input['type'] = null;
        // }

        if (isset($request->quiz_price)) {
            $input['amount'] = $request->amount;
        } else {
            $input['amount'] = null;
        }

        if (isset($request->show_ans)) {
            $input['show_ans'] = "1";
        } else {
            $input['show_ans'] = "0";
        }

        $input['status'] = "1";

        if (isset($request->quiz_again)) {
            $input['quiz_again'] = "1";
        } else {
            $input['quiz_again'] = "0";
        }

        $quiz = QuizTopic::create($input);

        return back()->with('success', __('Topic has been added'));
    }


    public function show($id)
    {
        $topic = QuizTopic::findOrFail($id);
        return view('admin.course.quiztopic.edit', compact('topic'));
    }


    public function edit(QuizTopic $quizTopic)
    {
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|max:300',
            'per_q_mark' => 'required|digits_between:1,3',
            'p_percent' => 'required|digits_between:1,3',
            'timer' => 'required|digits_between:1,3',
        ], [
            'title.required' => __('Quiz title is required'),
            'title.string' => __('Quiz title must be in string'),
            'title.max' => __('Quiz title should not be more than 100 charaters'),
            'description.required' => __('Quiz description is required'),
            'description.max' => __('Quiz description should not be more than 300 characters'),
            'per_q_mark.required' => __('Quiz marks is required'),
            'per_q_mark.digits_between' => __('Quiz marks should not be more than 3 digits'),
            'p_percent.required' => __('Passing percentage is required'),
            'p_percent.digits_between' => __('Passing percentage should not be more than 3 digits'),
            'timer.required' => __('Quiz timer is required'),
            'timer.digits_between' => __('Quiz timer should not be more than 3 digits'),
        ]);

        if (isset($request->pricechk)) {
            $request->validate([
                'amount' => 'required',
            ]);
        }

        $topic = QuizTopic::findOrFail($id);

        $topic->title = $request->title;
        $topic->description = $request->description;
        $topic->per_q_mark = $request->per_q_mark;
        $topic->p_percent = $request->p_percent;
        $topic->timer = isset($request->timer) ? $request->timer : null;
        $topic->due_days = $request->due_days;

        if (isset($request->type)) {
            $topic['type'] = '1';
        } else {
            $topic['type'] = null;
        }

        $topic['status'] = "1";

        if (isset($request->quiz_again)) {
            $topic['quiz_again'] = "1";
        } else {
            $topic['quiz_again'] = "0";
        }

        $topic->save();

        return redirect()->route('course.view', $topic->course_id);
    }


    public function destroy($id)
    {
        $topic = QuizTopic::findOrFail($id);

        if ($topic->courseclass) {
            return back()->with('delete', trans('flash.CannotDeleteQuiz'));
        } else {

            $topic->delete();

            Quiz::where('topic_id', $id)->delete();
            QuizAnswer::where('topic_id', $id)->delete();
        }

        return back()->with('delete', trans('flash.DeletedSuccessfully'));
    }


    public function delete($id)
    {
        $topic = QuizTopic::findOrFail($id);
        $answer = QuizAnswer::where('topic_id', $id)->get();

        if ($answer != null) {
            QuizAnswer::where('topic_id', $id)->delete();
        }
        return redirect()->route('course.view', $topic->course_id);
    }


    public function showreport($id)
    {
        $topics = QuizTopic::findOrFail($id);
        $ans = QuizAnswer::where('topic_id', $topics->id)->groupBy('user_id')->orderBy('id', 'DESC')->get();
        $c_que = Quiz::where('topic_id', $id)->count();

        //    $students = User::get();

        //    $filtStudents = collect();
        //    foreach ($students as $student) {
        //        foreach ($ans as $answer) {
        //            if ($answer->user_id == $student->id) {
        //                $filtStudents->push($student);
        //            }
        //        }
        //    }

        //    $filtStudents = $filtStudents->unique();
        //    $filtStudents = $filtStudents->flatten();

        return view('admin.course.quiztopic.report', compact('ans', 'c_que', 'topics'));
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
        // QuizTopic::whereIn('id', $request->checked)->delete();

        return back()->with('error', trans('Selected QuizTopic has been deleted.'));
    }


    public function quiztopicstatus($id)
    {
        $quiztopic = QuizTopic::findorfail($id);

        if ($quiztopic->status == 0) {
            DB::table('quiz_topics')->where('id', '=', $id)->update(['status' => "1"]);
            return back()->with('success', __('Status changed to active !'));
        } else {
            DB::table('quiz_topics')->where('id', '=', $id)->update(['status' => "0"]);
            return back()->with('delete', __('Status changed to deactive !'));
        }
    }


    public function quiztopicagainstatus($id)
    {
        $quiztopic = QuizTopic::findorfail($id);

        if ($quiztopic->quiz_again == 0) {
            DB::table('quiz_topics')->where('id', '=', $id)->update(['quiz_again' => "1"]);
            return back()->with('success', __('Status changed to active !'));
        } else {
            DB::table('quiz_topics')->where('id', '=', $id)->update(['quiz_again' => "0"]);
            return back()->with('delete', __('Status changed to deactive !'));
        }
    }


    public function quizreport(Request $request)
    {
        if (auth()->user()->hasRole('instructor')) {

            $orders = Order::query()
                ->where('instructor_id', auth()->id())
                ->whereHas('user', function ($q) {
                    $q->join('quiz_answers', 'quiz_answers.user_id', '=', 'users.id')
                        ->exceptTestuser();
                })
                ->whereHas('courses', function ($query) {
                    $query->join('quiz_answers', 'quiz_answers.course_id', '=', 'courses.id');
                })
                ->whereNotNull('course_id')
                ->with('user')
                ->with('courses', function ($query) {
                    $query->with('quizanswers');
                })
                ->allActiveInactiveOrder()
                ->groupBy('user_id')
                ->latest();
        } else {
            $orders = Order::query()
                ->whereHas('user', function ($q) {
                    $q->join('quiz_answers', 'quiz_answers.user_id', '=', 'users.id')
                        ->exceptTestuser();
                })
                ->whereHas('courses', function ($query) {
                    $query->join('quiz_answers', 'quiz_answers.course_id', '=', 'courses.id');
                })
                ->whereNotNull('course_id')
                ->with('user')
                ->with('courses', function ($query) {
                    $query->with('quizanswers');
                })
                ->allActiveInactiveOrder()
                ->groupBy('user_id')
                ->latest();
        }

        if ($request->ajax()) {
            return DataTables::of($orders)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->user->fname ? ($row->user->lname ? $row->user->fname . ' ' . $row->user->lname : $row->user->fname) : '';
                })
                ->editColumn('action', function ($row) {
                    return '<div class="btn-group mx-2">
                                <a href="' . route("quizre", $row->user->id) . '" class="btn btn-xs btn-primary-rgba"><i class="feather icon-eye mx-2"></i>' . __("View") . '</a>
                            </div>';
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        return view('admin.report.quiz');
    }


    public function view(Request $request, $id)
    {
        $userAnswers = QuizAnswer::query()
            ->where('user_id', $id)
            ->whereHas('courses', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['user', 'courses', 'topic'])
            ->groupBy('course_id', 'attempt');

        if ($request->ajax()) {
            return DataTables::of($userAnswers)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->user->fname ? ($row->user->lname ? $row->user->fname . ' ' . $row->user->lname : $row->user->fname) : '';
                })
                ->editColumn('marks_obtained', function ($row) {
                    $anss = QuizAnswer::where('topic_id', $row->topic_id)
                                        ->where('user_id', $row->user_id)
                                        ->where('attempt', $row->attempt)
                                        ->get();

                    $mark = 0;
                    $correct = 0;
                    foreach ($anss as $answer) {
                        if (strtolower($answer->user_answer) == strtolower($answer->answer)) {
                            $mark++;
                        }
                    }

                    return $mark * $row->topic->per_q_mark;
                })
                ->editColumn('total_marks', function ($row) {
                    return $row->topic->quizquestion->count() * $row->topic->per_q_mark;
                })
                ->rawColumns(['name', 'marks_obtained', 'total_marks'])
                ->make(true);
        }

        return view('admin.report.quizview');
    }
}
