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
use App\User;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = QuestionnaireCourse::with('course:id,title')
            ->with('questionnaire:id,title')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])->paginate();

        // $questionnaires = QuestionnaireCourse::where('course_id', 1119)->with('questionnaire:id,title')->get(['id', 'course_id', 'questionnaire_id', 'appointment']);
        // $questionnaires = $questionnaires->map(function ($item) {
        //     return [
        //         'id' => $item->id,
        //         'appointment' => $item->appointment,
        //         'questionnaire_title' => $item->questionnaire->title,
        //         'course_id' => $item->course_id,
        //         'questionnaire_id' => $item->questionnaire->id
        //     ];
        // });
        return $questionnaires;
    }

    public function store(Request $request)
    {

        // dd($request->all());
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

        return back();
    }

    public function show($id)
    {
        $questionnaire = QuestionnaireCourse::where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first();

        if (!$questionnaire) {
            return response()->json([
                "message" => "No questionnaire with this id"
            ], 404);
        }
        $questionnaire = $questionnaire->toArray();


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
        $questionnaire = QuestionnaireCourse::where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first();

        if (!$questionnaire) {
            return response()->json([
                "message" => "No questionnaire with this id"
            ], 404);
        }
        $questionnaire = $questionnaire->toArray();


        $questions = [];
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            $questions[] = [
                'id' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'],
                'title' => $questionnaire['questionnaire']['question_bonds'][$i]['question']['title']
            ];
        }

        $distinctUsers = QuestionnaireAnswer::groupBy('student_id')->get(['student_id', 'answer_date']);
        if ($distinctUsers) {
            $distinctUsers = $distinctUsers->toArray();
        }
        // dd($distinctUsers);

        $students = [];
        foreach ($distinctUsers as $distinct) {
            $user = User::where('id', $distinct['student_id'])->first();
            $answers = QuestionnaireAnswer::where('student_id', $user['id'])
                ->where('questionnaire_course_id', $id)->get();
            if ($answers) {
                $answers = $answers->toArray();
            } else {
                $answers = [];
            }
            if ($user) {
                $user = $user->toArray();
                $students[] = [
                    'id' => $user['id'],
                    'fname' => $user['fname'],
                    'lname' => $user['lname'],
                    'email' => $user['email'],
                    'answer_date' => $distinct['answer_date'],
                    'answers' => $answers,
                ];
            }
        }

        $summary = [];
        foreach ($questions as $question) {
            // dd($question);
            $count = QuestionnaireAnswer::where('question_id', $question['id'])
                ->where('questionnaire_course_id', $id)->count();
            $sum = QuestionnaireAnswer::where('question_id', $question['id'])
                ->where('questionnaire_course_id', $id)->sum('rate');
            // dd($sum / $count);
            $summary[] = [
                'id' => $question['id'],
                'title' => $question['title'],
                'average' => $sum / $count
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
            'students' => $students,
            'summary' => $summary,
        ];

        $questionnaire = $result;

        //dd($questionnaire);

        return view('admin.course.questionnaire.questionnaire', compact('questionnaire'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'appointment' => 'required|date_format:Y-m-d',
            'title' => 'required|string|max:250',
            'questions' => 'required|array|min:1',
            'questions.id' => 'nullable|integer|min:0',
            'questions.title' => 'nullable|string|max:250',
        ]);

        $questionnaire = QuestionnaireCourse::where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first();

        if (!$questionnaire) {
            return response()->json([
                "message" => "No questionnaire with this id"
            ], 404);
        }
        $questionnaire = $questionnaire->toArray();
        // dd($questionnaire);

        QuestionnaireCourse::where('id', $id)->update([
            "appointment" => $request->appointment
        ]);

        Questionnaire::where('id', $questionnaire['questionnaire']['id'])->update([
            'title' => $request->title,
        ]);

        $questions = [];
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            $questions[] = $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'];
        }
        // dd($questions);

        $takenQuestions = [];
        foreach ($request->questions as $question) {
            $takenQuestions[] = $question['id'];
            if (in_array($question['id'], $questions)) {
                //if exist update
                QuestionnaireQuestion::where('id', $question['id'])->update([
                    'title' => $question['title'],
                ]);

            } else {
                //if not exist create
                $qq = QuestionnaireQuestion::create([
                    'title' => $question['title']
                ]);

                QuestionnaireQuestionBond::create([
                    'questionnaire_id' => $questionnaire['questionnaire']['id'],
                    'question_id' => $qq->id,
                ]);
            }
        }

        foreach ($questions as $question) {
            if (!in_array($question, $takenQuestions)) {
                QuestionnaireQuestionBond::where('question_id', $question)->delete();
                QuestionnaireQuestion::where('id', $question);
            }
        }

        return back();
    }

    public function destroy($id)
    {
        $questionnaire = QuestionnaireCourse::where('id', $id)
            ->with('course:id,title')
            ->with('questionnaire.questionBonds.question')
            ->select(['id', 'course_id', 'questionnaire_id', 'appointment'])
            ->first();
        if (!$questionnaire) {
            return back();
        }
        $questionnaire = $questionnaire->toArray();

        QuestionnaireAnswer::where('questionnaire_course_id', $id)->delete();
        QuestionnaireQuestionBond::where('questionnaire_id', $questionnaire['questionnaire']['id'])->delete();
        QuestionnaireCourse::where('id', $id)->delete();
        Questionnaire::where('id', $questionnaire['questionnaire']['id'])->delete();
        for ($i = 0; $i < count($questionnaire['questionnaire']['question_bonds']); $i++) {
            QuestionnaireQuestion::
                where('id', $questionnaire['questionnaire']['question_bonds'][$i]['question']['id'])->delete();
        }
        return back();
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
