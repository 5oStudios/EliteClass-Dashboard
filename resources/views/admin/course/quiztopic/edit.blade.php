@extends('admin.layouts.master')
@section('title','Edit Quiztopic')
@section('maincontent')
@component('components.breadcumb',['thirdactive' => 'active'])
@slot('heading')
{{ __('Home') }}
@endslot
@slot('menu1')
{{ __('Admin') }}
@endslot
@slot('menu2')
{{ __(' Edit Quiz Topic') }}
@endslot
@slot('button')
<div class="col-md-5 col-lg-5">
<a href="{{ url('course/create/'. $topic->courses->id) }}" class="float-right btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
</div>
@endslot
@endcomponent

<div class="contentbar">
  <div class="row">
    <div class="col-lg-12">
      @if ($errors->any())  
      <div class="alert alert-danger" role="alert">
        @foreach($errors->all() as $error)     
          <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-   label="Close">
          <span aria-hidden="true" style="color:red;">&times;</span></button></p>
        @endforeach  
      </div>
      @endif
      <div class="card m-b-30">
        <div class="card-header">
          <h5 class="card-title">{{ __('adminstaticword.Edit') }} {{ __('Quiz Topic') }}</h5>
        </div>
        <div class="card-body ml-2">
          <form autocomplete="off" id="demo-form2" method="POST" action="{{route('quiztopic.update', $topic->id)}}" data-parsley-validate class="form-horizontal form-label-left">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
           
           

            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputTit1e">{{ __('adminstaticword.QuizTopic') }}:<span class="redstar">*</span> </label>
                <input type="text" placeholder="{{__('Enter Quiz Topic')}}" class="form-control " name="title" id="exampleInputTitle" value="{{ $topic->title }}" required>
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputDetails">{{ __('adminstaticword.QuizDescription') }}:<sup class="redstar">*</sup></label>
                <textarea name="description" rows="3" class="form-control" placeholder="{{__('Enter Description')}}" required>{{ $topic->description }}</textarea>
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-md-6">
                <label for="exampleInputTit1e">{{ __('adminstaticword.PerQuestionMarks') }}:<span class="redstar">*</span> </label>
                <input type="number" min="0" placeholder="{{__('Enter Per Question Mark')}}" class="form-control " name="per_q_mark" id="exampleInputTitle" value="{{ $topic->per_q_mark }}" required>
              </div>
                <div class="col-md-6">
                    <label for="exampleInputTit1e">{{ __('adminstaticword.PassingPercentage') }}:<span class="redstar">*</span></label>
                    <input type="number" min="0" placeholder="{{__('Enter Passing Percentage')}}" class="form-control" name="p_percent" id="exampleInputTitle" value="{{$topic->p_percent}}" required>
                </div>
            </div>
            <br>

            <div class="row">
            </div>
            <br>

            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputTit1e">{{ __('adminstaticword.QuizTimer') }}:<span class="redstar">*</span> </label>
                <input type="number" min="0" placeholder="{{__('Enter Quiz Time in (mins) Eg:20')}}" class="form-control" name="timer" id="exampleInputTitle" value="{{ $topic->timer }}" required>
              </div>
            </div>
            <br>

<!--            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputTit1e">{{ __('adminstaticword.Days') }}:</label>
                <small>({{ __('Days after quiz will start when user enroll in course') }})</small>
                <input type="text" placeholder="Enter Due Days" class="form-control" name="due_days" id="exampleInputTitle" value="{{ $topic->due_days }}">
              </div>
            </div>
            <br>-->

            <div class="row">
              <!-- <div class="col-md-4">
                  <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }} :</label><br>
                    <label class="switch">
                    <input class="slider" type="checkbox" name="status" {{ $topic->status == '1' ? 'checked' : '' }} />
                    <span class="knob"></span>
                  </label>
              </div> -->

              <div class="col-md-4">
                <label for="exampleInputTit1e">{{ __('adminstaticword.QuizReattempt') }} :</label><br>
                  <label class="switch">
                    <input class="slider" type="checkbox" name="quiz_again" {{ $topic->quiz_again == '1' ? 'checked' : '' }} />
                    <span class="knob"></span>
                  </label>
              </div>

              <!-- <div class="col-md-4">
                <label for="exampleInputTit1e">{{ __('Quiz Type') }} :</label><br>
                  <label class="switch">
                    <input class="slider" type="checkbox" name="type" {{ $topic->type == '1' ? 'checked' : '' }} />
                    <span class="knob"></span>
                  </label>
              </div> -->
            </div>
            <br>
    
            <div class="form-group">
              <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                {{ __('Reset') }}</button>
              <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                {{ __('Update') }}</button>
            </div>
            <div class="clear-both"></div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

