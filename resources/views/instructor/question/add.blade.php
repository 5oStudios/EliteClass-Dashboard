@extends('admin.layouts.master')
@section('title','Create a new question')
@section('breadcum')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Question') }}
@endslot

@slot('menu1')
{{ __('Question') }}
@endslot

@slot('button')

<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{url('instructorquestion')}}" class="float-right btn btn-primary-rgba mr-2"><i
        class="feather icon-arrow-left mr-2"></i>{{__("Back")}}</a> </div>
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
          <h5 class="box-tittle">{{ __('adminstaticword.Add') }} {{ __('adminstaticword.Question') }}</h5>
        </div>
        <div class="card-body">
          <form autocomplete="off" id="demo-form2" method="post" action="{{ route('instructorquestion.store') }}" data-parsley-validate class="form-horizontal form-label-left">
            {{ csrf_field() }}
            

            <input type="hidden" name="instructor_id" class="form-control" value="{{ Auth::User()->id }}"  />

            <div class="row"> 
              <div class="col-md-12">
                <label for="exampleInputSlug">{{ __("Course") }} <span class="redstar">*</span></label>
                <select name="course_id" class="form-control select2" required>
                  <option value="none" selected disabled hidden> 
                    Select an Option 
                  </option>
                  @foreach($course as $cor)
                      <option value="{{ $cor->id }}">{{ $cor->title }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- <div class="row"> 
              <div class="col-md-12">
                <select name="user_id" class="form-control display-none">
                  <option  value="{{ Auth::user()->id }}">{{ Auth::user()->fname }}</option>
                </select>
              </div>
            </div> --}}
            <br> 
            
            <div class="row">  
              <div class="col-md-12">
                <label for="exampleInputDetails">{{__("Question")}}:<sup class="redstar">*</sup></label>
                <textarea name="question" rows="3" onkeyup="countChar(this)" class="form-control" placeholder="{{__('Enter your question')}}" required></textarea>
                <div id="count" class="pull-right">
                    <span id="current_count">0</span>
                    <span id="maximum_count">/ 300</span>
                </div>
              </div>
            </div>
            <br>
            
            <div class="row">
              <div class="col-md-12">
                <label for="exampleInputDetails">{{__("Status")}}:</label>               
                  
                  <input id="c2222" type="checkbox" class="custom_toggle" name="status" checked />

                  <label class="tgl-btn" data-tg-off="Deactive" data-tg-on="Active" for="c2222"></label>
                <input type="hidden" name="status" value="0" id="t2222">
              </div>
            </div>
            <br>
          
            <div class="form-group">
              <button type="reset" class="btn btn-danger"><i class="fa fa-ban"></i> {{__("Reset")}}</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i>
                {{__("Create")}}</button>
            </div>

            <div class="clear-both"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


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
