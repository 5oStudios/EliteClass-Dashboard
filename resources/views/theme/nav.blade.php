@if($gsetting->promo_enable == 1)
<div id="promo-outer">
    <div id="promo-inner">
        <a href="{{ $gsetting['promo_link'] }}">{{ $gsetting['promo_text'] }}</a>
        <span id="close">x</span>
    </div>
</div>
<div id="promo-tab" class="display-none">SHOW</div>
@endif

<section id="nav-bar" class="nav-bar-main-block" data-toggle="sticky-onscroll">
    <div class="container">
        <!-- start navigation -->
        <div class="navigation fullscreen-search-block">
            <span style="font-size:30px;cursor:pointer" onclick="openNav()" class="hamburger">&#9776; </span>
            <div class="logo">
                  @if($gsetting->logo_type == 'L')
                    <a href="{{ url('/') }}" ><img src="{{ asset('images/logo/'.$gsetting->logo) }}" class="img-fluid" alt="logo"></a>
                @else()
                    <a href="{{ url('/') }}"><b><div class="logotext">{{ $gsetting->project_title }}</div></b></a>
                @endif
            </div>
            <div class="nav-search nav-wishlist">
                
                <a href="#find"><i data-feather="search"></i></a>
            </div>
            <div class="shopping-cart">
                <a href="{{ route('cart.show') }}" title="Cart"><i data-feather="shopping-cart"></i></a>
                <span class="red-menu-badge red-bg-success">
                    @php
                        $item = session()->get('cart.add_to_cart');
                        
                        if(isset($item) && count($item)>0){

                            echo count(array_unique($item));
                        }
                        else{

                            echo "0";
                        }
                    @endphp
                </span>
            </div>
            @auth
            <div class="shopping-cart">
                <a href="{{ route('cart.show') }}" title="Cart"><i data-feather="shopping-cart"></i></a>
                <span class="red-menu-badge red-bg-success">
                    @php
                        $item = App\Cart::where('user_id', Auth::User()->id)->count();
                        if($item>0){

                            echo $item;
                        }
                        else{

                            echo "0";
                        }
                    @endphp
                </span>
            </div>
            <div class="nav-wishlist">
                <div id="notification_li">
                    <a href="{{ url('send') }}" id="notificationLinkk" title="Notification"><i data-feather="bell"></i></a>
                    <span class="red-menu-badge red-bg-success">
                        {{ Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count() }}
                    </span>
                    <div id="notificationContainerr">
                    <div id="notificationTitle">{{ __('frontstaticword.Notifications') }}</div>
                    <div id="notificationsBody" class="notifications">
                        <ul>
                            @foreach(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                <li class="unread-notification">
                                    <a href="{{url('notifications/'.$notification->id)}}">          
                                    <div class="notification-image">
                                        @if($notification->data['image'] !== NULL )
                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="course" class="img-fluid" >
                                        @else
                                            <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="course" class="img-fluid">
                                        @endif
                                    </div>
                                    <div class="notification-data">
                                        In {{ str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                        <br>
                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                    </div>
                                    </a>
                                </li>
                            @endforeach

                            @foreach(Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                <li>
                                    <a href="{{ route('mycourse.show') }}">
                                    <div class="notification-image">
                                        @if($notification->data['image'] !== NULL )
                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="course" class="img-fluid" >
                                        @else
                                           <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="course" class="img-fluid">
                                        @endif
                                    </div>
                                    <div class="notification-data">
                                        In {{  str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                        <br>
                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                    </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="notificationFooter"><a href="{{route('deleteNotification')}}">{{ __('frontstaticword.ClearAll') }}</a></div>
                    </div>
                </div>
            </div>
            @endauth
            

            <div id="mySidenav" class="sidenav">
              <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                @guest
                <div class="login-block">
                    <a href="{{ route('register') }}" class="btn btn-primary" title="register">{{ __('frontstaticword.Signup') }}</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary" title="login">{{ __('frontstaticword.Login') }}</a>
                </div>
                @endguest
                @auth

                <div id="notificationTitle">
                     @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                      <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="dropdown-user-circle" alt="">
                    @else
                        <img src="{{ asset('images/default/user.jpg')}}" class="dropdown-user-circle" alt="">
                    @endif
                    <div class="user-detailss">
                        Hi, {{ Auth::User()->fname }}
                        
                    </div>
                    
                </div>

                <div class="login-block">

                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div id="notificationFooter">
                            {{ __('frontstaticword.Logout') }}
                            
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="display-none">
                                @csrf
                            </form>
                        </div>
                    </a>
                </div>

                @endauth

                @php
                    $categories = App\Categories::orderBy('position','ASC')->with(['subcategory','subcategory.childcategory'])->get();
                @endphp
                
                <div class="wrapper center-block">
                    
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    @foreach($categories->where('status', '1') as $cate)
                      <div class="panel panel-default">
                        <div class="panel-heading active" role="tab" id="headingOne">
                            <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{ $cate->id }}" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fa {{ $cate->icon }} rgt-10"></i> <label class="prime-cat" data-url="{{ route('category.page',['id' => $cate->id, 'category' => str_slug(str_replace('-','&',$cate->slug))]) }}">{{ str_limit($cate->title, $limit = 20, $end = '..') }}</label> 
                            </a>
                            </h4>
                        </div>

                        
                        <div id="collapseOne{{ $cate->id }}" class="subcate-collapse panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        @foreach($cate->subcategory as $sub)
                          @if($sub->status ==1)
                          <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingeleven">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseeleven{{ $sub->id }}" aria-expanded="false" aria-controls="collapseeleven">
                                      <i class="fa {{ $sub->icon }} rgt-10"></i> <label class="sub-cate" data-url="{{ route('subcategory.page',['id' => $sub->id, 'category' => str_slug(str_replace('-','&',$sub->slug))]) }}">{{ str_limit($sub->title, $limit = 15, $end = '..') }}</label>

                                    </a>
                                  </h4>
                                </div>

                                <div id="collapseeleven{{ $sub->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingeleven">
                                  @foreach($sub->childcategory as $child)
                                  @if($child->status ==1)
                                  <div class="panel-body sub-cat">
                                    <i class="fa {{ $child->icon }} rgt-10"></i> <label class="child-cate" data-url="{{ route('childcategory.page',['id' => $child->id, 'category' => str_slug(str_replace('-','&',$child->slug))]) }}">{{ $child->title }} </label>
                                  </div>
                                  @endif
                                  @endforeach
                                </div>
                                
                            </div>
                          </div>
                          @endif
                        @endforeach
                        </div>
                        
                      </div>
                    @endforeach
                  </div>
                      
                </div>
              
                @auth


                <div class="sidebar-nav-icon">
                    <ul>
                        @if(Auth::User()->role == "admin" )
                        
                        <a target="_blank" href="{{ url('/admins') }}"><li><i data-feather="pie-chart"></i>{{ __('frontstaticword.AdminDashboard') }}</li></a>
                       
                        @endif
                        @if(Auth::User()->role == "instructor")

                        <a target="_blank" href="{{ url('/instructor') }}"><li><i data-feather="pie-chart"></i>{{ __('frontstaticword.InstructorDashboard') }}</li></a>
                        @endif

                      
                        <a href="{{ route('mycourse.show') }}"><li><i data-feather="book-open"></i>{{ __('frontstaticword.MyCourses') }}</li></a>
                        <a href="{{ route('wishlist.show') }}"><li><i data-feather="heart"></i>{{ __('frontstaticword.MyWishlist') }}</li></a>
                        <a href="{{ route('purchase.show') }}"><li><i data-feather="shopping-cart"></i>{{ __('frontstaticword.PurchaseHistory') }}</li></a>
                        <a href="{{route('profile.show',Auth::User()->id)}}"><li><i data-feather="user"></i>{{ __('frontstaticword.UserProfile') }}</li></a>
                        <!-- @if(Auth::User()->role == "user")
                        @if($gsetting->instructor_enable == 1)
                        <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="Become An Instructor"><li><i data-feather="shield"></i>{{ __('frontstaticword.BecomeAnInstructor') }}</li></a>

                        @endif
                
                        @endif -->

                        <!-- <a href="{{ route('flash.deals') }}"><li><i data-feather="battery-charging"></i>{{ __('Flash Deals') }}</li></a> -->


                        <!-- @if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)

                        @if(Auth::User()->role == "instructor")
                        <a href="{{ route('plan.page') }}"><li><i data-feather="tag"></i>{{ __('frontstaticword.InstructorPlan') }}</li></a>
                        @endif
                        @endif -->


                        @if(Auth::User()->role == "user" || Auth::User()->role == "instructor")
                        @if($gsetting->device_control == 1)
                        <a href="{{ route('active.courses') }}" title="Watchlist"><li><i data-feather="framer"></i>{{ __('frontstaticword.Watchlist') }}</li></a>
                        @endif
                        @endif


                        <!-- @if($gsetting->donation_enable == 1)
                        <a target="__blank" href="{{ $gsetting->donation_link }}" title="Donation"><li><i data-feather="framer"></i>{{ __('frontstaticword.Donation') }}</li></a>
                        @endif -->

                      

                        @if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings'))

                        @php
                            $affiliate = App\Affiliate::first();
                            $wallet_settings = App\WalletSettings::first();
                        @endphp
                        

                        @if(isset($wallet_settings) && $wallet_settings->status == 1)
                        <a href="{{ url('/wallet') }}"><li><i class="icon-wallet icons"></i>{{ __('frontstaticword.MyWallet') }}</li></a>
                        @endif

                        @if(isset($affiliate) && $affiliate->status == 1)
                        <a href="{{ route('get.affiliate') }}"><li><i data-feather="users"></i>{{ __('frontstaticword.Affiliate') }}</li></a>
                        @endif

                        @endif
                        
                        <a href="{{ route('my.leaderboard') }}"><li><i class="icon-chart icons"></i>{{ __('frontstaticword.MyLeaderboard') }}</li></a>

                       
                    </ul>
                </div>
                
               
                @endauth
            </div>
        </div>
        
        <!-- end navigation -->
        <div class="row smallscreen-search-block">
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="logo">
                            

                            @if($gsetting->logo_type == 'L')
                                <a href="{{ url('/') }}" ><img src="{{ asset('images/logo/'.$gsetting->logo) }}" class="img-fluid" alt="logo"></a>
                            @else()
                                <a href="{{ url('/') }}"><b><div class="logotext">{{ $gsetting->project_title }}</div></b></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <div class="navigation">
                            <div id="cssmenu">
                                <ul>
                                    <li><a href="#" title="Categories"><i data-feather="grid"></i>{{ __('frontstaticword.Categories') }}</a>
                                       
                                        <ul>
                                            @foreach($categories as $cate)
                                            @if($cate->status == 1 )
                                            <li><a href="{{ route('category.page',['id' => $cate->id, 'category' => $cate->title]) }}" title="{{ $cate->title }}"><i class="fa {{ $cate->icon }} rgt-20"></i>{{ str_limit($cate->title, $limit = 25, $end = '..') }} <i data-feather="chevron-right" class="float-right"></i></a>
                                            <ul>   
                                                @foreach($cate->subcategory as $sub)
                                                @if($sub->status ==1)
                                                <li><a href="{{ route('subcategory.page',['id' => $sub->id, 'category' => $sub->title]) }}" title="{{ $sub->title }}"><i class="fa {{ $sub->icon }} rgt-20"></i>{{ str_limit($sub->title, $limit = 25, $end = '..') }}
                                                    <i data-feather="chevron-right" class="float-right"></i></a>
                                                    <ul>
                                                        @foreach($sub->childcategory as $child)
                                                        @if($child->status ==1)
                                                        <li>
                                                            <a href="{{ route('childcategory.page',['id' => $child->id, 'category' => $child->title]) }}" title="{{ $child->title }}"><i class="fa {{ $child->icon }} rgt-20"></i>{{ str_limit($child->title, $limit = 25, $end = '..') }}</a>
                                                        </li>
                                                        @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                                @endif
                                               @endforeach
                                            </ul>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                @guest
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="learning-business">
                            
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="shopping-cart">
                            <a href="{{ route('cart.show') }}" title="Cart"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $item = session()->get('cart.add_to_cart');
                                    
                                    if(isset($item) && count($item)>0){

                                        echo count(array_unique($item));
                                    }
                                    else{

                                        echo "0";
                                    }
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="{{ route('search') }}">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="Search in Site" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="Login-btn">
                            <a href="{{ route('login') }}" class="btn btn-secondary" title="login">{{ __('frontstaticword.Login') }}</a>
                            <a href="{{ route('register') }}" class="btn btn-primary" title="register">{{ __('frontstaticword.Signup') }}</a>
                            
                        </div> 
                    </div>
                @endguest

                @auth
                <div class="row">
                    
                    
                    <div class="col-lg-3 col-md-3 col-6">
                        <div class="learning-business learning-business-two">
                           
                        </div>
                    </div>

                  

                    <div class="col-lg-2 col-md-2 col-6">
                        <div class="learning-business">
                           
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <ul id="nav">
                                <li id="notification_li">
                                    <a href="{{ url('send') }}" id="notificationLink" title="Notification"><i data-feather="bell"></i></a>
                                    <span class="red-menu-badge red-bg-success">
                                        {{ Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count() }}
                                    </span>
                                    <div id="notificationContainer">
                                    <div id="notificationTitle">{{ __('frontstaticword.Notifications') }}</div>
                                    <div id="notificationsBody" class="notifications">
                                        <ul>
                                            @foreach(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                                <li class="unread-notification">
                                                    <a href="{{url('notifications/'.$notification->id)}}">          
                                                    <div class="notification-image">
                                                        @if($notification->data['image'] !== NULL )
                                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="course" class="img-fluid" >
                                                        @else
                                                            <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="course" class="img-fluid">
                                                        @endif
                                                    </div>
                                                    <div class="notification-data">
                                                        In {{ str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                                        <br>
                                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                                    </div>
                                                    </a>
                                                </li>
                                            @endforeach

                                            @foreach(Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                                <li>
                                                    <a href="{{ route('mycourse.show') }}">
                                                    <div class="notification-image">
                                                        @if($notification->data['image'] !== NULL )
                                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="course" class="img-fluid" >
                                                        @else
                                                           <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="course" class="img-fluid">
                                                        @endif
                                                    </div>
                                                    <div class="notification-data">
                                                        In {{  str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                                        <br>
                                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                                    </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="notificationFooter"><a href="{{route('deleteNotification')}}">{{ __('frontstaticword.ClearAll') }}</a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <a href="{{ route('wishlist.show') }}" title="Go to Wishlist"><i data-feather="heart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $wishlist = App\Wishlist::where('user_id', Auth::User()->id)->get();
                                    
                                @endphp

                                

                                @php
                                    $counter = 0;
                                    foreach ($wishlist as $item) {
                                         if($item->courses->status == '1'){

                                              
                                          $counter++;
       
                                         }
                                    }

                                    echo  $counter; 
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="shopping-cart">
                            <a href="{{ route('cart.show') }}" title="Cart"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $item = App\Cart::where('user_id', Auth::User()->id)->count();
                                    if($item>0){

                                        echo $item;
                                    }
                                    else{

                                        echo "0";
                                    }
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="{{ route('search') }}">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="Search in Site" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>
                       
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                        <div class="my-container">
                          <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle  my-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                 @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                  <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="circle" alt="">
                                @else
                                    <img src="{{ asset('images/default/user.jpg')}}"  class="circle" alt="">
                                @endif
                                <span class="dropdown__item name" id="name">{{ str_limit(Auth::User()->fname, $limit = 10, $end = '..') }}</span>
                                <span class="dropdown__item caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right User-Dropdown U-open" aria-labelledby="dropdownMenu1">
                                <div id="notificationTitle">
                                     @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                      <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="dropdown-user-circle" alt="">
                                    @else
                                        <img src="{{ asset('images/default/user.jpg')}}" class="dropdown-user-circle" alt="">
                                    @endif
                                    <div class="user-detailss">
                                        {{ Auth::User()->fname }}
                                        <br>
                                        {{ Auth::User()->email }}
                                    </div>
                                    
                                </div>

                                <div class="scroll-down">

                                @if(Auth::User()->role == "admin" )
                               
                                <a target="_blank" href="{{ url('/admins') }}"><li><i data-feather="pie-chart"></i>{{ __('frontstaticword.AdminDashboard') }}</li></a>
                               
                                @endif
                                @if(Auth::User()->role == "instructor")

                                <a target="_blank" href="{{ url('/instructor') }}"><li><i data-feather="pie-chart"></i>{{ __('frontstaticword.InstructorDashboard') }}</li></a>
                                @endif

                              
                        
                                <a href="{{ route('mycourse.show') }}"><li><i data-feather="book-open"></i>{{ __('frontstaticword.MyCourses') }}</li></a>
                                <a href="{{ route('wishlist.show') }}"><li><i data-feather="heart"></i>{{ __('frontstaticword.MyWishlist') }}</li></a>
                                <a href="{{ route('purchase.show') }}"><li><i data-feather="shopping-cart"></i>{{ __('frontstaticword.PurchaseHistory') }}</li></a>
                                <a href="{{route('profile.show',Auth::User()->id)}}"><li><i data-feather="user"></i>{{ __('frontstaticword.UserProfile') }}</li></a>
                                <!-- @if(Auth::User()->role == "user")
                                @if($gsetting->instructor_enable == 1)
                                <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="Become An Instructor"><li><i data-feather="shield"></i>{{ __('frontstaticword.BecomeAnInstructor') }}</li></a>

                                @endif
                        
                                @endif -->

                                <!-- <a href="{{ route('flash.deals') }}"><li><i data-feather="battery-charging"></i>{{ __('Flash Deals') }}</li></a> -->


                                <!-- @if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)

                                @if(Auth::User()->role == "instructor")
                                <a href="{{ route('plan.page') }}"><li><i data-feather="tag"></i>{{ __('frontstaticword.InstructorPlan') }}</li></a>
                                @endif
                                @endif -->


                                @if(Auth::User()->role == "user" || Auth::User()->role == "instructor")
                                @if($gsetting->device_control == 1)
                                <a href="{{ route('active.courses') }}" title="Watchlist"><li><i data-feather="framer"></i>{{ __('frontstaticword.Watchlist') }}</li></a>
                                @endif
                                @endif


                                <!-- @if($gsetting->donation_enable == 1)
                                <a target="__blank" href="{{ $gsetting->donation_link }}" title="Donation"><li><i data-feather="framer"></i>{{ __('frontstaticword.Donation') }}</li></a>
                                @endif -->

                                @if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings'))

                                

                                @if(isset($wallet_settings) && $wallet_settings->status == 1)
                                <a href="{{ url('/wallet') }}"><li><i class="icon-wallet icons"></i>{{ __('frontstaticword.MyWallet') }}</li></a>
                                @endif

                                @if(isset($affiliate) && $affiliate->status == 1)
                                <a href="{{ route('get.affiliate') }}"><li><i data-feather="users"></i>{{ __('frontstaticword.Affiliate') }}</li></a>
                                @endif

                                @endif

                                <a href="{{ route('my.leaderboard') }}"><li><i class="icon-chart icons"></i>{{ __('frontstaticword.MyLeaderboard') }}</li></a>

                                </div>
                                

                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <div id="notificationFooter">
                                        {{ __('frontstaticword.Logout') }}
                                        
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="display-none">
                                            @csrf
                                        </form>
                                    </div>
                                </a>
                            </ul>
                          </div>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </div>
        
    </div>
</section>

<!-- start search -->
<div id="find" class="small-screen-navigation">
    <button type="button" class="close">×</button>
     <form action="{{ route('search') }}" class="form-inline search-form" method="GET">
         <input type="find" name="searchTerm" class="form-control" id="search"  placeholder="{{ __('frontstaticword.Searchforcourses') }}" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
         <button type="submit" class="btn btn-outline-info btn_sm">Search</button> 
     </form>
</div>
<!-- start end -->


<!-- side navigation  -->
<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>


@include('instructormodel')