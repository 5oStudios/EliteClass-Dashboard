@extends('admin.layouts.master')
@section('title','Report')
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
   {{ __('Report') }}
@endslot

@slot('menu1')
   {{ __('Report') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
<a href="{{ url('course/create/'. $topics->courses->id) }}" class="float-right btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
</div>
@endslot
@endcomponent



<div class="contentbar"> 
  <div class="row">
    <div class="col-lg-12">
          <div class="card m-b-30">
              <div class="card-header">
                  <h5 class="card-title">{{ __('All Report') }}</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                      <table id="quizreport-datatable" class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>{{ __('User') }}</th>
                              <th>{{ __('Email') }}</th>          
                              <th>{{ __('Quiz') }}</th>
                              <th>{{ __('Marks Get') }}</th>
                              <th>{{ __('Total Marks') }}</th>
                              <th >{{ __('Actions') }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if ($ans)
                              @foreach($ans as $key => $a)
                              @php $student = $a->user; @endphp
                                <tr>
                                  <td>
                                    {{$key+1}}
                                  </td>
                                  <td>{{$student->fname}}</td>
                                  <td>{{$student->email}}</td>               
                                  <td>{{$topics->title}}</td>
                                  <td>
                                    @php
                                     
                                    $last_attempt = \App\QuizAnswer::where('topic_id', $a->topic_id)->where('user_id', $a->user_id)->orderBy('attempt','desc')->first();
                                    $answer = \App\QuizAnswer::where('topic_id', $a->topic_id)->where('user_id', $a->user_id)->where('attempt',$last_attempt->attempt)->get();
                                    $mark = 0;

                                    foreach ($answer as $an) {
                                            $mark+=(strtolower($an->answer) == strtolower($an->user_answer))?1:0;
                                    }
                                     $correct = $mark*$topics->per_q_mark;
                                    @endphp
                                    {{$correct}}
                                  </td>
                                  <td>
                                    {{$c_que*$topics->per_q_mark}}
                                  </td>
                                  <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">          
                                          <a class="dropdown-item" href="{{url('manualGrading/course/'.$ans->course_id/topic/.$ans->topic_id/student/.$ans->user_id)}}"><i
                                            class="feather icon-edit mr-2"></i>{{ __('Mark') }}
                                          </a>
                                          
                                        </div>
                                    </div>
                                   </td>
                                </tr>
                              @endforeach
            
                            @endif
                          </tbody>
            </table>
            {{$ans}}
            <br>
            {{$student}}
            <br>
            {{$topics}}
            <br>
          </div>
      </div>
  </div>
</div>
<!-- End col -->
</div>
<!-- End row -->
</div>
@endsection

@section('script')
<script>
	$(function () {
        $('#quizreport-datatable').dataTable({
            language: {
                searchPlaceholder: "Search quiz report here"
            },
        });
    });
    

</script>
@endsection
