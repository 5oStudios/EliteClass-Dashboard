@extends('admin.layouts.master')
@section('title','Edit QuestionAnswer')
@section('maincontent')
​
@component('components.breadcumb',['thirdactive' => 'active'])
​
@slot('heading')
{{ __('Home') }}
@endslot
​
@slot('menu1')
{{ __('Admin') }}
@endslot
​
@slot('menu2')
{{ __(' Edit Question Answer') }}
@endslot
​
@slot('button')
<div class="col-md-5 col-lg-5">
  <a href="{{ url('course/create/'. $que->courses->id) }}" class="float-right btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
</div>
@endslot
​
@endcomponent
<div class="contentbar">
  <div class="row">
    <div class="col-lg-12">
      @if ($errors->any())  
      <div class="alert alert-danger" role="alert">
          @foreach($errors->all() as $error)     
          <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true" style="color:red;">&times;</span></button></p>
          @endforeach  
      </div>
      @endif
      <div class="card m-b-30">
        <div class="card-header">
          <h5 class="card-title">{{ __('adminstaticword.Edit') }} {{ __('Question') }}</h5>
        </div>
        <div class="card-body ml-2">
          <form autocomplete="off" id="demo-form" method="post" action="{{url('questionanswer/'.$que->id)}}" data-parsley-validate class="form-horizontal form-label-left">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div style="display:none;">
                
              <input type="hidden" name="user_id" class="form-control" value="{{ Auth::User()->id }}"  />
          
              <select name="course_id" class="form-control select2 d-none">
                @foreach($courses as $cou)
                <option class="d-none" value="{{ $cou->id }}" {{$que->courses->id == $cou->id  ? 'selected' : ''}}>{{ $cou->title}}</option>
                @endforeach
              </select>
        
              <select name="instructor_id" class="form-control col-md-7 col-xs-12 display-none">
                @foreach($user as $cu)
                  <option class="display-none" value="{{ $cu->id }}" {{$que->courses->id == $cou->id  ? 'selected' : ''}}>{{ $cu->fname}}</option>
                @endforeach
              </select>
          
            </div>
                 
            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputTit1e">{{ __('adminstaticword.Question') }}:<span class="redstar">*</span></label>
                <textarea name="question" onkeyup="countChar(this)" rows="3" class="form-control" placeholder="{{__('Enter Your Question')}}" maxlength="300" autofocus required >{{$que->question}}</textarea>
                <div id="count" class="pull-right">
                    <span id="current_count">{{strlen($que->question)}}</span>
                    <span id="maximum_count">/ 300</span>
                </div>
              </div>
          
              <div class="col-md-12 mt-3">
                <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
               
                    <label class="switch">
                      <input class="slider" type="checkbox" name="status" {{ $que->status==1 ? 'checked' : '' }} />
                      <span class="knob"></span>
                    </label>
              </div>
            </div> 
            <br>
              
            <div class="form-group">
              <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                {{ __('Reset') }}</button>
              <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                {{ __('Update') }}</button>
            </div>
​
            <div class="clear-both"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
​
@endsection

@section('script')
<script>
  
  function countChar(val){
     
    var len = val.value.length;
     if (len > 300) {
              val.value = val.value.substring(0, 299);
     } else {
              $('#current_count').text(len);
     }     
  };
</script>
@endsection