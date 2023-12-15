@extends('admin.layouts.master')
@section('title','Create a new answer')
@section('breadcum')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Answer') }}
@endslot

@slot('menu1')
{{ __('Answer') }}
@endslot

@slot('button')

<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{url('instructoranswer')}}" class="float-right btn btn-dark-rgba mr-2"><i
        class="feather icon-arrow-left mr-2"></i>{{__('Back')}}</a> </div>
</div>

@endslot
@endcomponent
<div class="contentbar">
  @if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
  <div class="row">
    <div class="col-lg-12">
      <div class="card m-b-30">
        <div class="card-header">
          <h5 class="box-tittle">{{ __('adminstaticword.Add') }} {{ __('adminstaticword.Answer') }}</h5>
        </div>
        <div class="card-body">
          <form id="demo-form2" method="post" action="{{url('instructoranswer/')}}" data-parsley-validate class="form-horizontal form-label-left">
            {{ csrf_field() }}
            

            <input type="hidden" name="instructor_id" value="{{Auth::user()->id}}" />
            <input type="hidden" name="ans_user_id" value="{{Auth::user()->id}}" />
       
            <div class="row">
              <div class="col-md-12">
                <label  for="exampleInputTit1e"> {{ __('adminstaticword.Select') }} {{ __('adminstaticword.Question') }}:<sup class="redstar">*</sup></label>
                <br>
                <select name="question_id" required class="form-control select2">
                  <option value="none" selected disabled hidden> 
                     {{ __('adminstaticword.SelectanOption') }}
                  </option>
                  @foreach($questions as $ques)
                    <option value="{{ $ques->id }}">{{ $ques->question}}</option>
                  @endforeach
                </select>
              </div>
              @foreach($questions as $ques)
              <input type="hidden" name="ques_user_id"  value="{{$ques->user_id}}" />
              <input type="hidden" name="course_id"  value="{{$ques->course_id}}" />
              @endforeach
            </div>
            <br>

            <div class="row">
              <div class="col-md-12">
                <label for="exampleInput">{{ __('adminstaticword.Answer') }}:<sup class="redstar">*</sup></label>
                <textarea name="answer" rows="3" onkeyup="countChar(this)" class="form-control" placeholder="{{__('Please Enter Your Answer')}}"></textarea>
                <div id="count" class="pull-right">
                    <span id="current_count">0</span>
                    <span id="maximum_count">/ 300</span>
                </div>
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-md-6">
                <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label>
                <input id="c12" type="checkbox" class="custom_toggle" name="status" checked />

                  <label class="tgl-btn" data-tg-off="Deactive" data-tg-on="Active" for="c12"></label>
               
                <input type="hidden" name="status" value="1" id="t12">
              </div>
            </div>
            <br>
    
            <div class="form-group">
              <button type="reset" class="btn btn-danger"><i class="fa fa-ban"></i> Reset</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i>
                Create</button>
            </div>

            <div class="clear-both"></div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- @extends('admin/layouts.master')
@section('title', 'Add Answer - Instructor')
@section('body')
@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
<section class="content">
  @include('admin.message')
  <div class="row">
    <!-- left column -->
    <div class="col-xs-12"> 
      <!-- general form elements -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"> {{ __('adminstaticword.Add') }} {{ __('adminstaticword.Answer') }}</h3>
        </div>
        <br>
        <div class="box-body">
          <div class="form-group">
            <form id="demo-form2" method="post" action="{{url('instructoranswer/')}}" data-parsley-validate class="form-horizontal form-label-left">
                {{ csrf_field() }}
                

                <input type="hidden" name="instructor_id" value="{{Auth::user()->id}}" />
                <input type="hidden" name="ans_user_id" value="{{Auth::user()->id}}" />
           
                <div class="row">
                  <div class="col-md-12">
                    <label  for="exampleInputTit1e"> {{ __('adminstaticword.Select') }} {{ __('adminstaticword.Question') }}:<sup class="redstar">*</sup></label>
                    <br>
                    <select name="question_id" required class="form-control col-md-7 col-xs-12 js-example-basic-single">
                      <option value="none" selected disabled hidden> 
                         {{ __('adminstaticword.SelectanOption') }}
                      </option>
                      @foreach($questions as $ques)
                        <option value="{{ $ques->id }}">{{ $ques->question}}</option>
                      @endforeach
                    </select>
                  </div>
                  @foreach($questions as $ques)
                  <input type="hidden" name="ques_user_id"  value="{{$ques->user_id}}" />
                  <input type="hidden" name="course_id"  value="{{$ques->course_id}}" />
                  @endforeach
                </div>
                <br>

                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInput">{{ __('adminstaticword.Answer') }}:<sup class="redstar">*</sup></label>
                    <textarea name="answer" rows="4" class="form-control" placeholder="{{__('Please Enter Your Answer')}}"></textarea>
                  </div>
                </div>
                <br>

                <div class="row">
                  <div class="col-md-6">
                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label>
                    <li class="tg-list-item">   
                      <input class="tgl tgl-skewed" id="c12"  type="checkbox"/>
                      <label class="tgl-btn" data-tg-off="Deactive" data-tg-on="Active" for="c12"></label>
                    </li>
                    <input type="hidden" name="status" value="1" id="t12">
                  </div>
                </div>
                <br>
        
                <div class="box-footer">
                  <button type="submit" value="Add Answer" class="btn btn-md col-md-3 btn-primary">+ {{ __('adminstaticword.Save') }}</button>
                </div>

              </form>
          </div>
        </div>
        <!-- /.box -->
      </div>
      <!-- /.box -->
    </div>
    <!--/.col (right) -->
  </div>
  <!-- /.row -->
</section> 
@endsection --}}


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