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
            // 'questionnaire_id' => $questionnaire['questionnaire_id'],
            'questionnaire_title' => $questionnaire['questionnaire']['title'],
            'questionnaire_appointment' => $questionnaire['appointment'],
            'questions' => $questions,
        ];

        $questionnaire = $result;

        return view('admin.course.questionnaire.questionnaire', $questionnaire);
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

        $questionnaire = QuestionnaireCourse::Where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])->first();

        if (!$questionnaire) {
            return response()->json([
                'message' => "No questionnaire with this id"
            ], 404);
        }
        $questionnaire = $questionnaire->toArray();

        //check if answered before
        $answeredBefore = QuestionnaireAnswer::where('questionnaire_course_id', $id)
            ->where('student_id', Auth::user()->id)->first();
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
                'student_id' => Auth::user()->id,
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
        // Get all courses for the user
        $courseIds = CourseProgress::where('user_id', Auth::user()->id)->pluck('course_id')->toArray();

        // Initialize an array to store questionnaire IDs
        $questionnaireIds = [];

        // Loop through each course
        foreach ($courseIds as $courseId) {
            // Retrieve questionnaires for the current course with date filter
            $questionnaires = QuestionnaireCourse::where('course_id', $courseId)
                ->whereDate('appointment', '<=', now())
                ->whereNotExists(function ($query) {
                    $query->from('questionnaires_answers')
                        ->whereColumn('questionnaire_course_id', 'questionnaires_courses.id')
                        ->where('student_id', Auth::user()->id);
                })
                ->get(['id']);

            // Add questionnaire IDs to the array
            $questionnaireIds = array_merge($questionnaireIds, $questionnaires->pluck('id')->toArray());
        }

        if (empty($questionnaireIds)) {
            return response()->json([
                'message' => 'No questionnaires required',
                'questionnaires' => []
            ]);
        }

        $questionnaires = [];

        foreach ($questionnaireIds as $questionnaireId) {
            $questionnaire = QuestionnaireCourse::with([
                'course:id,title',
                'questionnaire.questionBonds.question:id,title'
            ])->findOrFail($questionnaireId);

            $result = [
                'id' => $questionnaire->id,
                'course_id' => $questionnaire->course_id,
                'course_title' => $questionnaire->course->title,
                // 'questionnaire_id' => $questionnaire->questionnaire_id,
                'questionnaire_title' => $questionnaire->questionnaire->title,
                'questionnaire_appointment' => $questionnaire->appointment,
                'questions' => $questionnaire->questionnaire->questionBonds->map(function ($bond) {
                    return [
                        'id' => $bond->question->id,
                        'title' => $bond->question->title
                    ];
                })->toArray(),
            ];

            $questionnaires[] = $result;
        }

        return response()->json([
            'message' => 'Required questionnaires',
            'questionnaires' => $questionnaires
        ]);
    }
}
