@extends('admin.layouts.master')
@section('title', 'Edit In-person session - Admin')
@section('maincontent')

@component('components.breadcumb',['fourthactive' => 'active'])

@slot('heading')
{{ __('In-Person sessions') }}
@endslot
@slot('menu1')
{{ __('In-Person Session') }}
@endslot
@slot('menu3')
{{ __('Edit') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{ route('offline.sessions.index') }}" class="btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __("Back")}}</a>
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
          <div class="row pull-right">
              <!-- language start -->
              @php
              $languages = App\Language::all(); 
              @endphp
                <li class="list-inline-item">
                    <div class="languagebar">
                        <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="languagelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="live-icon"> {{__('Selected Language')}} ({{Session::has('changed_language') ? Session::get('changed_language') : ''}})</span></a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languagelink">
                                @if (isset($languages) && count($languages) > 0)
                                @foreach ($languages as $language)
                                <a class="dropdown-item" href="{{ route('languageSwitch', $language->local) }}">
                                    <i class="feather icon-globe"></i>
                                    {{$language->name}} ({{$language->local}})</a>
                                @endforeach
                                @endif
                            
                            </div>
                        </div>
                    </div>                                   
                </li>
              <!-- language end -->
          </div>
        </div>
        <div class="card-body">

          <form autocomplete="off" action="{{ route('offline.sessions.update',$session->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              
              <div class="form-group col-md-6">
                <label> {{ __('In-Person Session') }} {{ __('adminstaticword.Name') }}: <sup class="redstar">*</sup></label>
                <input value="{{ $session->title }}" type="text" name="title" class="form-control" required placeholder="{{ __('Enter live streaming name') }}">
              </div>

              @if(Auth::User()->role == "admin")
              <div class="form-group col-md-6">
                <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                    class="redstar">*</span>
                </label>
                <select name="instructor_id" required class="form-control">
                  <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                  @foreach($users as $user)
                  <option {{ $session->instructor_id == $user->id ? 'selected' : "" }} value="{{ $user->id }}">{{$user->fname}} {{$user->lname}}</option>
                  @endforeach
                </select>
              </div>
              @endif

              <div class="form-group col-md-6">
                <label for="exampleInputDetails">{{ __('adminstaticword.LinkByCourse') }}:</label><br>
                <input type="checkbox" id="myCheck" name="link_by" {{ $session->link_by == 'course' ? 'checked' : '' }} class="custom_toggle">
              </div>
              <div class="form-group col-md-6" style="{{ $session['link_by'] == 'course' ? '' : 'display:none' }}" id="update-password">
                <label>{{ __('adminstaticword.Courses') }}:<sup class="text-danger">*</sup></label>
                <select name="course_id" id="course_id" class="form-control select2">
                  @foreach($course as $caat)
                    <option {{ optional($session)['course_id'] == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                  @endforeach 
                </select>
              </div>

              <div class="form-group col-md-6 indv-bbl" style="{{ $session->link_by == 'course' ? 'display:none': '' }}">
                <label>{{ __('adminstaticword.Category') }}<span class="redstar">*</span></label>
                <select name="main_category" id="category_id" class="form-control js-example-basic-single">
                    <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                    @foreach($category as $caat)
                    <option {{ $session->main_category == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                    @endforeach 
                </select>
              </div>

              <div class="form-group col-md-6 indv-bbl" style="{{ $session->link_by == 'course' ? 'display:none': '' }}">
                <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                <select name="scnd_category_id" id="type_id" class="form-control js-example-basic-single">
                  <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                  @foreach($typecategory as $caat)
                  <option {{ $session->scnd_category_id == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-6 indv-bbl" style="{{ $session->link_by == 'course' ? 'display:none': '' }}">
                  
                <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                <select name="sub_category" id="upload_id" class="form-control select2">
                  <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                  @foreach($subcategory as $caat)
                    <option {{ $session->sub_category == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                  @endforeach
                </select>
              </div>  

              <div class="form-group col-md-6 indv-bbl" style="{{ $session->link_by == 'course' ? 'display:none': '' }}">
                <label>{{ __('adminstaticword.ChildCategories') }}:<span class="redstar">*</span></label>
                <select name="ch_sub_category[]" id="grand" class="form-control select2" multiple="multiple">
                  <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                  @foreach($childcategory as $caat)
                    @if(is_array($session['ch_sub_category']) || is_object($session['ch_sub_category']))

                      <option value="{{ $caat->id }}" {{ in_array($caat->id, $session['ch_sub_category'] ?: []) ? 'selected': '' }}> {{ $caat->title }}
                      </option>

                    @endif
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Location') }}: <sup class="redstar">*</sup></label>
                <input value="{{ $session->location }}" type="text" name="location" class="form-control" required placeholder="{{ __('Enter location here') }}">
                
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Google Map Link') }}: <sup class="redstar">*</sup></label>
                <input value="{{ $session->google_map_link }}" type="text" name="google_map_link" class="form-control" required placeholder="{{ __('Paste google map link here') }}">
                
              </div>

              <div class="form-group col-md-6">
                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}:<sup class="text-danger">*</sup></label>
                <input type="number" step="0.001" class="form-control" name="price" min="0" id="priceMain"
                       placeholder="{{ __('adminstaticword.Enter') }} {{ __('price') }}" required
                  value="{{ $session->price?? 0 }}">
              </div>

              <div class="form-group col-md-6">
                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="text-danger">*</sup> </label> <small class="text-muted">Discounted price Zero(0) consider as free</small>
                <input type="number" step="0.001" class="form-control" name="discount_price" min="0" id="offerPrice"
                       placeholder="{{ __('adminstaticword.Enter') }} {{ __('discount price') }}" required
                  value="{{ $session->discount_price?? 0 }}">
              </div>
            
              <div class="form-group col-md-12">
                <label>
                  {{ __('In-Person Session Detail') }}:<sup class="redstar">*</sup>
                </label>
                <textarea id="detail" name="detail" rows="3" class="form-control">{{$session->detail}}</textarea>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('In-Person Session') }} {{ __('adminstaticword.Duration') }}: <sup class="redstar">*</sup><i class="feather icon-help-circle text-secondary"></i><small class="text-muted"> {{ __('It will be count in minutes.') }}</small></label>
                <input value="{{ $session->duration }}" type="number" name="duration" min="0" class="form-control" required placeholder="{{ __('Enter live streaming duration eg. 40') }}">
              </div>

              <div class="form-group col-md-6">
                <label>
                  {{ __('Presentation Start Time') }}:<sup class="redstar">*</sup>
                </label>
                <!-- getUserTimeZoneDateTime() is a helper function defined in App/Helpers/helper.php -->
                <div class="input-group">
                  <input value="{{ date('Y-m-d h:i a', strtotime(getUserTimeZoneDateTime($session->start_time))) }}" name="start_time" type="text" id="datetimepicker2" class="form-control"
                    placeholder="yyyy-mm-dd hh:ii a" aria-describedby="basic-addon5" required/>
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
                  <input value="{{ $session->expire_date }}" name="expire_date" type="text" class="form-control datepicker"
                    placeholder="yyyy-mm-dd" aria-describedby="basic-addon5" required/>
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon5"><i class="feather icon-calendar"></i></span>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('Set Max Participants') }}: <sup class="redstar">*</sup><i class="feather icon-help-circle text-secondary"></i><small class="text-muted"> {{ __('It will be inclusive of admin or instructor.') }}</small></label>
                <input value="{{ $session->setMaxParticipants }}" type="number" min="0" class="form-control" name="setMaxParticipants" placeholder="{{ __('Enter maximum participant no., leave blank if want unlimited participant') }}" required/>
              </div>

              <div class="form-group col-md-6">
                <label>{{ __('adminstaticword.Image') }}:<sup class="redstar">*</sup> {{ __('size: 270x200') }}</label> 
                <!-- ====================== -->
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputGroupFile01" name="image" value="{{ $session->image }}">
                        <label class="custom-file-label" for="inputGroupFile01">{{ $session->image?? __('Choose file') }}</label>
                    </div>
                </div>
                @if($session['image'] !== NULL && $session['image'] !== '')
                <img src="{{ url('/images/offlinesession/'.$session->image) }}" height="70px;" width="70px;"/>
                @endif
                <!-- ====================== -->
              </div>

            </div>

            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
            {{ __('Reset') }}</button>
            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
            {{ __('Update') }}</button>
            
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
 (function($) {
    "use strict";
    $(function(){
        $('#myCheck').change(function(){
          if($('#myCheck').is(':checked')){
            $('#update-password').show('fast');
            $('.indv-bbl').hide('fast');

            $('#category_id').trigger('change');
            $('#upload_id').trigger('change');
            $('#type_id').trigger('change');
            $('#grand').trigger('change');
          }else{
            $('#course_id').trigger('change');
            $('#update-password').hide('fast');
            $('.indv-bbl').show('fast');
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
                            subId: sub_id
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