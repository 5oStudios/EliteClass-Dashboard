<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Questionnaire;
use App\QuestionnaireQuestion;
use App\QuestionnaireQuestionBond;
use App\QuestionnaireCourse;

class QuestionnaireController extends Controller
{
    public function index($id)
    {
        $questionnaires = QuestionnaireCourse::where('course_id', $id)->with('questionnaire:id,title')->get(['course_id', 'questionnaire_id', 'appointment']);

        // return view('admin.questionnaire.index');

        return $questionnaires;
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment' => 'required|date_format:Y-m-d H:i:s',
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
}
