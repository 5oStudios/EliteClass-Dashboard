<?php

namespace App\Http\Controllers;

use App\Quiz;
use Illuminate\Http\Request;
use App\Course;
use App\QuizTopic;
use App\QuizAnswer;
use Illuminate\Support\Facades\File;
use Image;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Session;
use DB;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;


class QuizController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $cor = Course::all();
    $topics = QuizTopic::all();
    $questions = Quiz::all();
    return view('admin.course.quiz.index', compact('questions', 'topics', 'cor'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // 
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    if (!isset($request->type)) {
      $request->validate([
        'course_id' => 'required',
        'topic_id' => 'required',
        'question' => 'required|max:500',
        'type' => 'required|in:mcq,audio,essay,image',
      ], [
        'type.required' => __('Question type is required'),
      ]);
    } else {
      if ($request->type == 'mcq') {
        $request->validate([
          'course_id' => 'required',
          'topic_id' => 'required',
          'question' => 'required|max:500',
          'a' => 'required|max:200',
          'b' => 'required|max:200',
          'c' => 'required|max:200',
          'd' => 'required|max:200',
          'answer' => 'required|size:1',
        ], [
          'course_id.required' => __('Course is required'),
          'topic_id.string' => __('Quiz Topic is required'),
          'question.required' => __('Quiz question is required'),
          'question.max' => __('Quiz question should not be more than 500 characters'),
          'a.required' => __('Option a is required'),
          'a.max' => __('Option a should not be more than 200 characters'),
          'b.required' => __('Option b is required'),
          'b.max' => __('Option b should not be more than 200 characters'),
          'c.required' => __('Option c is required'),
          'c.max' => __('Option c should not be more than 200 characters'),
          'd.required' => __('Option d is required'),
          'd.max' => __('Option d should not be more than 200 characters'),
          'answer.required' => __('Answer is required'),
          'answer.size' => __('Answer must contain only one letter'),
        ]);


        $input = $request->all();

        $quiz = new Quiz();
        $quiz->course_id = $input['course_id'];
        $quiz->topic_id = $input['topic_id'];
        $quiz->question = $input['question'];
        $quiz->a = $input['a'];
        $quiz->b = $input['b'];
        $quiz->c = $input['c'];
        $quiz->d = $input['d'];
        $quiz->answer = $input['answer'];
        $quiz->type = $input['type'];
        $quiz->save();

      } elseif ($request->type == 'audio') {
        $request->validate([
          'course_id' => 'required',
          'topic_id' => 'required',
          'question' => 'sometimes|max:500',
          'answer' => 'sometimes|max:500',
          'audio' => 'required|file|mimes:audio/mpeg,mpga,mp3,wav,aac|max:10240'
        ], [
          'course_id.required' => __('Course is required'),
          'topic_id.required' => __('Quiz Topic is required'),
          'question.required' => __('Quiz question is required'),
          'question.max' => __('Quiz question should not be more than 500 characters'),
          'answer.max' => __('Answer should not be more than 500 characters'),
          'audio.mimes' => __('Audio file type should be one of :mpeg, mpga, mp3, wav or aac'),
          'audio.max' => __('Audio file type should be less than 10 MB'),
        ]);

        if ($request->hasFile('audio')) {
          $uniqueId = uniqid();
          // $original_name = $request->file('audio')->getClientOriginalName();
          // $size = $request->file('audio')->getSize();
          $extension = $request->file('audio')->getClientOriginalExtension();
          $name = Carbon::now()->format('Ymd') . '_' . $uniqueId . '.' . $extension;
          $path = $request->file('audio')->move(public_path('files/audio'), $name);
        }

        $input = $request->all();

        $quiz = new Quiz();
        $quiz->course_id = $input['course_id'];
        $quiz->topic_id = $input['topic_id'];
        $quiz->question = $input['question'] ?? null;
        $quiz->audio = $name;
        $quiz->answer = $input['answer'] ?? null;
        $quiz->type = $input['type'];
        $quiz->save();

      } elseif ($request->type == 'image') {
        $request->validate([
          'course_id' => 'required',
          'topic_id' => 'required',
          'question_img' => 'required|file|mimes:png,jpg,jpeg|max:10240',
          'question' => 'sometimes|max:500',
          'a' => 'required|max:200',
          'b' => 'required|max:200',
          'c' => 'required|max:200',
          'd' => 'required|max:200',
          'answer' => 'required|size:1',
        ], [
          'course_id.required' => __('Course is required'),
          'topic_id.string' => __('Quiz Topic is required'),
          'question.max' => __('Quiz question should not be more than 500 characters'),
          'a.required' => __('Option a is required'),
          'a.max' => __('Option a should not be more than 200 characters'),
          'b.required' => __('Option b is required'),
          'b.max' => __('Option b should not be more than 200 characters'),
          'c.required' => __('Option c is required'),
          'c.max' => __('Option c should not be more than 200 characters'),
          'd.required' => __('Option d is required'),
          'd.max' => __('Option d should not be more than 200 characters'),
          'answer.required' => __('Answer is required'),
          'answer.size' => __('Answer must contain only one letter'),
          'question_img.mimes' => __('Image file type should be one of :png, jpg or jpeg'),
          'question_img.max' => __('Image file type should be less than 10 MB'),
        ]);

        if ($request->hasFile('question_img')) {
          $uniqueId = uniqid();
          // $original_name = $request->file('question_img')->getClientOriginalName();
          // $size = $request->file('question_img')->getSize();
          $extension = $request->file('question_img')->getClientOriginalExtension();
          $name = Carbon::now()->format('Ymd') . '_' . $uniqueId . '.' . $extension;
          $path = $request->file('question_img')->move(public_path('files/images'), $name);
        }

        $input = $request->all();

        $quiz = new Quiz();
        $quiz->course_id = $input['course_id'];
        $quiz->topic_id = $input['topic_id'];
        $quiz->question = $input['question'] ?? null;
        $quiz->question_img = $name;
        $quiz->a = $input['a'];
        $quiz->b = $input['b'];
        $quiz->c = $input['c'];
        $quiz->d = $input['d'];
        $quiz->answer = $input['answer'];
        $quiz->type = $input['type'];
        $quiz->save();


      } elseif ($request->type == 'essay') {
        $request->validate([
          'course_id' => 'required',
          'topic_id' => 'required',
          'question' => 'required|max:500',
          'answer' => 'sometimes|max:500',
        ], [
          'course_id.required' => __('Course is required'),
          'topic_id.string' => __('Quiz Topic is required'),
          'question.required' => __('Quiz question is required'),
          'question.max' => __('Quiz question should not be more than 500 characters'),
          'answer.max' => __('Answer should not be more than 500 characters'),
        ]);

        $input = $request->all();

        $quiz = new Quiz();
        $quiz->course_id = $input['course_id'];
        $quiz->topic_id = $input['topic_id'];
        $quiz->question = $input['question'];
        $quiz->answer = $input['answer'] ?? null;
        $quiz->type = $input['type'];
        $quiz->save();
      }
    }

    return back()->with('success', trans('flash.AddedSuccessfully'));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Quiz  $quiz
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $topic = QuizTopic::findOrFail($id);
    $quizes = Quiz::where('topic_id', $topic->id)->get();
    return view('admin.course.quiz.index', compact('topic', 'quizes'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Quiz  $quiz
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $topic = QuizTopic::findOrFail($id);
    $editquizes = Quiz::where('$id', $topic->id)->get();
    return view('admin.course.quiz.index', compact('topic', 'editquizes'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Quiz  $quiz
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $question = Quiz::findOrFail($id);
    $input = $request->all();

    if ($question->type == 'mcq' || $question->type == null) {
      $request->validate([
        'question' => 'sometimes|max:500',
        'a' => 'sometimes|max:200',
        'b' => 'sometimes|max:200',
        'c' => 'sometimes|max:200',
        'd' => 'sometimes|max:200',
        'answer' => 'sometimes|size:1',
      ], [
        'question.max' => __('Quiz question should not be more than 500 characters'),
        'a.max' => __('Option a should not be more than 200 characters'),
        'b.max' => __('Option b should not be more than 200 characters'),
        'c.max' => __('Option c should not be more than 200 characters'),
        'd.max' => __('Option d should not be more than 200 characters'),
        'answer.size' => __('Answer must contain only one letter'),
      ]);

      if (isset($input['question'])) {
        $question->question = $input['question'];
      }

      if (isset($input['a'])) {
        $question->a = $input['a'];
      }

      if (isset($input['b'])) {
        $question->a = $input['b'];
      }

      if (isset($input['c'])) {
        $question->a = $input['c'];
      }

      if (isset($input['d'])) {
        $question->a = $input['d'];
      }

      if (isset($input['answer'])) {
        $question->answer = $input['answer'];
      }

      $question->save();

    } elseif ($question->type == 'audio') {
      $request->validate([
        'question' => 'sometimes|max:500',
        'answer' => 'sometimes|max:500',
        'audio' => 'sometimes|file|mimes:audio/mpeg,mpga,mp3,wav,aac|max:10240'
      ], [
        'question.required' => __('Quiz question is required'),
        'question.max' => __('Quiz question should not be more than 500 characters'),
        'answer.max' => __('Answer should not be more than 500 characters'),
        'audio.mimes' => __('Audio file type should be one of :mpeg, mpga, mp3, wav or aac'),
        'audio.max' => __('Audio file type should be less than 10 MB'),
      ]);

      if ($request->hasFile('audio')) {
        $audioPath = public_path("/files/audio" . $question->audio);
        if (File::exists($audioPath)) {
          File::delete($audioPath);
        }


        $uniqueId = uniqid();
        // $original_name = $request->file('audio')->getClientOriginalName();
        // $size = $request->file('audio')->getSize();
        $extension = $request->file('audio')->getClientOriginalExtension();
        $name = Carbon::now()->format('Ymd') . '_' . $uniqueId . '.' . $extension;
        $path = $request->file('audio')->move(public_path('files/audio'), $name);
        $question->audio = $name;
      }

      if (isset($input['question'])) {
        $question->question = $input['question'];
      }

      if (isset($input['answer'])) {
        $question->answer = $input['answer'];
      }

      $question->save();

    } elseif ($question->type == 'image') {
      $request->validate([
        'question_img' => 'sometimes|file|mimes:png,jpg,jpeg|max:10240',
        'question' => 'sometimes|max:500',
        'a' => 'sometimes|max:200',
        'b' => 'sometimes|max:200',
        'c' => 'sometimes|max:200',
        'd' => 'sometimes|max:200',
        'answer' => 'sometimes|size:1',
      ], [
        'question.max' => __('Quiz question should not be more than 500 characters'),
        'a.max' => __('Option a should not be more than 200 characters'),
        'b.max' => __('Option b should not be more than 200 characters'),
        'c.max' => __('Option c should not be more than 200 characters'),
        'd.max' => __('Option d should not be more than 200 characters'),
        'answer.size' => __('Answer must contain only one letter'),
        'question_img.mimes' => __('Image file type should be one of :png, jpg or jpeg'),
        'question_img.max' => __('Image file type should be less than 10 MB'),
      ]);

      if ($request->hasFile('question_img')) {
        $imagePath = public_path("/files/images" . $question->question_img);
        if (File::exists($imagePath)) {
          File::delete($imagePath);
        }

        $uniqueId = uniqid();
        // $original_name = $request->file('question_img')->getClientOriginalName();
        // $size = $request->file('question_img')->getSize();
        $extension = $request->file('question_img')->getClientOriginalExtension();
        $name = Carbon::now()->format('Ymd') . '_' . $uniqueId . '.' . $extension;
        $path = $request->file('question_img')->move(public_path('files/images'), $name);

        $question->question_img = $name;
      }

      if (isset($input['question'])) {
        $question->question = $input['question'];
      }

      if (isset($input['a'])) {
        $question->a = $input['a'];
      }

      if (isset($input['b'])) {
        $question->a = $input['b'];
      }

      if (isset($input['c'])) {
        $question->a = $input['c'];
      }

      if (isset($input['d'])) {
        $question->a = $input['d'];
      }

      if (isset($input['answer'])) {
        $question->answer = $input['answer'];
      }

      $question->save();
    } elseif ($question->type == 'essay') {

      $request->validate([
        'question' => 'sometimes|max:500',
        'answer' => 'sometimes|max:500',
      ], [
        'question.max' => __('Quiz question should not be more than 500 characters'),
        'answer.size' => __('Answer must contain only one letter'),
      ]);

      if (isset($input['question'])) {
        $question->question = $input['question'];
      }

      if (isset($input['answer'])) {
        $question->answer = $input['answer'];
      }

      $question->save();
    } else {
      return back()->with('error', trans('flash.NotFound'));
    }

    return back()->with('success', trans('flash.UpdatedSuccessfully'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Quiz  $quiz
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $question = Quiz::findOrFail($id);
    $question->delete();

    QuizAnswer::where('question_id', $id)->delete();

    return back()->with('delete', trans('flash.DeletedSuccessfully'));
  }


  public function importquiz()
  {
    return view('admin.course.quiz.importindex');
  }


  public function import(Request $request)
  {
    $validator = Validator::make(
      [
        'file' => $request->file,
        'extension' => strtolower($request->file->getClientOriginalExtension()),
      ],
      [
        'file' => 'required',
        'extension' => 'required|in:xlsx,xls,csv',
      ]

    );


    if ($validator->fails()) {
      return back()->withErrors('Invalid file type!');
    }

    if (!$request->has('file')) {

      return back()->withErrors('Please choose a file !');
    }

    $fileName = time() . '.' . $request->file->getClientOriginalExtension();

    if (!is_dir(public_path() . '/excel')) {
      mkdir(public_path() . '/excel');
    }

    $request->file->move(public_path('excel'), $fileName);

    $lang = Session::get('changed_language');



    $quiz_import = (new FastExcel)->import(public_path() . '/excel/' . $fileName);


    if (count($quiz_import) > 0) {

      try {


        foreach ($quiz_import as $key => $row_fetch) {



          $line_number = $key + 1;

          $course_title = $row_fetch['Course'];

          $course_id = Course::whereRaw("JSON_EXTRACT(title, '$.$lang') = '$course_title'")->first();

          $quiz_topic = $row_fetch['QuizTopic'];

          $topic_id = QuizTopic::whereRaw("JSON_EXTRACT(title, '$.$lang') = '$quiz_topic'")->first();

          $quiz_question = $row_fetch['Question'];

          $option_A = $row_fetch['A'];

          $option_B = $row_fetch['B'];

          $option_C = $row_fetch['C'];

          $option_D = $row_fetch['D'];


          $correct_answer = $row_fetch['CorrectAnswer'];



          $product = Quiz::create([

            'course_id' => $course_id->id,
            'topic_id' => $topic_id->id,
            'question' => $quiz_question,
            'a' => $option_A,
            'b' => $option_B,
            'c' => $option_C,
            'd' => $option_D,
            'answer' => $correct_answer,

          ]);



        }

      } catch (\Swift_TransportException $e) {

        $file = @file_get_contents(public_path() . '/excel/' . $fileName);

        if ($file) {
          unlink(public_path() . '/excel/' . $fileName);
        }

        \Session::flash('delete', $e->getMessage());
        return back();
      }

    } else {

      $file = @file_get_contents(public_path() . '/excel/' . $fileName);

      if ($file) {
        unlink(public_path() . '/excel/' . $fileName);
      }

      return back()->with('success', trans('flash.AddedSuccessfully'));
    }


    return back()->with('success', trans('flash.AddedSuccessfully'));
  }


  public function quizreview()
  {

    $answers = QuizAnswer::where('type', '1')->get();
    return view('admin.course.quiz.review.index', compact('answers'));

  }


  public function quizreviewQuick(Request $request)
  {

    $user = QuizAnswer::find($request->id);

    $user->txt_approved = $request->status;

    $user->save();
    return response()->json($request->all());



  }

  public function status(Request $request)
  {
    $user = QuizAnswer::find($request->id);
    $user->status = $request->status;
    $user->save();
    return back()->with('success', trans('flash.UpdatedSuccessfully'));


  }

}
