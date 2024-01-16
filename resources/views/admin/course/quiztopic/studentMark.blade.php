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
                
                    <div class="col-md-6 ">
                        <div class="card mt-2 mb-2">
                            <div class="card-body">

                                <h5 class="card-title">{{ $question['quiz']['question'] }}    <span>
                                    @if($question['grade'] == 1) 
                                        <span><i class="text-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="5 13 9 17 19 7"></polyline>
                                                </svg>
                                                </i></span>
                                                @elseif($question['grade'] === null) <span><i class="text-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="12" y1="16" x2="12" y2="12" />
                                            <line x1="12" y1="8" x2="12" y2="8" />
                                        </svg>

                                        </i></span>
                                        @elseif($question['grade'] == 0) <span><i class="text-danger"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="14.5" y1="9.8" x2="9.8" y2="14.5" />
                                                    <line x1="9.8" y1="9.8" x2="14.5" y2="14.5" />
                                                    </svg></i></span>
                                       
                                        @else 
                                    @endif
                                </span></h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    @if ($question['quiz']['type'] == 'audio')
                                        <audio controls id="audioPreview{{ $key }}"
                                            src="{{ env('APP_URL') . '/files/audio/' . $question['quiz']['audio'] }}"></audio>
                                    @else
                                        {{ __('adminstaticword.essay') }}
                                    @endif
                                </h6>
                                <p class="card-text">answer: {{ $question['user_answer'] }}</p>
                                <div class="d-flex  justify-content-between align-items-center">
                                     <form id="demo-form" method="post"  action="{{url('/manual-grading/')}}
                                            "data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input type="text" name="course_id" value="{{$course['id']}}" hidden>
                                            <input type="text" name="topic_id" value="{{$topic['id']}}" hidden>
                                            <input type="text" name="student_id" value="{{$student['id']}}" hidden>
                                            <input type="text" name="answer_id" value="{{$question['id']}}" hidden>
                                            <input type="text" name="grade" value="1" hidden>
                                            <button type="submit" class="btn btn-success">
                                                <i >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="5 13 9 17 19 7"></polyline>
                                                </svg>
                                                </i>
                                            </button>
                                     </form>    
                                 <form id="demo-form" method="post"  action="{{url('/manual-grading/')}}
                                         "data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
                                          {{ csrf_field() }}
                                          <input type="text" name="course_id" value="{{$course['id']}}" hidden>
                                          <input type="text" name="topic_id" value="{{$topic['id']}}" hidden>
                                          <input type="text" name="student_id" value="{{$student['id']}}" hidden>
                                          <input type="text" name="answer_id" value="{{$question['id']}}" hidden>
                                          <input type="text" name="grade" value="0" hidden>
                                        <button type="submit" class="btn btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="14.5" y1="9.8" x2="9.8" y2="14.5" />
                                                    <line x1="9.8" y1="9.8" x2="14.5" y2="14.5" />
                                                    </svg>
                                        </button>
                                 </form>
                                </div>
                            </div>
                        </div>
                    </div>
                
                @endforeach
            @endif
            </div>
          <!-- Button trigger modal -->
          <div class="d-flex justify-content-center align-items-center">
            @if(is_null($remark))
              <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#feedbackModal">
              {{__(leave_feedback)}}
              </button>
            @else
              <button type="button" class="btn btn-warning " data-toggle="modal" data-target="#feedbackModalUpdate">
              {{__(update_feedback)}}
              </button>
            @endif
          </div>
          @if(is_null($remark))
        
            <!-- Modal -->
            <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">{{__(leave_feedback)}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post"  action="{{url('/remark')}}
                        "data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="text" name="student_id" value="{{$student['id']}}" hidden>
                        <input type="text" name="topic_id" value="{{$topic['id']}}" hidden>
                        <div class="form-group">
                            <label for="feedbackTextArea">{{__(feedback)}}</label>
                            <textarea name="content" style="border:black solid 1px !important" class="form-control" id="feedbackTextArea" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__(Close)}}</button>
                            <button type="submit" class="btn btn-success">{{__(Submit)}}</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
            </div>
          @else
            <!-- Modal -->
            <div class="modal fade" id="feedbackModalUpdate" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="feedbackModalLabel">{{__(update_feedback)}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST"  action="{{url('/remark/'.$remark->id)}}
                                    "data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
                                    @csrf
                                    @method("PATCH")
                                    <input type="text" name="student_id" value="{{$student['id']}}" hidden>
                                    <input type="text" name="topic_id" value="{{$topic['id']}}" hidden>
                                    <div class="form-group">
    <label for="feedbackTextArea">{{__(feedback)}}</label>
    <textarea name="content" id="content" style="border:black solid 1px !important" class="form-control" id="feedbackTextArea" rows="3">{{ old('content', $remark->content) }}</textarea>
</div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__(Close)}}</button>
                                        <button type="submit" class="btn btn-success">{{__(Submit)}}</button>
                                    </div>
                                </form>
                            </div>
                            </div>
                  </div>
            </div>
          @endif
    </div>
          
@endsection


