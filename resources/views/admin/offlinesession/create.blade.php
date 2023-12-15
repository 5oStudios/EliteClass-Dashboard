@extends('admin.layouts.master')
@section('title', 'Create a new In-person session - Admin')
@section('maincontent')


@component('components.breadcumb',['fourthactive' => 'active'])
@slot('heading')
{{ __('In-person sessions') }}
@endslot
@slot('menu1')
{{ __('In-Person Session') }}
@endslot
@slot('menu3')
{{ __('Add') }}
@endslot
@slot('button')
<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{ route("offline.sessions.index") }}" class="btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __("Back")}}</a>

  </div>
</div>
@endslot
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
          <h5 class="card-title">{{ __('Create new In-person session') }}</h5>
        </div>
        <div class="card-body">

          <form autocomplete="off" action="{{ route('offline.sessions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

              <div class="form-group col-md-6">
                <label> {{ __('In-Person Session') }} {{ __('adminstaticword.Name') }}: <sup class="redstar">*</sup></label>
                <input value="{{ old('title') }}" type="text" name="title" class="form-control" required placeholder="{{ __('Enter In-person session name') }}">
              </div>
            
              <div class="form-group col-md-6">
                <label for="exampleInputDetails">{{ __('adminstaticword.LinkByCourse') }}:</label><br>
                <input type="checkbox" id="myCheck" name="link_by" class="custom_toggle">
              </div>
              <div class="col-md-6 course-enable" style="display: none">
                <div class="form-group">
                  <label>{{ __('adminstaticword.Courses') }}:<span class="redstar">*</span></label>
                  <select name="course_id" id="course_id" class="select2 form-control">
                    @foreach($course as $cor)
                      <option value="{{$cor->id}}">{{$cor->title}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="indv-bbl form-group col-md-6">
                <label>{{ __('adminstaticword.Category') }}:<span class="redstar">*</span></label>
                <select name="main_category" id="category_id" class="form-control select2">
                  <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                  @foreach($category as $cate)
                  <option value="{{$cate->id}}">{{$cate->title}}</option>
                  @endforeach
                </select>
              </div>

              <div class="indv-bbl form-group col-md-6">
                  <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                  <select name="scnd_category_id" id="type_id" class="form-control select2">
                  </select>
              </div>

              <div class="indv-bbl form-group col-md-6">
                <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                <select name="sub_category" id="upload_id" class="form-control select2">
                </select>
              </div>

              <div class="indv-bbl form-group col-md-6">
                <label>{{ __('adminstaticword.ChildCategories') }}:<span class="redstar">*</span></label>
                <select name="ch_sub_category[]" id="grand" class="form-control select2" multiple="multiple"></select>
              </div>

              @if(Auth::User()->role == "admin")
                <div class="form-group col-md-6">
                  <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                      class="redstar">*</span></label>
                  <select name="instructor_id" required class="form-control js-example-basic-single">
                    <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->fname}} {{$user->lname}}</option>
                    @endforeach
                  </select>
                </div>
              @endif

              <div class="form-group col-md-6">
                <label for="image">{{ __('adminstaticword.Image') }}: <sup class="redstar">*</sup> {{ __('size: 270x200') }}</label><br>
                <div class="input-group mb-3">
                      <div class="input-group-prepend">
                          <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                      </div>
                      <div class="custom-file">
                          <input type="file" class="custom-file-input" id="inputGroupFile01" name="image">
                          <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                        </div>
                  </div>
              </div>

              <div class="form-group col-md-6" id="sec4_four">
                <label>
                  {{ __('Presentation Start Time') }}:<sup class="redstar">*</sup>
                </label>

                <div class="input-group">
                  <input name="start_time" type="text" id="datetimepicker1" class="form-control"
                    placeholder="yyyy-mm-dd hh:ii aa" aria-describedby="basic-addon5" required/>
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon5"><i class="feather icon-calendar"></i></span>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-6" id="sec4_four">
                <label>
                  {{ __('Session Expire Date') }}:<sup class="redstar">*</sup>
                </label>

                <div class="input-group">
                  <input name="expire_date" type="text" class="form-control default-datepicker"
                    placeholder="yyyy-mm-dd" aria-describedby="basic-addon5" required/>
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon5"><i class="feather icon-calendar"></i></span>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('In-Person Session') }} {{ __('adminstaticword.Duration') }}: <sup class="redstar">*</sup><i class="feather icon-help-circle text-secondary"></i><small class="text-muted"> {{ __('It will be count in minutes.') }}</small></label>
                <input value="{{ old('duration') }}" type="number" name="duration" min="0" class="form-control" required placeholder="{{ __('Enter In-person session duration eg. 40') }}">
                
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Location') }}: <sup class="redstar">*</sup></label>
                <input value="{{ old('location') }}" type="text" name="location" class="form-control" required placeholder="{{ __('Enter location here') }}">
                
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Google Map Link') }}: <sup class="redstar">*</sup></label>
                <input value="{{ old('google_map_link') }}" type="text" name="google_map_link" class="form-control" required placeholder="{{ __('Paste google map link here') }}">
                
              </div>

              <div class="form-group col-md-6">
                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}:<sup class="text-danger">*</sup></label>
                <input type="number" step="0.001" class="form-control" name="price" min="0" id="priceMain" required oninput="javascript:offerPrice.value = this.value;"
                  placeholder="{{ __('adminstaticword.Enter') }} {{ __('price') }}"
                  value="{{ (old('price')?? 0) }}">
              </div>

              <div class="form-group col-md-6">
                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="text-danger">*</sup> <small class="text-muted">Discounted price Zero(0) consider as free</small> </label>
                <input type="number" step="0.001" class="form-control" name="discount_price" min="0" id="offerPrice" required
                  placeholder="{{ __('adminstaticword.Enter') }} {{ __('discount price') }}"
                  value="{{ (old('discount_price')?? 0) }}">
              </div>

              <div class="form-group col-md-12">
                <label>
                  {{ __('In-Person Session Detail') }}:<sup class="redstar">*</sup>
                </label>
                <textarea id="detail" name="detail" rows="3" class="form-control"></textarea>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Set Max Participants') }}: <sup class="redstar">*</sup><i class="feather icon-help-circle text-secondary"></i><small class="text-muted"> {{ __('It will be inclusive of admin or instructor.') }}</small></label>
                <input value="{{ old('setMaxParticipants') }}" type="number" min="0" class="form-control" name="setMaxParticipants" placeholder="{{ __('Enter maximum participant no.') }}" required/>

              </div>

                <input type='hidden'  class="custom_toggle" value="1" name="allow_record" />
            </div>
            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i> {{ __('Reset') }}</button>
            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
              {{ __('Create') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@php
  $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',now(Auth::user()->timezone));
  $currentDate= $date->format('Y-m-d');
@endphp
@endsection


@section('script')
<script>
  (function($) {
    "use strict";
    $(function() {
      $('#timepickerwithdate22').datepicker({
        language: 'en',
        timeFormat: 'hh:ii aa',
        timepicker: true,
        dateFormat: 'yyyy-mm-dd',
        minDate: new Date("{{ $currentDate }}"),

      });
      $('#myCheck').change(function() {
        if ($('#myCheck').is(':checked')) {
          $('.course-enable').show('fast');
          $('.indv-bbl').hide('fast');

          $('#category_id').val('').trigger('change');
          $('#upload_id').val('').trigger('change');
          $('#type_id').val('').trigger('change');
          $('#grand').val('').trigger('change');
        } else {
          $('#course_id').val('').trigger('change');
          $('.course-enable').hide('fast');
          $('.indv-bbl').show('fast');
        }
      });
    });

    $(function() {

      $('#link_by').change(function() {
        if ($('#link_by').is(':checked')) {
          $('#sec1_one').show('fast');
        } else {
          $('#sec1_one').hide('fast');
        }

      });
    });

    $(function() {
      var urlLike = '{{ url('type/categories') }}';
      $('#category_id').change(function() {
      var up = $('#type_id').empty();
      var cat_id = $(this).val();
      if (cat_id){
      $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:"GET",
      url: urlLike,
      data: {catId: cat_id},
      success:function(data){
      console.log(data);
      up.append("<option value=''>{{ __('Please Choose') }}</option>");
      $.each(data, function(id, title) {
      up.append($('<option>', {value:id, text:title}));
      });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      }
      });
      }
      });
    });

    $(function() {
      var urlLike = '{{ url('admin/dropdown') }}';
      $('#type_id').change(function() {
      var up = $('#upload_id').empty();
      var cat_id = $('#category_id').val();
      var type_id = $(this).val();
      if (type_id){
      $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:"GET",
      url: urlLike,
      data: {
              catId: cat_id,
              typeId: type_id
            },
      success:function(data){
      console.log(data);
      up.append("<option value=''>{{ __('Please Choose') }}</option>");
      $.each(data, function(id, title) {
      up.append($('<option>', {value:id, text:title}));
      });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      }
      });
      }
      });
    });

    $(function() {
      var urlLike = '{{ url('admin/gcat') }}';
      $('#upload_id').change(function() {
      var up = $('#grand').empty();
      var cat_id = $('#category_id').val();
      var type_id = $('#type_id').val();
      var sub_id = $(this).val();
      if (sub_id){
      $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:"GET",
      url: urlLike,
      data: {
              catId: cat_id,
              typeId: type_id,
              subId: sub_id,
            },
      success:function(data){
      console.log(data);
      up.select2({
          placeholder: "{{ __('Please Choose') }}",
          allowClear: true
      });
      $.each(data, function(id, title) {
      up.append($('<option>', {value:id, text:title}));
      });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      }
      });
      }
      });
    });
    
  })(jQuery);
</script>
@endsection