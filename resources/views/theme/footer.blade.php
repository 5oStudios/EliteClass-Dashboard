<footer id="footer" class="footer-main-block">
    <div class="container" style="display:none;">
        <div class="footer-block">
            <div class="row">
                @php
                    $widgets = App\WidgetSetting::first();
                @endphp
                <div class="col-lg-4 col-md-4 col-12">
                   
                    <div class="footer-logo">
                        @if($gsetting->logo_type == 'L')
                            @if($gsetting->footer_logo != NULL)
                            <a href="{{ url('/') }}" title="logo"><img src="{{ asset('images/logo/'.$gsetting->footer_logo) }}" alt="logo" class="img-fluid" ></a>
                            @endif
                        @else()
                            <a href="{{ url('/') }}"><b>{{ $gsetting->project_title }}</b></a>
                        @endif
                    </div>

                    

                    <div class="mobile-btn">
                        @if($gsetting->play_download == '1')
                            <a href="{{ $gsetting->play_link }}" title=""><img src="{{ url('images/icons/download-google-play.png') }}" alt="logo"></a>
                        @endif
                        @if($gsetting->app_download == '1')
                            <a href="{{ $gsetting->app_link }}" title=""><img src="{{ url('images/icons/app-download-ios.png') }}" alt="logo"></a>
                        @endif
                    </div>


                </div>
                @if(isset($widgets) && $widgets->widget_enable == 1)

                <div class="col-lg-2 col-md-2 col-4">
                    <div class="widget"><b>{{ $widgets->widget_one }}</b></div>
                    <div class="footer-link">
                        <ul>
                            <!--@if($gsetting->instructor_enable == 1)-->
                            <!--    @if(Auth::check())-->
                            <!--        @if(Auth::User()->role == "user")-->
                            <!--        <li><a href="#" data-toggle="modal" data-target="#myModalinstructor" title="{{ __('Become An Instructor')}}">{{ __('Become An Instructor') }}</a></li>-->
                            <!--        @endif-->
                            <!--    @else-->
                            <!--        <li><a href="{{ route('login') }}" title="{{ __('Become An Instructor') }}">{{ __('Become An Instructor') }}</a></li>-->
                            <!--    @endif-->
                            <!--@endif-->

                            @if(isset($widgets) && $widgets->about_enable == 1)
                            <li><a href="{{ route('about.show') }}" title="{{ __('About us') }}">{{ __('About us') }}</a></li>
                            @endif
                            
                            @if(isset($widgets) && $widgets->contact_enable == 1)
                            <li><a href="{{url('user_contact')}}" title="{{ __('Contact us') }}">{{ __('Contact us') }}</a></li>
                            @endif
                            
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-4">
                    <div class="widget"><b>{{ $widgets->widget_two }}</b></div>
                    <div class="footer-link">
                        <ul>
                            <!-- @if(isset($widgets) && $widgets->career_enable == 1)
                            <li><a href="{{ route('careers.show') }}" title="{{ __('Careers') }}">{{ __('Careers') }}</a></li>
                            @endif

                            @if(isset($widgets) && $widgets->blog_enable == 1)
                            <li><a href="{{ route('blog.all') }}" title="{{ __('Blog') }}">{{ __('Blog') }}</a></li>
                            @endif -->

                            @if(isset($widgets) && $widgets->help_enable == 1)
                            <li><a href="{{ route('help.show') }}" title="{{ __('Help&Support') }}">{{ __('Help&Support') }}</a></li>
                            @endif
                            
                        </ul>
                    </div>
                </div>
                <!-- <div class="col-lg-2 col-md-2 col-4">
                    <div class="widget"><b>{{ $widgets->widget_three }}</b></div>
                    <div class="footer-link">
                        <ul>
                            
                            @php
                                $pages = App\Page::get();
                            @endphp
                            
                            @if(isset($pages))
                            @foreach($pages as $page)
                                @if($page->status == 1)
                                <li><a href="{{ route('page.show', $page->slug) }}" title="{{ $page->title }}">{{ $page->title }}</a></li>
                                @endif
                            @endforeach
                            @endif
                            
                        </ul>
                    </div>
                </div> -->

                @endif

                <div class="col-lg-2 col-md-2">

                    @php
                        $languages = App\Language::get(); 
                    @endphp
                    @if(isset($languages) && count($languages) > 0)
                    <div class="footer-dropdown">
                        <a href="#" class="a" data-toggle="dropdown"><i data-feather="globe"></i>{{Session::has('changed_language') ? ucfirst(Session::get('changed_language')) : ''}}<i class="fa fa-angle-up lft-10"></i></a>
                        
                       
                        <ul class="dropdown-menu">
                          
                            @foreach($languages as $language)
                            <a href="{{ route('languageSwitch', $language->local) }}"><li>{{$language->name}}</li></a>
                            @endforeach
                        </ul>
                    </div>
                    @endif


                    @php
                        $currencies = DB::table('currencies')->get(); 
                    @endphp
                    @if(isset($currencies) && count($currencies) > 0)
                    <div class="footer-dropdown footer-dropdown-two">
                        <a href="#" class="a" data-toggle="dropdown"><i data-feather="credit-card"></i> {{Session::has('changed_currency') ? ucfirst(Session::get('changed_currency')) : $currency->code}}<i class="fa fa-angle-up lft-10"></i></a>
                        
                       
                        <ul class="dropdown-menu">
                          
                            @foreach($currencies as $currency)
                            <a href="{{ route('CurrencySwitch', $currency->code) }}"><li>{{$currency->code}}</li></a>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
                
                
            </div>
        </div>
    </div>
    <hr>
    <div class="tiny-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo-footer">
                        <ul>

                            <li>{{ $gsetting->cpy_txt }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="copyright-social">
                        <ul>
                           
                            <li>
                                @if(isset($terms->terms) && $terms->terms != NULL && $terms->terms != '')
                                <a href="{{url('terms_condition')}}" title="{{ __('Terms & Condition') }}">{{ __('Terms & Condition') }}</a>
                                @endif
                            </li> 
                            <li>
                                @if(isset($terms->policy) && $terms->policy != NULL && $terms->policy != '')
                                <a href="{{url('privacy_policy')}}" title="{{ __('Privacy Policy') }}">{{ __('Privacy Policy') }}</a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>


@include('instructormodel')


