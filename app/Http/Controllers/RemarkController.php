<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Remark;

class RemarkController extends Controller
{

    public function index()
    {
        dd('from index');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'student_id' => 'required|integer|exists:users,id',
            'topic_id' => 'required|integer|exists:quiz_topics,id',
            'content' => 'required|string',
        ];

        $customMessages = [
            'student_id.required' => 'The student is required.',
            'topic_id.required' => 'The topic is required.',
            'content.required' => 'The content is required.',
            'student_id.exists' => 'The student not found.',
            'topic_id.exists' => 'The topic not found.',
        ];
        $request->validate($rules, $customMessages);

        Remark::create([
            'student_id' => $request->student_id,
            'topic_id' => $request->topic_id,
            'instructor_id' => $user->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'FOL El FOL');
    }

    public function update(Request $request, $id)
    {
        dd('from update', $request, $id);
    }

    public function delete($id)
    {
        dd('from delete', $id);
    }
}
