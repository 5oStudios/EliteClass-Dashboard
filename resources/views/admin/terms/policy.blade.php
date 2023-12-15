@extends('admin.layouts.master')
@section('title', 'Privacy Policy - Admin')
@section('maincontent')
@component('components.breadcumb',['fourthactive' => 'active'])
@slot('heading')
   {{ __('Privacy Policy') }}
@endslot
@slot('menu1')
{{ __('Privacy Policy') }}
@endslot
@endcomponent
<div class="contentbar">
    <div class="row">

  
    <!-- row started -->
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
                <!-- Card header will display you the heading -->
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-box">{{ __('adminstaticword.PrivacyPolicy') }}</h5>
                        </div>
                        <!-- language start -->
                        @php
                        $languages = App\Language::all(); 
                        @endphp
                        <div class="col-md-6">
                            <li class="list-inline-item pull-right">
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
                        </div>
                        <!-- language end -->
                    </div>
                </div> 
               
                <!-- card body started -->
                <div class="card-body">
               <!-- form start -->
               <form action="{{ action('TermsController@update') }}" class="form" method="POST" novalidate enctype="multipart/form-data">
                      {{ csrf_field() }}
                      {{ method_field('PUT') }}
                        <!-- row start -->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- card start -->
                                <div class="card">
                                    <!-- card body start -->
                                    <div class="card-body">
                                        <!-- row start -->
                                          <div class="row">
                                              
                                              <div class="col-md-12">
                                                  <!-- row start -->
                                                  <div class="row">
                                                   
                                                    <!-- Description -->
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="text-dark">{{ __('adminstaticword.PrivacyPolicy') }} : <span class="text-danger">*</span></label>
                                                            <textarea id="detail" name="policy" class="@error('policy') is-invalid @enderror" required="">{{ optional($items)->policy }}</textarea>
                                                            @error('policy')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                                  
                                                    <!-- create and close button -->
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <button type="reset" class="btn btn-danger-rgba mr-1"><i class="fa fa-ban"></i> {{ __("Reset")}}</button>
                                                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                                            {{ __("Update")}}</button>
                                                        </div>
                                                    </div>

                                                  </div><!-- row end -->
                                              </div><!-- col end -->
                                          </div><!-- row end -->

                                    </div><!-- card body end -->
                                </div><!-- card end -->
                            </div><!-- col end -->
                        </div><!-- row end -->
                  </form>
                  <!-- form end -->
                </div><!-- card body end -->
            
        </div><!-- col end -->
    </div>
</div>
</div><!-- row end -->
    <br><br>
@endsection
<!-- main content section ended -->
<!-- This section will contain javacsript start -->
@section('script')
@endsection
<!-- This section will contain javacsript end -->