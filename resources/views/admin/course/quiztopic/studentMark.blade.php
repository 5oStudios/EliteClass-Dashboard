@extends('admin.layouts.master')
@section('title', 'Report')
@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Quiz-remark') }}
        @endslot

        @slot('menu1')
            <a href="{{ url('/course/create/' . $course['id']) }}">
                {{ $course['title'] }}
            </a>
        @endslot
        @slot('menu3')
            <a href="{{ url('/show/quizreport/' . $topic['id']) }}">
                {{ $topic['title'] }}
            </a>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="text-center">
            <h5>student name : {{ $student['fname'] . ' ' . $student['lname'] }}</h5>
        </div>
        <div class="row">
            @if ($questions)
                @foreach ($questions as $key => $question)
                    <div class="col-md-6">
                        <div class="card mt-2 mb-2">
                            <div class="card-body">
                                <h5 class="card-title">{{ $question['quiz']['question'] }}</h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    @if ($question['quiz']['type'] == 'audio')
                                        <audio controls id="audioPreview{{ $key }}"
                                            src="{{ env('APP_URL') . '/files/audio/' . $question['quiz']['audio'] }}"></audio>
                                    @else
                                        {{ __('adminstaticword.essay') }}
                                    @endif
                                </h6>
                                <p class="card-text">answer: {{ $question['user_answer'] }}</p>
                                <div class="w-50 mx-auto">
                                    <button type="button" class="btn btn-success"
                                        onclick="handleCorrectButtonClick({{ $course['id'] }},'{{ $topic['id'] }}', '{{ $student['id'] }}', '{{ $question['question_id'] }}', '1')">Correct</button>
                                    <a href="{{ url('/manual-grading/course/' . $course['id'] . '/topic/' . $topic['id'] . '/student/' . $student['id'] . '/question/' . $question['question_id'] . '/grade/' . '0') }}"
                                        class="card-link"><button type="button"
                                            class="btn btn-danger">Incorrect</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

<script>
    function handleCorrectButtonClick(courseId, topicId, studentId, questionId, grade) {

        // Assuming you want to send the following data in the request body
        const accessToken = getCookie('access_token');
        const bodyRequest = {
            course_id: courseId,
            topic_id: topicId,
            student_id: studentId,
            question_id: questionId,
            grade
        }
        console.log(bodyRequest);
        fetch('/manual-grading/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${accessToken}`,
                },
                body: JSON.stringify({
                    bodyRequest
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
</script>
