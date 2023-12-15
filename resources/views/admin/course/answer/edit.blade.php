@extends('admin.layouts.master')
@section('title',__('Edit Answer'))
@section('maincontent')

@component('components.breadcumb',['thirdactive' => 'active'])

@slot('heading')
{{ __('Answers') }}
@endslot

{{-- @slot('menu1')
{{ __('Admin') }}
@endslot --}}

@slot('menu2')
{{ __(' Edit Answer') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
  <a href="{{ url('courseanswer/'. $show->question_id) }}" class="float-right btn btn-primary mr-2"><i
      class="feather icon-arrow-left mr-2"></i>{{__('Back')}}</a>
</div>
@endslot

@endcomponent
 
<div class="contentbar">
   	<div class="row">
	    <div class="col-md-12">
	    	<div class="card m-b-30">
	           	<div class="card-header">
	          	<h3 class="card-box"> {{ __('adminstaticword.Edit') }} {{ __('adminstaticword.Answer') }}</h3>
	       		</div>
	          	<div class="card-body ml-2">
	          		<form action="{{route('courseanswer.update',$show->id)}}" method="POST" enctype="multipart/form-data">
		                {{ csrf_field() }}
		                {{ method_field('PUT') }}
						

						<input type="hidden" name="instructor_id" class="form-control" value="{{ Auth::User()->id }}"  />
						
		              
	                    <input type="hidden" value="{{ $show->course_id }}" autofocus name="course_id" type="text" class="form-control d-none" >


	                    <div class="row">
	                    	<div class="col-md-12">
	                    		<label for="exampleInput">{{ __('adminstaticword.Answer') }}:<sup class="redstar">*</sup></label>
	                  			<textarea name="answer" onkeyup="countChar(this)" rows="3" class="form-control" placeholder="{{__('Please Enter Your Answer')}}" required>{{ $show->answer }}</textarea>
								<div id="count" class="pull-right">
									<span id="current_count">{{strlen($show->answer)}}</span>
									<span id="maximum_count">/ 300</span>
								</div>
	                    	</div>
	                    </div>
		              	
		              	<br>


		              	<div class="form-group col-md-12">
                          <label class="text-dark" for="exampleInputDetails">{{ __('adminstaticword.Status') }} :</label><br>
                          <label class="switch">
                            <input class="slider" type="checkbox" name="status" {{ $show->status == '1' ? 'checked' : '' }} />
                            <span class="knob"></span>
                          </label>
                        </div>
		              	
						<div class="box-footer">
		              		<button value="" type="submit"  class="btn btn-md col-md-2 btn-primary-rgba">{{ __('adminstaticword.Update') }}</button>
		              	</div>

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
