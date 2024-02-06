<?php

namespace App\Http\Controllers;

use App\Questionnaire;
use App\CourseProgress;
use App\QuestionnaireAnswer;
use App\QuestionnaireCourse;
use Illuminate\Http\Request;
use App\QuestionnaireQuestion;
use App\QuestionnaireQuestionBond;
use Illuminate\Support\Facades\Auth;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = QuestionnaireCourse::with('course:id,title')->with('questionnaire:id,title')->select(['id', 'course_id', 'questionnaire_id', 'appointment'])->paginate();

        return $questionnaires;


    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment' => 'required|date_format:Y-m-d',
            'course_id' => 'required|integer|exists:courses,id',
            'title' => 'required|string|max:250',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string|max:250',
        ]);

        $questionnaire = Questionnaire::create([
            'title' => $request->title
        ]);

        foreach ($request->questions as $q) {
            $question = QuestionnaireQuestion::create([
                'title' => $q
            ]);

            QuestionnaireQuestionBond::create([
                'questionnaire_id' => $questionnaire->id,
                'question_id' => $question->id
            ]);
        }

        QuestionnaireCourse::create([
            'questionnaire_id' => $questionnaire->id,
            'course_id' => $request->course_id,
            'appointment' => $request->appointment
        ]);

        return ['msg' => "created successfully"];
    }

    public function show($id)
    {
        $questionnaire = QuestionnaireCourse::where('id', $id)->exists();
        if (!$questionnaire) {
            return response()->json([
                "message" => "No questionnaire with this id"
            ], 404);
        }

        $questionnaire = QuestionnaireCourse::where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first()->toArray();


        $questions = [];
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            $questions[] = [
                'id' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'],
                'title' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['title']
            ];
        }

        $result = [
            'id' => $questionnaire['id'],
            'course_id' => $questionnaire['course_id'],
            'course_title' => $questionnaire['course']['title'],
            'questionnaire_id' => $questionnaire['questionnaire_id'],
            'questionnaire_title' => $questionnaire['questionnaire']['title'],
            'questionnaire_appointment' => $questionnaire['appointment'],
            'questions' => $questions,
        ];

        return $result;
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
    }

    public function answer(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:questionnaires_questions,id',
            'answers.*.rate' => 'required|integer|min:0|max:5',
            'answers.*.answer' => 'required|string|max:250',
        ]);

        $questionnaire = QuestionnaireCourse::findOrFail($id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])->first()->toArray();

        //check if answered before
        $answeredBefore = QuestionnaireAnswer::where('questionnaire_course_id', $id)->where('student_id', 5494)->first();
        // $answeredBefore = QuestionnaireAnswer::where('questionnaire_course_id', $id)->where('student_id', Auth::user()->id)->first();
        if ($answeredBefore) {
            return response()->json([
                'message' => "This questionnaire is answered before"
            ], 400);
        }

        //check questions number is correct
        if (count($questionnaire['questionnaire']['question_bonds']) != count($request->answers)) {
            $questionsLen = count($questionnaire['questionnaire']['question_bonds']);
            return response()->json([
                'message' => "Invalid data, answers length must be $questionsLen."
            ], 422);
        }

        //get all questions ids
        $questions = [];
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            $questions[] = $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'];
        }

        //question_id checks
        for ($i = 0; $i < count($request->answers); $i++) {
            if (!in_array($request->answers[$i]['question_id'], $questions)) {
                $id = $request->answers[$i]['question_id'];
                return response()->json([
                    'message' => "Invalid data, no question with id:$id in this questionnaire"
                ], 422);
            }
        }

        //create answers
        $current = now();
        for ($i = 0; $i < count($request->answers); $i++) {
            QuestionnaireAnswer::create([
                'questionnaire_course_id' => $id,
                'student_id' => 5494,
                // 'student_id' => Auth::user()->id,
                'question_id' => $request->answers[$i]['question_id'],
                'rate' => $request->answers[$i]['rate'],
                'answer' => $request->answers[$i]['answer'],
                'answer_date' => $current
            ]);
        }

        return response()->json([
            'message' => "Questionnaire answered successfully"
        ], 200);
    }

    public function getQuestionnairesForStudent()
    {
        $courses = CourseProgress::where(['user_id' => 5494])->get('course_id')->toArray();

        $questionnaire_ids = [];

        for ($i = 0; $i < count($courses); $i++) {
            $course_id = $courses[$i]['course_id'];

            //get questionnaire for this course with date filter
            $questionnaires = QuestionnaireCourse::where('course_id', $course_id)
                ->where('appointment', '<=', date('Y-m-d'))->get()->toArray();

            if ($questionnaires) {
                // dd($questionnaires);
                for ($i = 0; $i < count($questionnaires); $i++) {
                    //check if done by user or not
                    $answeredBefore = QuestionnaireAnswer::where('questionnaire_course_id', $questionnaires[$i]['id'])->where('student_id', 5494)->exists();
                    // dd($answeredBefore);

                    if (!$answeredBefore) {
                        $questionnaire_ids[] = $questionnaires[$i]['id'];
                        // dd($questionnaire_ids);
                    }
                }
            }
        }

        if (count($questionnaire_ids) == 0) {
            return response()->json([
                'message' => 'No questionnaires required',
                'questionnaire_ids' => $questionnaire_ids
            ]);
        }

        $questionnaire = QuestionnaireCourse::where('id', $questionnaire_ids[0])->exists();
        if (!$questionnaire) {
            return response()->json([
                "message" => "No questionnaire with this id"
            ], 404);
        }

        $questionnaire = QuestionnaireCourse::where('id', $questionnaire_ids[0])
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first()->toArray();


        $questions = [];
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            $questions[] = [
                'id' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'],
                'title' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['title']
            ];
        }

        $result = [
            'id' => $questionnaire['id'],
            'course_id' => $questionnaire['course_id'],
            'course_title' => $questionnaire['course']['title'],
            'questionnaire_id' => $questionnaire['questionnaire_id'],
            'questionnaire_title' => $questionnaire['questionnaire']['title'],
            'questionnaire_appointment' => $questionnaire['appointment'],
            'questions' => $questions,
        ];

        return response()->json([
            'message' => 'Required questionnaires',
            'questionnaire_ids' => $questionnaire_ids,
            'questionnaire' => $result
        ]);
    }
}
