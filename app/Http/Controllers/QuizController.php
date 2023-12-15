<?php

namespace App\Http\Controllers;

use App\Quiz;
use Illuminate\Http\Request;
use App\Course;
use App\QuizTopic;
use App\QuizAnswer;
use File;
use Image;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Session;
use DB;
use Spatie\Permission\Models\Role;


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
        

        if(isset($request->type)){

          $request->validate([
            'course_id' => 'required',
            'topic_id' => 'required',
            'question' => 'required|max:500',
            'type' => 'required',
          ]);
          

        }else{
          

          $request->validate([
            'course_id' => 'required',
            'topic_id' => 'required',
            'question' => 'required|max:500',
            'a' => 'required|max:200',
            'b' => 'required|max:200',
            'c' => 'required|max:200',
            'd' => 'required|max:200',
            'answer' => 'required|size:1',
          ],[
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

        }



        $input = $request->all();

        if(isset($request->type)){
          $input['type'] = '1';
        }else{
          $input['type'] = null;
        }

        if($file = $request->file('question_img')) 
        { 
          
          $path = 'images/quiz/';

          if(!file_exists(public_path().'/'.$path)) {
            
            $path = 'images/quiz/';
            File::makeDirectory(public_path().'/'.$path,0777,true);
          }    
          $optimizeImage = Image::make($file);
          $optimizePath = public_path().'/images/quiz/';
          $image = time().$file->getClientOriginalName();
          $optimizeImage->save($optimizePath.$image, 72);

          $input['question_img'] = $image;
          
        }

        
        $input['answer_exp'] = $request->answer_exp;
        $input['answer'] = strtolower($request->answer);
        Quiz::create($input);
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
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required|max:500',
          'a' => 'required|max:100',
          'b' => 'required|max:100',
          'c' => 'required|max:100',
          'd' => 'required|max:100',
          'answer' => 'required|size:1',
        ],[
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

        if($file = $request->file('question_img')) 
        { 
          
          $path = 'images/quiz/';

          if(!file_exists(public_path().'/'.$path)) {
            
            $path = 'images/quiz/';
            File::makeDirectory(public_path().'/'.$path,0777,true);
          }    
          $optimizeImage = Image::make($file);
          $optimizePath = public_path().'/images/quiz/';
          $image = time().$file->getClientOriginalName();
          $optimizeImage->save($optimizePath.$image, 72);

          $input['question_img'] = $image;
          
        }


        
        $input['answer_exp'] = $request->answer_exp;
        $input['answer'] = strtolower($request->answer);
        $question->update($input);
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
        

        if (count($quiz_import) > 0) 
        {

          try
          {
            

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

          }
          catch (\Swift_TransportException $e)
          {
            
              $file = @file_get_contents(public_path() . '/excel/' . $fileName);

              if ($file) {
                unlink(public_path() . '/excel/' . $fileName);
              }

              \Session::flash('delete', $e->getMessage());
              return back();
          }

        }
        else {
            
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
        
        $user =  QuizAnswer::find($request->id);

        $user->txt_approved = $request->status;

        $user->save();
        return response()->json($request->all());
    
       
      
    }

   public function status(Request $request)
    {
        $user = QuizAnswer::find($request->id);
        $user->status = $request->status;
        $user->save();
        return back()->with('success',trans('flash.UpdatedSuccessfully'));
        
        
    }

}
