@extends('admin.layouts.master')
@section('title',__('Edit Course'))
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Course') }} : <span >[{{ $cor->title }}]</span>

@endslot

@slot('menu1')
{{ __('Course') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
    <div class="widgetbar">
        <a href="{{route('course.index')}}" class="float-right btn btn-primary-rgba mr-2"><i
                class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a> </div>
</div>
@endslot

@endcomponent
<div class="contentbar">                
  <!-- Start row -->
  <div class="row">
      <!-- Start col -->
      <div class="col-lg-5 col-xl-3">
          <div class="card m-b-30">
              <div class="card-header">
                <div class="row">
                  <div class="col-md-6">
                    <h5 class="card-box">{{__('Course')}}</h5>
                  </div>
                  <!-- language start -->
                  @php
                  $languages = App\Language::all(); 
                  @endphp
                  <div class="col-md-6">
                    <li class="list-inline-item pull-right">
                      <div class="languagebar">
                          <div class="dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="languagelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="live-icon">{{Session::has('changed_language') ? Session::get('changed_language') : ''}}</span></a>
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
              <div class="card-body">
                    @php
                      $involvement = App\Involvement::firstWhere(['user_id' => Auth::user()->id,'course_id' => $cor->id]);
                    @endphp
          
                    @if(isset($involvement))
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                      <a class="nav-link mb-2 active" id="v-pills-CourseChapter-tab" data-toggle="pill" href="#v-pills-CourseChapter"
                        role="tab" aria-controls="v-pills-CourseChapter" aria-selected="true"><i
                          class="feather icon-grid mr-2"></i>{{ __('adminstaticword.CourseChapter') }}</a>
                     {{-- <a class="nav-link mb-2" id="v-pills-CourseClass-tab" data-toggle="pill" href="#v-pills-CourseClass" role="tab"
                        aria-controls="v-pills-CourseClass" aria-selected="false"><i
                          class="feather icon-package mr-2"></i>{{ __('adminstaticword.CourseClass')  }}</a> --}}
                    </div>
                    @else
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                      
                      <a class="nav-link mb-2 show active" data-toggle="pill" href="#v-pills-courseedit" role="tab" aria-selected="true"><i class="feather icon-grid mr-2"></i>{{ __('adminstaticword.Course') }}</a>

                      <a class="nav-link mb-2" id="v-pills-introduction-tab" data-toggle="pill" href="#v-pills-introduction" role="tab" aria-controls="v-pills-introduction" aria-selected="false"><i class="feather icon-video mr-2"></i>{{ __('adminstaticword.CourseIntroduction') }}</a>

                      {{-- <a class="nav-link mb-2" id="v-pills-order-tab" data-toggle="pill" href="#v-pills-order" role="tab" aria-controls="v-pills-order" aria-selected="false"><i class="feather icon-book mr-2"></i>{{ __('adminstaticword.CourseInclude') }}</a> --}}
                      
                      @canany(['what-learn.edit', 'what-learn.view'])
                        <a class="nav-link mb-2" id="v-pills-addresses-tab" data-toggle="pill" href="#v-pills-addresses" role="tab" aria-controls="v-pills-addresses" aria-selected="false"><i class="feather icon-map-pin mr-2"></i>{{ __('adminstaticword.WhatLearns') }}</a>
                      @endcan

                      @canany(['course-chapter.edit', 'course-chapter.view'])
                        <a class="nav-link mb-2" id="v-pills-wishlist-tab" data-toggle="pill" href="#v-pills-wishlist" role="tab" aria-controls="v-pills-wishlist" aria-selected="false"><i class="feather icon-book-open mr-2"></i>{{ __('adminstaticword.CourseChapter') }}</a>
                      @endcan

                      {{-- <a class="nav-link mb-2" id="v-pills-wallet-tab" data-toggle="pill" href="#v-pills-wallet" role="tab" aria-controls="v-pills-wallet" aria-selected="true"><i class="feather icon-credit-card mr-2"></i>{{ __('adminstaticword.CourseClass') }}</a> --}}

                      {{-- <a class="nav-link mb-2" id="v-pills-chat-tab" data-toggle="pill" href="#v-pills-chat" role="tab" aria-controls="v-pills-chat" aria-selected="false"><i class="feather icon-message-circle mr-2"></i>{{ __('adminstaticword.RelatedCourse') }}</a> --}}
                      @can(['question.create', 'question.edit', 'question.view', 'question.delete'])
                      <a class="nav-link mb-2" id="v-pills-notifications-tab" data-toggle="pill" href="#v-pills-notifications" role="tab" aria-controls="v-pills-notifications" aria-selected="false"><i class="feather icon-bell mr-2"></i>{{ __("adminstaticword.Question Answer") }}</a>
                      @endcan

                      @can('review-rating.manage')
                        <a class="nav-link mb-2" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false"><i class="feather icon-user mr-2"></i>{{ __('adminstaticword.ReviewRating') }}</a>
                      @endcan

                      {{-- <a class="nav-link  mb-2" id="v-pills-logout-tab" data-toggle="pill" href="#v-pills-logout" role="tab" aria-controls="v-pills-logout" aria-selected="false"><i class="feather icon-speaker mr-2"></i>{{ __('adminstaticword.Announcement') }}</a> --}}

                      {{-- <a class="nav-link  mb-2" id="v-pills-ReviewReport-tab" data-toggle="pill" href="#v-pills-ReviewReport" role="tab" aria-controls="v-pills-ReviewReport" aria-selected="false"><i class="feather icon-file-text mr-2"></i>{{ __('adminstaticword.ReviewReport') }}</a> --}}

                      @canany(['quiz-topic.edit', 'quiz-topic.view'])
                        <a class="nav-link  mb-2" id="v-pills-QuizTopic-tab" data-toggle="pill" href="#v-pills-QuizTopic" role="tab" aria-controls="v-pills-QuizTopic" aria-selected="false"><i class="feather icon-log-out mr-2"></i>{{ __('adminstaticword.QuizTopic') }}</a>
                      @endcan
                      @can('review-rating.manage')

                      <a class="nav-link  mb-2" id="v-pills-Questionnaire-tab" data-toggle="pill" href="#v-pills-Questionnaire" role="tab" aria-controls="v-pills-Questionnaire" aria-selected="false"><i class="feather icon-help-circle mr-2"></i>{{ __('adminstaticword.Questionnaire') }}</a>
                      @endcan

                      {{-- @if($gsetting->appointment_enable == 1)
                      <a class="nav-link" id="v-pills-Appointment-tab3" data-toggle="pill" href="#v-pills-Appointment" role="tab"
                        aria-controls="v-pills-Appointment" aria-selected="false"><i
                          class="feather icon-plus mr-2"></i>{{ __('adminstaticword.Appointment') }}</a>
                      @endif
                      
                      <a class="nav-link" id="v-pills-PreviousPaper-tab4" data-toggle="pill" href="#v-pills-PreviousPaper" role="tab"
                        aria-controls="v-pills-PreviousPaper" aria-selected="false"><i
                        class="feather icon-file mr-2"></i>{{ __('adminstaticword.PreviousPaper') }}</a> --}}
                        
                    </div>
                    @endif
              </div>
          </div>
      </div>
      <!-- End col -->
      <!-- Start col -->
      <div class="col-lg-7 col-xl-9">
        <div class="tab-content" id="v-pills-tabContent">
          @if(isset($involvement))
            <div class="tab-pane fade show active" id="v-pills-CourseChapter">
              @include('admin.course.coursechapter.index')
            </div>
            <div class="tab-pane fade" id="v-pills-CourseClass" role="tabpanel" aria-labelledby="v-pills-CourseClass-tab">
              {{-- @include('admin.course.courseclass.index') --}}
            </div>
          @else
          <div class="tab-pane fade show active" id="v-pills-courseedit" role="tabpanel">
            @canany(['courses.view','courses.edit'])
              @include('admin.course.editcor')
            {{-- @elseif(auth()->user()->role == 'instructor')
              @include('admin.course.view') --}}
            @endcan
          </div>
        
          <div class="tab-pane fade" id="v-pills-introduction" role="tabpanel" aria-labelledby="v-pills-introduction-tab">
            @include('admin.course.introduction.add')
          </div>

          {{-- <div class="tab-pane fade" id="v-pills-order" role="tabpanel" aria-labelledby="v-pills-order-tab">
            @include('admin.course.courseinclude.index')
          </div> --}}

          <!-- My Addresses Start -->
          <div class="tab-pane fade" id="v-pills-addresses" role="tabpanel" aria-labelledby="v-pills-addresses-tab">
            @include('admin.course.whatlearns.index')
          </div>
          <!-- My Addresses End -->

          <!-- My Wishlist Start -->
          <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel" aria-labelledby="v-pills-wishlist-tab">
            @include('admin.course.coursechapter.index')
          </div>
          <!-- My Wishlist End -->
      
          <!-- My Wallet Start -->
          {{-- <div class="tab-pane fade" id="v-pills-wallet" role="tabpanel" aria-labelledby="v-pills-wallet-tab">
            @include('admin.course.courseclass.index')
          </div> --}}
          <!-- My Wallet End -->

          <!-- My Chat Start -->
          {{-- <div class="tab-pane fade" id="v-pills-chat" role="tabpanel" aria-labelledby="v-pills-chat-tab">
            @include('admin.course.relatedcourse.index')
          </div> --}}
          <!-- My Chat End -->

          <!-- My Notifications Start -->
          <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel" aria-labelledby="v-pills-notifications-tab">
            @include('admin.course.questionanswer.index')
          </div>
          <!-- My Notifications End -->

          <!-- My Profile Start -->
          <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            @include('admin.course.reviewrating.index')
          </div>
          
          {{-- <div class="tab-pane fade" id="v-pills-logout" role="tabpanel" aria-labelledby="v-pills-logout-tab">
            @include('admin.course.announsment.index')
          </div> --}}

          <div class="tab-pane fade" id="v-pills-ReviewReport" role="tabpanel" aria-labelledby="v-pills-ReviewReport-tab">
            @include('admin.course.reviewreport.index')
          </div>
          
          <div class="tab-pane fade" id="v-pills-QuizTopic" role="tabpanel" aria-labelledby="v-pills-QuizTopic-tab">
            @include('admin.course.quiztopic.index')
          </div>
          <div class="tab-pane fade" id="v-pills-Questionnaire" role="tabpanel" aria-labelledby="v-pills-QuizTopic-tab">
            @include('admin.course.questionnaire.index')
          </div>
          

          {{-- <div class="tab-pane fade" id="v-pills-Appointment" role="tabpanel" aria-labelledby="v-pills-Appointment-tab3">
            @include('admin.course.appointment.index')
          </div> --}}
  
          {{-- <div class="tab-pane fade" id="v-pills-PreviousPaper" role="tabpanel" aria-labelledby="v-pills-PreviousPaper-tab4">
            @include('admin.course.previous_paper.index')
          </div> --}}
          @endif                         
        </div>                        
      </div>
      <!-- End col -->
  </div>
  <!-- End row -->                  
</div>
@endsection

@push('script')
<!-- import bunnycdn js file here -->
<script src="{{ url('js/bunnycdn.js') }}"></script>

@if ($gsetting->vimeo_enable == 1)
  <script src="{{ url('js/vimeo.js') }}"></script>
@endif
@endpush