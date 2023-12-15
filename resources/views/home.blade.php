@extends('theme.master')
@section('title', 'Online Courses')
@section('content')

@include('admin.message')
@include('sweetalert::alert')

@section('meta_tags')

<meta name="title" content="{{ $gsetting['project_title'] }}">
<meta name="description" content="{{ $gsetting['meta_data_desc'] }} ">
<meta property="og:title" content="{{ $gsetting['project_title'] }} ">
<meta property="og:url" content="{{ url()->full() }}">
<meta property="og:description" content="{{ $gsetting['meta_data_desc'] }}">
<meta property="og:image" content="{{ asset('images/logo/'.$gsetting['logo']) }}">
<meta itemprop="image" content="{{ asset('images/logo/'.$gsetting['logo']) }}">
<meta property="og:type" content="website">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ asset('images/logo/'.$gsetting['logo']) }}">
<meta property="twitter:title" content="{{ $gsetting['project_title'] }} ">
<meta property="twitter:description" content="{{ $gsetting['meta_data_desc'] }}">
<meta name="twitter:site" content="{{ url()->full() }}" />

<link rel="canonical" href="{{ url()->full() }}" />
<meta name="robots" content="all">
<meta name="keywords" content="{{ $gsetting->meta_data_keyword }}">

@endsection

<!-- categories-tab start-->
@if($gsetting->category_enable == 1)
<section id="categories-tab" class="categories-tab-main-block">
  <div class="container">
    <div id="categories-tab-slider" class="categories-tab-block owl-carousel">

      @foreach($category as $cat)
      @if($cat->status == 1)
      <div class="item categories-tab-dtl">
        <a href="{{ route('category.page',['id' => $cat->id, 'category' => str_slug(str_replace('-','&',$cat->slug))]) }}"
          title="{{ $cat->title }}"><i class="fa {{ $cat->icon }}"></i>{{ $cat->title }}</a>
      </div>
      @endif
      @endforeach
    </div>
  </div>
</section>
@endif
<!-- categories-tab end-->

@if(isset($sliders))
<section id="home-background-slider" class="background-slider-block owl-carousel">
  <div class="lazy item home-slider-img">
    @foreach($sliders as $slider)
    @if($slider->status == 1)
    <div id="home" class="home-main-block"
      style="background-image: url('{{ asset('images/slider/'.$slider['image']) }}')">
      <div class="container">
        <div class="row">
          <div
            class="col-lg-12 {{$slider['left'] == 1 ? 'col-md-offset-6 col-sm-offset-6 col-sm-6 col-md-6 text-right' : ''}}">
            <div class="home-dtl">
              <div class="home-heading">{{ $slider['heading'] }}</div>
              <p class="btm-10">{{ $slider['sub_heading'] }}</p>
              <p class="btm-20">{{ $slider['detail'] }}
            </div>

            @if($slider->search_enable == 1)
            <div class="home-search">
              <form method="GET" id="searchform" action="{{ route('search') }}">
                <div class="search">

                  <input type="text" name="searchTerm" class="searchTerm"
                    placeholder="{{ __('What do you want to learn?')}}">
                  <button type="submit" class="searchButton">{{ __('Search')}}
                  </button>

                </div>
              </form>
            </div>
            @endif

          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  @endforeach
  </div>
</section>
@endif
<!-- home end -->
<!-- learning-work start -->

@if(isset($facts))
<section id="learning-work" class="learning-work-main-block">
  <div class="container">
    <div class="row">
      @foreach($facts as $fact)
      <div class="col-lg-4 col-md-4">
        <div class="learning-work-block">
          <div class="row">
            <div class="col-lg-3 col-md-3">
              <div class="learning-work-icon">
                <i class="fa {{ $fact['icon'] }}"></i>
              </div>
            </div>
            <div class="col-lg-9 col-md-9">
              <div class="learning-work-dtl">
                <div class="work-heading">{{ $fact['heading'] }}</div>
                <p>{{ $fact['sub_heading'] }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- learning-work end -->
<!-- fact start -->

@if($hsetting->fact_enable == 1 && isset($factsetting))
<section id="facts" class="fact-main-block">
  <div class="container">
    <div class="row">
      @foreach($factsetting as $factset)
      <div class="col-lg-3 col-md-6 col-12">
        <div class="facts-block text-center">
          <div class="facts-block-one">
            <div class="facts-block-img">
              @if($factset['image'] !== NULL && $factset['image'] !== '')
              <img src="{{ url('/images/facts/'.$factset->image) }}" class="img-fluid" alt="" />
              @else
              <img src="{{ Avatar::create($factset->title)->toBase64() }}" alt="course" class="img-fluid">
              @endif
              <div class="facts-count">{{ $factset->number }}</div>
            </div>
            <h5 class="facts-title"><a href="#" title="">{{ $factset->title }}</a></h5>
            <p>{{ $factset->description }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif
<!-- fact end -->
<!-- Advertisement -->
@if(isset($advs))
@foreach($advs as $adv)
@if($adv->position == 'belowslider')
<br>
<section id="student" class="student-main-block top-40">
  <div class="container">
    <a href="{{ $adv->url1 }}" title="{{ __('Click to visit') }}">

    </a>
  </div>
</section>
@endif
@endforeach
@endif
@if($hsetting->discount_enable == 1 && isset($discountcourse) && count($discountcourse) >0)
<section id="student" class="student-main-block top-40">
  <div class="container">
    <h4 class="student-heading">{{ __('Top Discounted Courses') }}</h4>
    <div id="discounted-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($discountcourse as $discount)
      @if($discount->status == 1 && $discount->featured == 1)
      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block{{$discount->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($discount['preview_image'] !== NULL && $discount['preview_image'] !== '')
              <a href="{{ route('user.course.show',['id' => $discount->id, 'slug' => $discount->slug ]) }}"><img
                  data-src="{{ asset('images/course/'.$discount['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('user.course.show',['id' => $discount->id, 'slug' => $discount->slug ]) }}"><img
                  data-src="{{ Avatar::create($discount->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="advance-badge">
              @if($discount['level_tags'] == !NULL)
              <span class="badge bg-primary">{{ $discount['level_tags'] }}</span>
              @endif
            </div>
            <div class="view-user-img">

              @if(optional($discount->user)['user_img'] !== NULL && optional($discount->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$discount->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>

            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('user.course.show',['id' => $discount->id, 'slug' => $discount->slug ]) }}">{{ str_limit($discount->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($discount->user)['fname'] }}</span></h6>
              </div>
              <div class="rating">
                <ul>
                  <li>
                    <?php 
                                            $learn = 0;
                                            $price = 0;
                                            $value = 0;
                                            $sub_total = 0;
                                            $sub_total = 0;
                                            $reviews = App\ReviewRating::where('course_id',$discount->id)->get();
                                            ?>
                    @if(!empty($reviews[0]))
                    <?php
                                            $count =  App\ReviewRating::where('course_id',$discount->id)->count();

                                            foreach($reviews as $review){
                                                $learn = $review->price*5;
                                                $price = $review->price*5;
                                                $value = $review->value*5;
                                                $sub_total = $sub_total + $learn + $price + $value;
                                            }

                                            $count = ($count*3) * 5;
                                            $rat = $sub_total/$count;
                                            $ratings_var = ($rat*100)/5;
                                            ?>

                    <div class="pull-left">
                      <div class="star-ratings-sprite"><span style="width:<?php echo $ratings_var; ?>%"
                          class="star-ratings-sprite-rating"></span>
                      </div>
                    </div>


                    @else
                    <div class="pull-left">{{ __('No Rating') }}</div>
                    @endif
                  </li>
                  <!-- overall rating-->
                  <?php 
                                        $learn = 0;
                                        $price = 0;
                                        $value = 0;
                                        $sub_total = 0;
                                        $count =  count($reviews);
                                        $onlyrev = array();

                                        $reviewcount = App\ReviewRating::where('course_id', $discount->id)->WhereNotNull('review')->get();

                                        foreach($reviews as $review){

                                            $learn = $review->learn*5;
                                            $price = $review->price*5;
                                            $value = $review->value*5;
                                            $sub_total = $sub_total + $learn + $price + $value;
                                        }

                                        $count = ($count*3) * 5;
                                         
                                        if($count != 0)
                                        {
                                            $rat = $sub_total/$count;
                                     
                                            $ratings_var = ($rat*100)/5;
                                   
                                            $overallrating = ($ratings_var/2)/10;
                                        }
                                         
                                        ?>

                  @php
                  $reviewsrating = App\ReviewRating::where('course_id', $discount->id)->first();
                  @endphp
                  @if(!empty($reviewsrating))
                  <!-- <li>
                                            <b>{{ round($overallrating, 1) }}</b>
                                        </li> -->
                  @endif
                  <li class="reviews">
                    (@php
                    $data = App\ReviewRating::where('course_id', $discount->id)->count();
                    if($data>0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp Reviews)
                  </li>

                </ul>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        @php
                        $data = App\Order::where('course_id', $discount->id)->count();
                        if(($data)>0){

                        echo $data;
                        }
                        else{

                        echo "0";
                        }
                        @endphp</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if( $discount->type == 1)
                    <div class="rate text-right">
                      <ul>

                        @if($discount->discount_price == !NULL)

                        <li><a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($discount['discount_price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}
                              {{ activeCurrency()->getData()->position == 'r' ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($discount['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>


                        @else

                        @if($c->price == !NULL)
                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($discount['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>
                        @endif

                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>



              <div class="img-wishlist">
                <div class="protip-wishlist">
                  <ul>

                    <li class="protip-wish-btn"><a
                        href="https://calendar.google.com/calendar/r/eventedit?text={{ $discount['title'] }}"
                        target="__blank" title="reminder"><i data-feather="bell"></i></a></li>

                    @if(Auth::check())

                    <li class="protip-wish-btn"><a class="compare" data-id="{{filter_var($discount->id)}}"
                        title="compare"><i data-feather="bar-chart"></i></a></li>

                    @php
                    $wish = App\Wishlist::where('user_id', Auth::User()->id)->where('course_id',
                    $discount->id)->first();
                    @endphp
                    @if ($wish == NULL)
                    <li class="protip-wish-btn">
                      <form id="demo-form2" method="post" action="{{ url('show/wishlist', $discount->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$discount->id}}" />

                        <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @else
                    <li class="protip-wish-btn-two">
                      <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $discount->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$discount->id}}" />

                        <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @endif
                    @else
                    <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                          data-feather="heart"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>


            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block{{$discount->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $discount['title'] }}</h5>
              <div class="main-des">
                <p>Last Updated: {{ date('jS F Y', strtotime($discount->updated_at)) }}</p>
              </div>

              <ul class="description-list">
                <li>
                  <i data-feather="play-circle"></i>
                  <div class="class-des">
                    {{ __('Classes') }}:
                    @php
                    $data = App\CourseClass::where('course_id', $discount->id)->count();
                    if($data > 0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp
                  </div>
                </li>
                &nbsp;
                <li>
                  <div>
                    <div class="time-des">
                      <span class="">
                        <i data-feather="clock"></i>
                        @php

                        $classtwo = App\CourseClass::where('course_id',
                        $discount->id)->sum("duration");

                        @endphp
                        {{ $classtwo }} {{ __('Minutes')}}
                      </span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="lang-des">
                    @if($discount['language_id'] == !NULL)
                    @if(isset($c->language))
                    <i data-feather="globe"></i> {{ $discount->language['name'] }}
                    @endif
                    @endif
                  </div>
                </li>

              </ul>

              <div class="product-main-des">
                <p>{{ $discount->short_detail }}</p>
              </div>
              <div>
                @if($discount->whatlearns->isNotEmpty())
                @foreach($discount->whatlearns as $wl)
                @if($wl->status ==1)
                <div class="product-learn-dtl">
                  <ul>
                    <li><i data-feather="check-circle"></i>{{ str_limit($wl['detail'], $limit = 120, $end = '...') }}
                    </li>
                  </ul>
                </div>
                @endif
                @endforeach
                @endif
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-8">
                    @if($discount->type == 1)
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $discount->id, 'slug' => $discount->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $order = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $discount->id)->first();
                    @endphp
                    @if(!empty($order) && $order->status == 1)
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $discount->id, 'slug' => $discount->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $cart = App\Cart::where('user_id', Auth::User()->id)->where('course_id',
                    $discount->id)->first();
                    @endphp
                    @if(!empty($cart))
                    <div class="protip-btn">
                      <form id="demo-form2" method="post" action="{{ route('remove.item.cart',$cart->id) }}">
                        {{ csrf_field() }}

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Remove From Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @else
                    <div class="protip-btn">
                      <form id="demo-form2" method="post"
                        action="{{ route('addtocart',['course_id' => $discount->id, 'price' => $discount->price, 'discount_price' => $discount->discount_price ]) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="category_id" value="{{$discount->category['id'] ?? '-'}}" />

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Add To Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @endif
                    @endif
                    @endif
                    @else
                    @if($gsetting->guest_enable == 1)
                    <form id="demo-form2" method="post" action="{{ route('guest.addtocart', $discount->id) }}"
                      data-parsley-validate class="form-horizontal form-label-left">
                      {{ csrf_field() }}


                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary"><i
                            data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</button>
                      </div>
                    </form>

                    @else

                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary"><i
                          data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $discount->id, 'slug' => $discount->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $enroll = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $discount->id)->first();
                    @endphp
                    @if($enroll == NULL)
                    <div class="protip-btn">
                      <a href="{{url('enroll/show',$c->id)}}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $discount->id, 'slug' => $discount->slug ]) }}"
                        class="btn btn-secondary" title="Cart">{{ __('Go To Course') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @endif
                    @endif
                  </div>
                  <div class="col-lg-4">
                    <div class="img-wishlist">
                      <div class="protip-wishlist">
                        <ul>
                          @if(Auth::check())

                          @php
                          $wish = App\Wishlist::where('user_id',
                          Auth::User()->id)->where('course_id', $discount->id)->first();
                          @endphp
                          @if ($wish == NULL)
                          <li class="protip-wish-btn">
                            <form id="demo-form2" method="post" action="{{ url('show/wishlist', $discount->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$discount->id}}" />

                              <button class="wishlisht-btn" title="{{ __('Add to wishlist')}}" type="submit"><i
                                  data-feather="heart"></i></button>
                            </form>
                          </li>
                          @else
                          <li class="protip-wish-btn-two">
                            <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $discount->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$discount->id}}" />

                              <button class="wishlisht-btn heart-fill" title="{{ __('Remove from Wishlist')}}"
                                type="submit"><i data-feather="heart"></i></button>
                            </form>
                          </li>
                          @endif
                          @else
                          <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                                data-feather="heart"></i></a></li>
                          @endif
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      @endforeach
    </div>

  </div>
</section>
@endif
<!-- Student start -->
@if(Auth::check())
@if($hsetting->recentcourse_enable == 1 && isset($recent_course_id) && isset($recent_course) &&
optional($recent_course)->status == 1)
<section id="student" class="student-main-block top-40">
  <div class="container">

    @if($total_count >= '0')
    <h4 class="student-heading">{{ __('Recently Viewed Courses') }}</h4>
    <div id="recent-courses-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($recent_course_id as $view)
      @php

      $recent_course = App\Course::where('id', $view)->with('user')->first();

      @endphp
      @if(isset($recent_course))

      @if($recent_course->status == 1)
      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image">
          <div class="view-block">
            <div class="view-img">
              @if($recent_course['preview_image'] !== NULL && $recent_course['preview_image'] !== '')
              <a href="{{ route('user.course.show',['id' => $recent_course->id, 'slug' => $recent_course->slug ]) }}"><img
                  data-src="{{ asset('images/course/'.$recent_course['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('user.course.show',['id' => $recent_course->id, 'slug' => $recent_course->slug ]) }}"><img
                  data-src="{{ Avatar::create($recent_course->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="advance-badge">
              @if($recent_course['level_tags'] == !NULL)
              <span class="badge bg-primary">{{ $recent_course['level_tags'] }}</span>
              @endif
            </div>
            <div class="view-user-img">

              @if($recent_course->user['user_img'] !== NULL && $recent_course->user['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$recent_course->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif
            </div>
            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('user.course.show',['id' => $recent_course->id, 'slug' => $recent_course->slug ]) }}">{{ str_limit($recent_course->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($recent_course->user)['fname'] }}</span></h6>
              </div>
              <div class="rating">
                <ul>
                  <li>
                    <?php 
                                            $learn = 0;
                                            $price = 0;
                                            $value = 0;
                                            $sub_total = 0;
                                            $sub_total = 0;
                                            $reviews = App\ReviewRating::where('course_id',$recent_course->id)->get();
                                            ?>
                    @if(!empty($reviews[0]))
                    <?php
                                            $count =  App\ReviewRating::where('course_id',$recent_course->id)->count();

                                            foreach($reviews as $review){
                                                $learn = $review->price*5;
                                                $price = $review->price*5;
                                                $value = $review->value*5;
                                                $sub_total = $sub_total + $learn + $price + $value;
                                            }

                                            $count = ($count*3) * 5;
                                            $rat = $sub_total/$count;
                                            $ratings_var = ($rat*100)/5;
                                            ?>

                    <div class="pull-left">
                      <div class="star-ratings-sprite"><span style="width:<?php echo $ratings_var; ?>%"
                          class="star-ratings-sprite-rating"></span>
                      </div>
                    </div>


                    @else
                    <div class="pull-left">{{ __('No Rating') }}</div>
                    @endif
                  </li>
                  <!-- overall rating-->
                  <?php 
                                        $learn = 0;
                                        $price = 0;
                                        $value = 0;
                                        $sub_total = 0;
                                        $count =  count($reviews);
                                        $onlyrev = array();

                                        $reviewcount = App\ReviewRating::where('course_id', $recent_course->id)->WhereNotNull('review')->get();

                                        foreach($reviews as $review){

                                            $learn = $review->learn*5;
                                            $price = $review->price*5;
                                            $value = $review->value*5;
                                            $sub_total = $sub_total + $learn + $price + $value;
                                        }

                                        $count = ($count*3) * 5;
                                         
                                        if($count != 0)
                                        {
                                            $rat = $sub_total/$count;
                                     
                                            $ratings_var = ($rat*100)/5;
                                   
                                            $overallrating = ($ratings_var/2)/10;
                                        }
                                         
                                        ?>

                  @php
                  $reviewsrating = App\ReviewRating::where('course_id', $recent_course->id)->first();
                  @endphp
                  <!-- @if(!empty($reviewsrating))
                                        <li>
                                            <b>{{ round($overallrating, 1) }}</b>
                                        </li>
                                        @endif -->

                  <li class="reviews">
                    (@php
                    $data = App\ReviewRating::where('course_id', $recent_course->id)->count();
                    if($data>0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp Reviews)
                  </li>
                </ul>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        @php
                        $data = App\Order::where('course_id', $recent_course->id)->count();
                        if(($data)>0){

                        echo $data;
                        }
                        else{

                        echo "0";
                        }
                        @endphp</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if( $recent_course->type == 1)
                    <div class="rate text-right">
                      <ul>

                        @if($recent_course->discount_price == !NULL)

                        <li><a><b>
                              {{ currency($recent_course->discount_price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = true) }}
                            </b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ currency($recent_course->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = true) }}</strike></b></a>
                        </li>


                        @else
                        <li><a><b>
                              {{ currency($recent_course->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = true) }}</b></a>
                        </li>


                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="img-wishlist">
                <div class="protip-wishlist">
                  <ul>
                    @if(Auth::check())
                    @php
                    $wish = App\Wishlist::where('user_id', auth()->user()->id)->where('course_id',
                    $recent_course->id)->first();
                    @endphp
                    @if ($wish == NULL)
                    <li class="protip-wish-btn">
                      <form id="demo-form2" method="post" action="{{ url('show/wishlist', $recent_course->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{auth()->user()->id}}" />
                        <input type="hidden" name="course_id" value="{{$recent_course->id}}" />

                        <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i data-feather="heart"
                            class="rgt-10"></i></button>
                      </form>
                    </li>
                    @else
                    <li class="protip-wish-btn-two">
                      <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $recent_course->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{auth()->user()->id}}" />
                        <input type="hidden" name="course_id" value="{{$recent_course->id}}" />

                        <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                            data-feather="heart" class="rgt-10"></i></button>
                      </form>
                    </li>
                    @endif
                    @else
                    <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i data-feather="heart"
                          class="rgt-10"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      @endif
      @endif
      @endforeach
    </div>
    @endif

  </div>
</section>
@endif
@endif
<!-- Students end -->
<!-- Student start -->
@if(Auth::check())
@php
if(Schema::hasColumn('orders', 'refunded')){
$enroll = App\Order::where('refunded', '0')->where('user_id', auth()->user()->id)->where('status',
'1')->with('courses')->with(['user','courses.user'] )->get();
}
else{
$enroll = NULL;
}
@endphp
@if($hsetting->purchase_enable == 1 && isset($enroll))
<section id="student" class="student-main-block top-40">
  <div class="container">
    @if(count($enroll) > 0)
    <h4 class="student-heading">{{ __('My Purchased Courses') }}</h4>
    <div id="my-courses-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($enroll as $enrol)
      @if(isset($enrol->courses) && $enrol->courses['status'] == 1 )
      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image">
          <div class="view-block">
            <div class="view-img">
              @if($enrol->courses['preview_image'] !== NULL && $enrol->courses['preview_image'] !== '')
              <a href="{{ route('course.content',['id' => $enrol->courses->id, 'slug' => $enrol->courses->slug ]) }}"><img
                  data-src="{{ asset('images/course/'.$enrol->courses['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('course.content',['id' => $enrol->courses->id, 'slug' => $enrol->courses->slug ]) }}"><img
                  data-src="{{ Avatar::create($enrol->courses->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="view-user-img">

              @if($enrol->user['user_img'] !== NULL && $enrol->user['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$enrol->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif
            </div>
            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('course.content',['id' => $enrol->courses->id, 'slug' => $enrol->courses->slug ]) }}">{{ str_limit($enrol->courses->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($enrol->user)['fname'] }}</span></h6>
              </div>
              <div class="rating">
                <ul>
                  <li>
                    <?php 
                                            $learn = 0;
                                            $price = 0;
                                            $value = 0;
                                            $sub_total = 0;
                                            $sub_total = 0;
                                            $reviews = App\ReviewRating::where('course_id',$enrol->courses->id)->get();
                                            ?>
                    @if(!empty($reviews[0]))
                    <?php
                                            $count =  App\ReviewRating::where('course_id',$enrol->courses->id)->count();

                                            foreach($reviews as $review){
                                                $learn = $review->price*5;
                                                $price = $review->price*5;
                                                $value = $review->value*5;
                                                $sub_total = $sub_total + $learn + $price + $value;
                                            }

                                            $count = ($count*3) * 5;
                                            $rat = $sub_total/$count;
                                            $ratings_var = ($rat*100)/5;
                                            ?>

                    <div class="pull-left">
                      <div class="star-ratings-sprite"><span style="width:<?php echo $ratings_var; ?>%"
                          class="star-ratings-sprite-rating"></span>
                      </div>
                    </div>


                    @else
                    <div class="pull-left">{{ __('No Rating') }}</div>
                    @endif
                  </li>
                  <!-- overall rating-->
                  <?php 
                                        $learn = 0;
                                        $price = 0;
                                        $value = 0;
                                        $sub_total = 0;
                                        $count =  count($reviews);
                                        $onlyrev = array();

                                        $reviewcount = App\ReviewRating::where('course_id', $enrol->courses->id)->WhereNotNull('review')->get();

                                        foreach($reviews as $review){

                                            $learn = $review->learn*5;
                                            $price = $review->price*5;
                                            $value = $review->value*5;
                                            $sub_total = $sub_total + $learn + $price + $value;
                                        }

                                        $count = ($count*3) * 5;
                                         
                                        if($count != 0)
                                        {
                                            $rat = $sub_total/$count;
                                     
                                            $ratings_var = ($rat*100)/5;
                                   
                                            $overallrating = ($ratings_var/2)/10;
                                        }
                                         
                                        ?>

                  @php
                  $reviewsrating = App\ReviewRating::where('course_id', $enrol->courses->id)->first();
                  @endphp

                  <li class="reviews">
                    (@php
                    $data = App\ReviewRating::where('course_id', $enrol->courses->id)->count();
                    if($data>0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp Reviews)
                  </li>
                </ul>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        @php
                        $data = App\Order::where('course_id', $enrol->courses->id)->count();
                        if(($data)>0){

                        echo $data;
                        }
                        else{

                        echo "0";
                        }
                        @endphp</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if( $enrol->courses->type == 1)
                    <div class="rate text-right">
                      <ul>


                        @if($enrol->courses->discount_price == !NULL)

                        <li><a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}
                              {{ price_format( currency($enrol->courses->discount_price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}
                              {{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li><a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}
                                {{ price_format( currency($enrol->courses->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}
                                {{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>




                        @else

                        <li><a><b>
                              {{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}
                              {{ price_format( currency($enrol->courses->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}
                              {{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
      @endif
      @endforeach
    </div>
    @endif

  </div>
</section>
@endif
@endif
<!-- Students end -->

<!-- learning-courses start -->
@if($hsetting->recentcourse_enable == 1 && isset($categories))
<section id="learning-courses" class="learning-courses-main-block">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-6">
        <h4 class="student-heading">{{ __('Recent Courses') }}</h4>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-6">
        <div class="btn_more float-right">

        </div>
      </div>
    </div>

    <div class="row">



      <div class="col-lg-12">
        <div class="learning-courses">
          @if(isset($categories))
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach($categories as $cats)

            <li class="btn nav-item"><a class="nav-item nav-link" id="home-tab" data-toggle="tab" href="#content-tabs"
                role="tab" aria-controls="home" onclick="showtab('{{ $cats->id }}')"
                aria-selected="true">{{ $cats['title'] }}</a></li>

            @endforeach
          </ul>
          @endif
        </div>
        <div class="tab-content" id="myTabContent">
          @if(!empty($categories))


          @foreach($categories as $cate)
          <div class="tab-pane fade show active" id="content-tabs" role="tabpanel" aria-labelledby="home-tab">

            <div id="tabShow">

            </div>

          </div>
          @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endif
<!-- learning-courses end -->
<!-- Advertisement -->
@if(isset($advs))
@foreach($advs as $adv)
@if($adv->position == 'belowrecent')
<br>
<section id="student" class="student-main-block btm-40">
  <div class="container">
    <a href="{{ $adv->url1 }}" title="{{ __('Click to visit') }}">
      <img class="lazy img-fluid advertisement-img-one" data-src="{{ url('images/advertisement/'.$adv->image1) }}"
        alt="{{ $adv->image1 }}">
    </a>
  </div>
</section>
@endif

@endforeach

@endif
<!-- Advertisement -->
<!-- Student start -->

@if( ! $cors->isEmpty() && $hsetting->featured_enable)
<section id="student" class="student-main-block">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h4 class="student-heading">{{ __('Featured Courses') }}</h4>
      </div>
    </div>
    <div id="student-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($cors as $c)


      @if($c->status == 1 && $c->featured == 1)
      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block{{$c->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($c['preview_image'] !== NULL && $c['preview_image'] !== '')
              <a href="{{ route('user.course.show',['id' => $c->id, 'slug' => $c->slug ]) }}"><img
                  data-src="{{ asset('images/course/'.$c['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('user.course.show',['id' => $c->id, 'slug' => $c->slug ]) }}"><img
                  data-src="{{ Avatar::create($c->title)->toBase64() }}" alt="course" class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="advance-badge">
              @if($c['level_tags'] == !NULL)
              <span class="badge bg-primary">{{ $c['level_tags'] }}</span>
              @endif
            </div>
            <div class="view-user-img">

              @if(optional($c->user)['user_img'] !== NULL && optional($c->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$c->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>

            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('user.course.show',['id' => $c->id, 'slug' => $c->slug ]) }}">{{ str_limit($c->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($c->user)['fname'] }}</span></h6>
              </div>
              <div class="rating">
                <ul>
                  <li>
                    <?php 
                                            $learn = 0;
                                            $price = 0;
                                            $value = 0;
                                            $sub_total = 0;
                                            $sub_total = 0;
                                            $reviews = App\ReviewRating::where('course_id',$c->id)->get();
                                            ?>
                    @if(!empty($reviews[0]))
                    <?php
                                            $count =  App\ReviewRating::where('course_id',$c->id)->count();

                                            foreach($reviews as $review){
                                                $learn = $review->price*5;
                                                $price = $review->price*5;
                                                $value = $review->value*5;
                                                $sub_total = $sub_total + $learn + $price + $value;
                                            }

                                            $count = ($count*3) * 5;
                                            $rat = $sub_total/$count;
                                            $ratings_var = ($rat*100)/5;
                                            ?>

                    <div class="pull-left">
                      <div class="star-ratings-sprite"><span style="width:<?php echo $ratings_var; ?>%"
                          class="star-ratings-sprite-rating"></span>
                      </div>
                    </div>


                    @else
                    <div class="pull-left">{{ __('No Rating') }}</div>
                    @endif
                  </li>
                  <!-- overall rating-->
                  <?php 
                                        $learn = 0;
                                        $price = 0;
                                        $value = 0;
                                        $sub_total = 0;
                                        $count =  count($reviews);
                                        $onlyrev = array();

                                        $reviewcount = App\ReviewRating::where('course_id', $c->id)->WhereNotNull('review')->get();

                                        foreach($reviews as $review){

                                            $learn = $review->learn*5;
                                            $price = $review->price*5;
                                            $value = $review->value*5;
                                            $sub_total = $sub_total + $learn + $price + $value;
                                        }

                                        $count = ($count*3) * 5;
                                         
                                        if($count != 0)
                                        {
                                            $rat = $sub_total/$count;
                                     
                                            $ratings_var = ($rat*100)/5;
                                   
                                            $overallrating = ($ratings_var/2)/10;
                                        }
                                         
                                        ?>

                  @php
                  $reviewsrating = App\ReviewRating::where('course_id', $c->id)->first();
                  @endphp
                  @if(!empty($reviewsrating))
                  <!-- <li>
                                            <b>{{ round($overallrating, 1) }}</b>
                                        </li> -->
                  @endif
                  <li class="reviews">
                    (@php
                    $data = App\ReviewRating::where('course_id', $c->id)->count();
                    if($data>0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp Reviews)
                  </li>

                </ul>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        @php
                        $data = App\Order::where('course_id', $c->id)->count();
                        if(($data)>0){

                        echo $data;
                        }
                        else{

                        echo "0";
                        }
                        @endphp</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if( $c->type == 1)
                    <div class="rate text-right">
                      <ul>

                        @if($c->discount_price == !NULL)

                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($c['discount_price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($c['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>


                        @else

                        @if($c->price == !NULL)
                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($c['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>
                        @endif

                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>



              <div class="img-wishlist">
                <div class="protip-wishlist">
                  <ul>

                    <li class="protip-wish-btn"><a
                        href="https://calendar.google.com/calendar/r/eventedit?text={{ $c['title'] }}" target="__blank"
                        title="reminder"><i data-feather="bell"></i></a></li>

                    @if(Auth::check())

                    <li class="protip-wish-btn"><a class="compare" data-id="{{filter_var($c->id)}}" title="compare"><i
                          data-feather="bar-chart"></i></a></li>

                    @php
                    $wish = App\Wishlist::where('user_id', Auth::User()->id)->where('course_id',
                    $c->id)->first();
                    @endphp
                    @if ($wish == NULL)
                    <li class="protip-wish-btn">
                      <form id="demo-form2" method="post" action="{{ url('show/wishlist', $c->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$c->id}}" />

                        <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @else
                    <li class="protip-wish-btn-two">
                      <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $c->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$c->id}}" />

                        <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @endif
                    @else
                    <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                          data-feather="heart"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>


            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block{{$c->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $c['title'] }}</h5>
              <div class="main-des">
                <p>Last Updated: {{ date('jS F Y', strtotime($c->updated_at)) }}</p>
              </div>

              <ul class="description-list">
                <li>
                  <i data-feather="play-circle"></i>
                  <div class="class-des">
                    {{ __('Classes') }}:
                    @php
                    $data = App\CourseClass::where('course_id', $c->id)->count();
                    if($data > 0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp
                  </div>
                </li>
                &nbsp;
                <li>
                  <div>
                    <div class="time-des">
                      <span class="">
                        <i data-feather="clock"></i>
                        @php

                        $classtwo = App\CourseClass::where('course_id',
                        $c->id)->sum("duration");

                        @endphp
                        {{ $classtwo }} Minutes
                      </span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="lang-des">
                    @if($c['language_id'] == !NULL)
                    @if(isset($c->language))
                    <i data-feather="globe"></i> {{ $c->language['name'] }}
                    @endif
                    @endif
                  </div>
                </li>

              </ul>

              <div class="product-main-des">
                <p>{{ $c->short_detail }}</p>
              </div>
              <div>
                @if($c->whatlearns->isNotEmpty())
                @foreach($c->whatlearns as $wl)
                @if($wl->status ==1)
                <div class="product-learn-dtl">
                  <ul>
                    <li><i data-feather="check-circle"></i>{{ str_limit($wl['detail'], $limit = 120, $end = '...') }}
                    </li>
                  </ul>
                </div>
                @endif
                @endforeach
                @endif
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-8">
                    @if($c->type == 1)
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $c->id, 'slug' => $c->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $order = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $c->id)->first();
                    @endphp
                    @if(!empty($order) && $order->status == 1)
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $c->id, 'slug' => $c->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $cart = App\Cart::where('user_id', Auth::User()->id)->where('course_id',
                    $c->id)->first();
                    @endphp
                    @if(!empty($cart))
                    <div class="protip-btn">
                      <form id="demo-form2" method="post" action="{{ route('remove.item.cart',$cart->id) }}">
                        {{ csrf_field() }}

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Remove From Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @else
                    <div class="protip-btn">
                      <form id="demo-form2" method="post"
                        action="{{ route('addtocart',['course_id' => $c->id, 'price' => $c->price, 'discount_price' => $c->discount_price ]) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="category_id" value="{{$c->category['id'] ?? '-'}}" />

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary"><i
                              data-feather="shopping-cart"></i>{{ __('Add To Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @endif
                    @endif
                    @endif
                    @else
                    @if($gsetting->guest_enable == 1)
                    <form id="demo-form2" method="post" action="{{ route('guest.addtocart', $c->id) }}"
                      data-parsley-validate class="form-horizontal form-label-left">
                      {{ csrf_field() }}


                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary"><i
                            data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</button>
                      </div>
                    </form>

                    @else

                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary"><i
                          data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $c->id, 'slug' => $c->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $enroll = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $c->id)->first();
                    @endphp
                    @if($enroll == NULL)
                    <div class="protip-btn">
                      <a href="{{url('enroll/show',$c->id)}}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $c->id, 'slug' => $c->slug ]) }}"
                        class="btn btn-secondary" title="Cart">{{ __('Go To Course') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @endif
                    @endif
                  </div>
                  <div class="col-lg-4">
                    <div class="img-wishlist">
                      <div class="protip-wishlist">
                        <ul>
                          @if(Auth::check())

                          @php
                          $wish = App\Wishlist::where('user_id',
                          Auth::User()->id)->where('course_id', $c->id)->first();
                          @endphp
                          @if ($wish == NULL)
                          <li class="protip-wish-btn">
                            <form id="demo-form2" method="post" action="{{ url('show/wishlist', $c->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$c->id}}" />

                              <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                                  data-feather="heart"></i></button>
                            </form>
                          </li>
                          @else
                          <li class="protip-wish-btn-two">
                            <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $c->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$c->id}}" />

                              <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                                  data-feather="heart"></i></button>
                            </form>
                          </li>
                          @endif
                          @else
                          <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                                data-feather="heart"></i></a></li>
                          @endif
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      @endforeach
    </div>

  </div>
</section>
@endif
<!-- Students end -->


<!-- Subscription Bundle start -->
<section id="subscription" class="student-main-block">
  <div class="container">
    @if (isset($subscriptionBundles) && !$subscriptionBundles->isEmpty())
    <h4 class="student-heading">{{ __('Subscription Bundles') }}</h4>
    <div id="subscription-bundle-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach ($subscriptionBundles as $bundle)
      @if ($bundle->status == 1 && $bundle->is_subscription_enabled == 1)

      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-interactive="false"
          data-pt-title="#prime-next-item-description-block-3{{ $bundle->id }}">
          <div class="view-block">
            <div class="view-img">
              @if ($bundle['preview_image'] !== null && $bundle['preview_image'] !== '')
              <a href="{{ route('bundle.detail', $bundle->id) }}"><img
                  data-src="{{ asset('images/bundle/' . $bundle['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('bundle.detail', $bundle->id) }}"><img
                  data-src="{{ Avatar::create($bundle->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="view-user-img">
              <a href="" title=""><img src="http://eclass.test/images/user_img/159116548431.jpg"
                  class="img-fluid user-img-one" alt=""></a>
            </div>
            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('bundle.detail', $bundle->id) }}">{{ str_limit($bundle->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($bundle->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y', strtotime($bundle['created_at'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if ($bundle->type == 1 && $bundle->price != null)
                    <div class="rate text-right">
                      <ul>
                        @if ($bundle->discount_price == !null)

                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($bundle->discount_price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($bundle->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>


                        @else



                        <li><a><b>
                              {{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($bundle->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>
                        @endif
                      </ul>
                    </div>

                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="prime-next-item-description-block-3{{ $bundle->id }}" class="prime-description-block">
        <div class="prime-description-under-block">
          <div class="prime-description-under-block">
            <h5 class="description-heading">{{ $bundle['title'] }}</h5>
            <div class="main-des">
              @if($bundle['short_detail'] != NUll)

              <p>{{ str_limit($bundle['short_detail'], $limit = 200, $end = '...') }}</p>
              @else
              <p>{{ str_limit($bundle['detail'], $limit = 200, $end = '...') }}</p>
              @endif
            </div>
            <div class="des-btn-block">
              <div class="row">
                <div class="col-lg-12">
                  @if ($bundle->type == 1)
                  @if (Auth::check())
                  @if (Auth::User()->role == 'admin')
                  <div class="protip-btn">
                    <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                  </div>
                  @else
                  @php
                  $order = App\Order::where('user_id',
                  Auth::User()->id)->where('bundle_id',
                  $bundle->id)->first();
                  @endphp
                  @if (!empty($order) && $order->status == 1)
                  <div class="protip-btn">
                    <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                  </div>
                  @else
                  @php
                  $cart = App\Cart::where('user_id',
                  Auth::User()->id)->where('bundle_id',
                  $bundle->id)->first();
                  @endphp
                  @if (!empty($cart))
                  <div class="protip-btn">
                    <form id="demo-form2" method="post" action="{{ route('remove.item.cart', $cart->id) }}">
                      {{ csrf_field() }}

                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Remove From Cart') }}</button>
                      </div>
                    </form>
                  </div>
                  @else
                  <div class="protip-btn">
                    <form id="demo-form2" method="post" action="{{ route('bundlecart', $bundle->id) }}"
                      data-parsley-validate class="form-horizontal form-label-left">
                      {{ csrf_field() }}

                      <input type="hidden" name="user_id" value="{{ Auth::User()->id }}" />
                      <input type="hidden" name="bundle_id" value="{{ $bundle->id }}" />

                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Subscribe Now') }}</button>
                      </div>


                    </form>
                  </div>
                  @endif
                  @endif
                  @endif
                  @else
                  <div class="protip-btn">

                    <a href="{{ route('login') }}" class="btn btn-primary"><i class="fa fa-cart-plus"
                        aria-hidden="true"></i>&nbsp;{{ __('Subscribe Now') }}</a>

                  </div>
                  @endif
                  @else
                  @if (Auth::check())
                  @if (Auth::User()->role == 'admin')
                  <div class="protip-btn">
                    <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                  </div>
                  @else
                  @php
                  $enroll = App\Order::where('user_id',
                  Auth::User()->id)->where('course_id', $c->id)->first();
                  @endphp
                  @if ($enroll == null)
                  <div class="protip-btn">
                    <a href="{{ url('enroll/show', $bundle->id) }}" class="btn btn-primary"
                      title="Enroll Now">{{ __('Subscribe Now') }}</a>
                  </div>
                  @else
                  <div class="protip-btn">
                    <a href="" class="btn btn-secondary" title="Cart">{{ __('Purchased') }}</a>
                  </div>
                  @endif
                  @endif
                  @else
                  <div class="protip-btn">
                    <a href="{{ route('login') }}" class="btn btn-primary"
                      title="Enroll Now">{{ __('Subscribe Now') }}</a>
                  </div>
                  @endif
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    @endif

    @endforeach
  </div>
  @endif
  </div>
</section>
<!-- Subscription Bundle end -->

<!-- Bundle start -->
@if(isset($bundles) && $hsetting->bundle_enable)
<section id="bundle-block" class="student-main-block">
  <div class="container">
    @if(count($bundles) > 0)
    <h4 class="student-heading">{{ __('Bundle Courses') }}</h4>

    <div id="bundle-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($bundles as $bundle)
      @if($bundle->status == 1)

      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-4{{$bundle->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($bundle['preview_image'] !== NULL && $bundle['preview_image'] !== '')
              <a href="{{ route('bundle.detail', $bundle->id) }}"><img
                  data-src="{{ asset('images/bundle/'.$bundle['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('bundle.detail', $bundle->id) }}"><img
                  data-src="{{ Avatar::create($bundle->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="view-user-img">
              <a href="" title=""><img src="http://eclass.test/images/user_img/159116548431.jpg"
                  class="img-fluid user-img-one" alt=""></a>
            </div>
            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('bundle.detail', $bundle->id) }}">{{ str_limit($bundle->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($bundle->user)['fname'] }}</span></h6>
              </div>
              <!-- <p class="btm-10"><a herf="#">{{ __('by') }} @if(isset($bundle->user)) {{ $bundle->user['fname'] }} {{ $bundle->user['lname'] }} @endif</a></p> -->

              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        {{ $bundle->order->count() }}</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if($bundle->type == 1 && $bundle->price != null)
                    <div class="rate text-right">
                      <ul>

                        @if($bundle->discount_price == !NULL)

                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format(  currency($bundle->discount_price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($bundle->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>


                        @else
                        <li><a><b>
                              {{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format(  currency($bundle->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>
                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="img-wishlist">
                <div class="protip-wishlist">
                  <ul>

                    <li class="protip-wish-btn"><a
                        href="https://calendar.google.com/calendar/r/eventedit?text={{ $bundle['title'] }}"
                        target="__blank" title="reminder"><i data-feather="bell"></i></a></li>

                    @if(Auth::check())

                    <li class="protip-wish-btn"><a class="compare" data-id="{{filter_var($bundle->id)}}"
                        title="compare"><i data-feather="bar-chart"></i></a></li>

                    @php
                    $wish = App\Wishlist::where('user_id', Auth::User()->id)->where('course_id',
                    $bundle->id)->first();
                    @endphp
                    @if ($wish == NULL)
                    <li class="protip-wish-btn">
                      <form id="demo-form2" method="post" action="{{ url('show/wishlist', $bundle->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$bundle->id}}" />

                        <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @else
                    <li class="protip-wish-btn-two">
                      <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $bundle->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$bundle->id}}" />

                        <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @endif
                    @else
                    <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                          data-feather="heart"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>

            </div>

          </div>
        </div>
        <div id="prime-next-item-description-block-4{{$bundle->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $bundle['title'] }}</h5>

              <div class="product-main-des">
                <p>{{ strip_tags(str_limit($bundle['detail'], $limit = 200, $end = '...')) }}</p>
              </div>
              <div>
                <div class="product-learn-dtl">
                  <ul>

                    @foreach ($bundle->course_id as $bundles)

                    @php
                    $course = App\Course::where('id', $bundles)->first();
                    @endphp
                    @isset($course)
                    <li><i data-feather="check-circle"></i>
                      <a href="#">{{ $course['title'] }}</a>
                    </li>
                    @endisset

                    @endforeach
                  </ul>
                </div>
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-12">
                    @if($bundle->type == 1)
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                    </div>
                    @else
                    @php
                    $order = App\Order::where('user_id', Auth::User()->id)->where('bundle_id',
                    $bundle->id)->first();
                    @endphp
                    @if(!empty($order) && $order->status == 1)
                    <div class="protip-btn">
                      <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                    </div>
                    @else
                    @php
                    $cart = App\Cart::where('user_id', Auth::User()->id)->where('bundle_id',
                    $bundle->id)->first();
                    @endphp
                    @if(!empty($cart))
                    <div class="protip-btn">
                      <form id="demo-form2" method="post" action="{{ route('remove.item.cart',$cart->id) }}">
                        {{ csrf_field() }}

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Remove From Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @else
                    <div class="protip-btn">
                      <form id="demo-form2" method="post" action="{{ route('bundlecart', $bundle->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="bundle_id" value="{{$bundle->id}}" />

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary"><i
                              data-feather="shopping-cart"></i>{{ __('Add To Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @endif
                    @endif
                    @endif
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary"><i
                          data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</a>
                    </div>
                    @endif
                    @else
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="" class="btn btn-secondary" title="course">{{ __('Purchased') }}</a>
                    </div>
                    @else
                    @php
                    $enroll = App\Order::where('user_id', Auth::User()->id)->where('bundle_id',
                    $bundle->id)->first();
                    @endphp
                    @if($enroll == NULL)
                    <div class="protip-btn">
                      <a href="{{url('enroll/show',$bundle->id)}}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @else
                    <div class="protip-btn">
                      <a href="" class="btn btn-secondary" title="Cart">{{ __('Purchased') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @endif
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      @endif

      @endforeach
    </div>
    @endif

  </div>
</section>
@endif
<!-- Bundle end -->
@if(! $bestselling->isEmpty() && $hsetting->bestselling_enable && isset($bestselling) && count($bestselling) > 0 )
<section id="student" class="student-main-block">
  <div class="container">
    <h4 class="student-heading">{{ __('Best selling Courses') }}</h4>
    <div id="bestseller-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($bestselling as $best)

      @if($best->courses->status == 1 )

      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block{{$best->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($best->courses['preview_image'] !== NULL && $best->courses['preview_image'] !== '')
              <a href="{{ route('user.course.show',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"><img
                  data-src="{{ asset('images/course/'.$best->courses['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('user.course.show',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"><img
                  data-src="{{ Avatar::create($best->title)->toBase64() }}" alt="course" class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="advance-badge">
              @if($best->courses['level_tags'] == !NULL)
              <span class="badge bg-primary">{{ $best->courses['level_tags'] }}</span>
              @endif
            </div>
            <div class="view-user-img">

              @if(optional($best->user)['user_img'] !== NULL && optional($best->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$best->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>

            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('user.course.show',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}">{{ str_limit($best->courses->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($best->user)['fname'] }}</span></h6>
              </div>
              <div class="rating">
                <ul>
                  <li>
                    <?php 
                                            $learn = 0;
                                            $price = 0;
                                            $value = 0;
                                            $sub_total = 0;
                                            $sub_total = 0;
                                            $reviews = App\ReviewRating::where('course_id',$best->courses->id)->get();
                                            ?>
                    @if(!empty($reviews[0]))
                    <?php
                                            $count =  App\ReviewRating::where('course_id',$best->courses->id)->count();

                                            foreach($reviews as $review){
                                                $learn = $review->price*5;
                                                $price = $review->price*5;
                                                $value = $review->value*5;
                                                $sub_total = $sub_total + $learn + $price + $value;
                                            }

                                            $count = ($count*3) * 5;
                                            $rat = $sub_total/$count;
                                            $ratings_var = ($rat*100)/5;
                                            ?>

                    <div class="pull-left">
                      <div class="star-ratings-sprite"><span style="width:<?php echo $ratings_var; ?>%"
                          class="star-ratings-sprite-rating"></span>
                      </div>
                    </div>


                    @else
                    <div class="pull-left">{{ __('No Rating') }}</div>
                    @endif
                  </li>
                  <!-- overall rating-->
                  <?php 
                                        $learn = 0;
                                        $price = 0;
                                        $value = 0;
                                        $sub_total = 0;
                                        $count =  count($reviews);
                                        $onlyrev = array();

                                        $reviewcount = App\ReviewRating::where('course_id', $best->courses->id)->WhereNotNull('review')->get();

                                        foreach($reviews as $review){

                                            $learn = $review->learn*5;
                                            $price = $review->price*5;
                                            $value = $review->value*5;
                                            $sub_total = $sub_total + $learn + $price + $value;
                                        }

                                        $count = ($count*3) * 5;
                                         
                                        if($count != 0)
                                        {
                                            $rat = $sub_total/$count;
                                     
                                            $ratings_var = ($rat*100)/5;
                                   
                                            $overallrating = ($ratings_var/2)/10;
                                        }
                                         
                                        ?>

                  @php
                  $reviewsrating = App\ReviewRating::where('course_id', $best->courses->id)->first();
                  @endphp
                  @if(!empty($reviewsrating))
                  <!-- <li>
                                            <b>{{ round($overallrating, 1) }}</b>
                                        </li> -->
                  @endif
                  <li class="reviews">
                    (@php
                    $data = App\ReviewRating::where('course_id', $best->courses->id)->count();
                    if($data>0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp Reviews)
                  </li>

                </ul>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i><span>
                        @php
                        $data = App\Order::where('course_id', $best->courses->id)->count();
                        if(($data)>0){

                        echo $data;
                        }
                        else{

                        echo "0";
                        }
                        @endphp</span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    @if( $best->courses->type == 1)
                    <div class="rate text-right">
                      <ul>

                        @if($best->courses->discount_price == !NULL)

                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($best->courses['discount_price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>

                        <li>
                          <a><b><strike>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($best->courses['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</strike></b></a>
                        </li>


                        @else

                        @if($c->price == !NULL)
                        <li>
                          <a><b>{{ activeCurrency()->getData()->position == 'l'  ? activeCurrency()->getData()->symbol :'' }}{{ price_format( currency($best->courses['price'], $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false) ) }}{{ activeCurrency()->getData()->position == 'r'  ? activeCurrency()->getData()->symbol :'' }}</b></a>
                        </li>
                        @endif

                        @endif
                      </ul>
                    </div>
                    @else
                    <div class="rate text-right">
                      <ul>
                        <li><a><b>{{ __('Free') }}</b></a></li>
                      </ul>
                    </div>
                    @endif
                  </div>
                </div>
              </div>



              <div class="img-wishlist">
                <div class="protip-wishlist">
                  <ul>

                    <li class="protip-wish-btn"><a
                        href="https://calendar.google.com/calendar/r/eventedit?text={{ $best['title'] }}"
                        target="__blank" title="reminder"><i data-feather="bell"></i></a></li>

                    @if(Auth::check())

                    <li class="protip-wish-btn"><a class="compare" data-id="{{filter_var($best->id)}}"
                        title="compare"><i data-feather="bar-chart"></i></a></li>

                    @php
                    $wish = App\Wishlist::where('user_id',
                    Auth::User()->id)->where('course_id',$best->courses->id)->first();
                    @endphp
                    @if ($wish == NULL)
                    <li class="protip-wish-btn">
                      <form id="demo-form2" method="post" action="{{ url('show/wishlist', $best->courses->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$best->courses->id}}" />

                        <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @else
                    <li class="protip-wish-btn-two">
                      <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $best->courses->id) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                        <input type="hidden" name="course_id" value="{{$best->courses->id}}" />

                        <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                            data-feather="heart"></i></button>
                      </form>
                    </li>
                    @endif
                    @else
                    <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                          data-feather="heart"></i></a></li>
                    @endif
                  </ul>
                </div>
              </div>


            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block{{$best->courses->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $best->courses['title'] }}</h5>
              <div class="main-des">
                <p>Last Updated: {{ date('jS F Y', strtotime($best->courses->updated_at)) }}</p>
              </div>

              <ul class="description-list">
                <li>
                  <i data-feather="play-circle"></i>
                  <div class="class-des">
                    {{ __('Classes') }}:
                    @php
                    $data = App\CourseClass::where('course_id', $best->courses->id)->count();
                    if($data > 0){

                    echo $data;
                    }
                    else{

                    echo "0";
                    }
                    @endphp
                  </div>
                </li>
                &nbsp;
                <li>
                  <div>
                    <div class="time-des">
                      <span class="">
                        <i data-feather="clock"></i>
                        @php

                        $classtwo = App\CourseClass::where('course_id',
                        $best->courses->id)->sum("duration");

                        @endphp
                        {{ $classtwo }} {{ __('Minutes')}}
                      </span>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="lang-des">
                    @if($best->courses['language_id'] == !NULL)
                    @if(isset($best->courses->language))
                    <i data-feather="globe"></i> {{ $best->courses->language['name'] }}
                    @endif
                    @endif
                  </div>
                </li>
              </ul>

              <div class="product-main-des">
                <p>{{ $best->courses->short_detail }}</p>
              </div>
              <div>
                @if($best->courses->whatlearns->isNotEmpty())
                @foreach($best->courses->whatlearns as $wl)
                @if($wl->status ==1)
                <div class="product-learn-dtl">
                  <ul>
                    <li><i data-feather="check-circle"></i>{{ str_limit($wl['detail'], $limit = 120, $end = '...') }}
                    </li>
                  </ul>
                </div>
                @endif
                @endforeach
                @endif
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-8">
                    @if($best->courses->type == 1)
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $order = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $best->courses->id)->first();
                    @endphp
                    @if(!empty($order) && $order->status == 1)
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $cart = App\Cart::where('user_id', Auth::User()->id)->where('course_id',
                    $best->courses->id)->first();
                    @endphp
                    @if(!empty($cart))
                    <div class="protip-btn">
                      <form id="demo-form2" method="post" action="{{ route('remove.item.cart',$cart->id) }}">
                        {{ csrf_field() }}

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Remove From Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @else
                    <div class="protip-btn">
                      <form id="demo-form2" method="post"
                        action="{{ route('addtocart',['course_id' => $best->courses->id, 'price' => $best->courses->price, 'discount_price' => $best->courses->discount_price ]) }}"
                        data-parsley-validate class="form-horizontal form-label-left">
                        {{ csrf_field() }}

                        <input type="hidden" name="category_id" value="{{$best->category['id'] ?? '-'}}" />

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">{{ __('Add To Cart') }}</button>
                        </div>
                      </form>
                    </div>
                    @endif
                    @endif
                    @endif
                    @else
                    @if($gsetting->guest_enable == 1)
                    <form id="demo-form2" method="post" action="{{ route('guest.addtocart', $best->courses->id) }}"
                      data-parsley-validate class="form-horizontal form-label-left">
                      {{ csrf_field() }}
                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary"><i
                            data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</button>
                      </div>
                    </form>
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary"><i
                          data-feather="shopping-cart"></i>&nbsp;{{ __('Add To Cart') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    @if(Auth::check())
                    @if(Auth::User()->role == "admin")
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"
                        class="btn btn-secondary" title="course">{{ __('Go To Course') }}</a>
                    </div>
                    @else
                    @php
                    $enroll = App\Order::where('user_id', Auth::User()->id)->where('course_id',
                    $best->courses->id)->first();
                    @endphp
                    @if($enroll == NULL)
                    <div class="protip-btn">
                      <a href="{{url('enroll/show',$best->courses->id)}}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('course.content',['id' => $best->courses->id, 'slug' => $best->courses->slug ]) }}"
                        class="btn btn-secondary" title="Cart">{{ __('Go To Course') }}</a>
                    </div>
                    @endif
                    @endif
                    @else
                    <div class="protip-btn">
                      <a href="{{ route('login') }}" class="btn btn-primary" title="Enroll Now"><i
                          data-feather="shopping-cart"></i>{{ __('Enroll Now') }}</a>
                    </div>
                    @endif
                    @endif
                  </div>
                  <div class="col-lg-4">
                    <div class="img-wishlist">
                      <div class="protip-wishlist">
                        <ul>
                          @if(Auth::check())

                          @php
                          $wish = App\Wishlist::where('user_id',
                          Auth::User()->id)->where('course_id', $best->courses->id)->first();
                          @endphp
                          @if ($wish == NULL)
                          <li class="protip-wish-btn">
                            <form id="demo-form2" method="post" action="{{ url('show/wishlist', $best->courses->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$best->courses->id}}" />

                              <button class="wishlisht-btn" title="Add to wishlist" type="submit"><i
                                  data-feather="heart"></i></button>
                            </form>
                          </li>
                          @else
                          <li class="protip-wish-btn-two">
                            <form id="demo-form2" method="post" action="{{ url('remove/wishlist', $best->id) }}"
                              data-parsley-validate class="form-horizontal form-label-left">
                              {{ csrf_field() }}

                              <input type="hidden" name="user_id" value="{{Auth::User()->id}}" />
                              <input type="hidden" name="course_id" value="{{$best->courses->id}}" />

                              <button class="wishlisht-btn heart-fill" title="Remove from Wishlist" type="submit"><i
                                  data-feather="heart"></i></button>
                            </form>
                          </li>
                          @endif
                          @else
                          <li class="protip-wish-btn"><a href="{{ route('login') }}" title="heart"><i
                                data-feather="heart"></i></a></li>
                          @endif
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      @endforeach

    </div>
  </div>
</section>
@endif
<!-- Advertisement -->
@if(isset($advs))
@foreach($advs as $adv)
@if($adv->position == 'belowbundle')
<br>
<section id="student" class="student-main-block btm-40">
  <div class="container">
    <a href="{{ $adv->url1 }}" title="{{ __('Click to visit') }}">
      <img class="lazy img-fluid advertisement-img-one" data-src="{{ url('images/advertisement/'.$adv->image1) }}"
        alt="{{ $adv->image1 }}">
    </a>
  </div>
</section>
@endif

@endforeach

@endif


<!-- Batch start -->
@if($hsetting->batch_enable && isset($batches))

<section id="batch-block" class="student-main-block">
  <div class="container">
    @if(count($batches) > 0)
    <h4 class="student-heading">{{ __('Batches') }}</h4>

    <div id="batch-view-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($batches as $batch)
      @if($batch->status == 1)

      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-5{{$batch->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($batch['preview_image'] !== NULL && $batch['preview_image'] !== '')
              <a href="{{ route('batch.detail', $batch->id) }}"><img
                  data-src="{{ asset('images/batch/'.$batch['preview_image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('batch.detail', $batch->id) }}"><img
                  data-src="{{ Avatar::create($batch->title)->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif
            </div>
            <div class="view-user-img">

              @if(optional($batch->user)['user_img'] !== NULL && optional($batch->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$batch->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('batch.detail', $batch->id) }}">{{ str_limit($batch->title, $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($c->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="count-user">
                      <i data-feather="user"></i>
                      <span>
                        @php
                        $data = App\Batch::where('allowed_users', $batch->id)->count();
                        @endphp
                        {{ $data }}
                      </span>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($batch['created_at'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block-5{{$batch->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $batch['title'] }}</h5>
              <div class="view-time btm-10">
                <a href="#"><i data-feather="clock"></i>
                  {{ date('h:i:s A',strtotime($batch['created_at'])) }}</a>
              </div>
              <div class="main-des">
                <p>{!! str_limit($batch['detail'], $limit = 250, $end = '...') !!}</p>
              </div>

            </div>
          </div>
        </div>
      </div>
      @endif
      @endforeach
    </div>
    @endif
  </div>
</section>
@endif
<!-- Batch end -->
<!-- Zoom start -->
@if($hsetting->livemeetings_enable == 1)
@if($gsetting->zoom_enable == '1' || $gsetting->bbl_enable == '1' || $gsetting->googlemeet_enable == '1' ||
$gsetting->jitsimeet_enable == '1')
<section id="student" class="student-main-block">
  <div class="container">
    @php
    $mytime = Carbon\Carbon::now();
    @endphp
    @if( count($meetings) > 0 || count($bigblue) > 0 || count($allgooglemeet) > 0 || count($jitsimeeting) > 0 )
    <h4 class="student-heading">{{ __('Live Meetings') }}</h4>
    <div id="zoom-view-slider" class="student-view-slider-main-block owl-carousel">

      @if( ! $meetings->isEmpty() )
      @foreach($meetings as $meeting)
      <div class="item student-view-block student-view-block-1" style="display:none;">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-6{{$meeting->id}}">
          <div class="view-block">
            <div class="view-img">

              @if($meeting['image'] !== NULL && $meeting['image'] !== '')
              <a href="{{ route('zoom.detail', $meeting->id) }}"><img
                  data-src="{{ asset('images/zoom/'.$meeting['image']) }}" alt="course" class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('zoom.detail', $meeting->id) }}"><img
                  data-src="{{ Avatar::create($meeting['meeting_title'])->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif


            </div>
            <div class="view-user-img">

              @if(optional($meeting->user)['user_img'] !== NULL && optional($meeting->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$meeting->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            @if(asset('images/meeting_icons/zoom.png') == !NULL)
            <div class="meeting-icon"><img src="{{ asset('images/meeting_icons/zoom.png')}}" class="img-circle" alt="">
            </div>
            @endif


            <div class="view-dtl">
              <div class="view-heading"><a href="#">
                  {{ str_limit($meeting->meeting_title, $limit = 30, $end = '...') }}</a></div>
              <div class="user-name">
                <h6>By <span>{{ optional($meeting->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block-6{{$meeting->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading"><a
                  href="{{ route('zoom.detail', $meeting->id) }}">{{ $meeting['meeting_title'] }}</a>
              </h5>
              <div class="protip-img">
                <h6 class="user-name">{{ __('by') }}
                  @if(isset($meeting->user)) {{ $meeting->user['fname'] }} @endif</h6>
                <p class="meeting-owner btm-10"><a herf="#">Meeting Owner:
                    {{ $meeting->owner_id }}</a></p>
              </div>
              <div class="main-des meeting-main-des">
                <div class="main-des-head">Start At: </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="des-btn-block">
                <a href="{{ $meeting->zoom_url }}" class="iframe btn btn-light">{{ __('Join Meeting') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      @endif

      @if( ! $bigblue->isEmpty() )
      @foreach($bigblue as $bbl)


      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-7{{$bbl->id}}">
          <div class="view-block">
            <div class="view-img">
              <a href="{{ route('bbl.detail', $bbl->id) }}"><img
                  data-src="{{ Avatar::create($bbl['meetingname'])->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
            </div>
            <div class="view-user-img">

              @if(optional($bbl->user)['user_img'] !== NULL && optional($bbl->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$bbl->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            @if(asset('images/meeting_icons/bigblue.png') == !NULL)
            <div class="meeting-icon"><img src="{{ asset('images/meeting_icons/bigblue.png')}}" class="img-circle"
                alt=""></div>
            @endif

            <div class="view-dtl">
              <div class="view-heading"><a
                  href="{{ route('bbl.detail', $bbl->id) }}">{{ str_limit($bbl['meetingname'], $limit = 30, $end = '...') }}</a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($bbl->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($bbl['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($bbl['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div id="prime-next-item-description-block-7{{$bbl->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $bbl['meetingname'] }}</h5>
              <div class="protip-img">
                <a href="{{ route('bbl.detail', $bbl->id) }}"><img
                    src="{{ Avatar::create($bbl['meetingname'])->toBase64() }}" alt="course" class="img-fluid"></a>
              </div>

              <div class="main-des">
                <p>{!! $bbl['detail'] !!}</p>
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-12">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @endforeach
      @endif

      @if( isset($allgooglemeet) )
      @foreach($allgooglemeet as $meeting)
      <div class="item student-view-block student-view-block-1" style="display:none;">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-6{{ $meeting['meeting_id'] }}">
          <div class="view-block">
            <div class="view-img">

              @if($meeting['image'] !== NULL && $meeting['image'] !== '')
              <a href="{{ route('googlemeetdetailpage.detail', $meeting['id']) }}"><img
                  data-src="{{ asset('images/googlemeet/profile_image/'.$meeting['image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('googlemeetdetailpage.detail', $meeting['id']) }}"><img
                  data-src="{{ Avatar::create($meeting['meeting_title'])->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif


            </div>
            <div class="view-user-img">

              @if(optional($meeting->user)['user_img'] !== NULL && optional($meeting->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$meeting->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            @if(asset('images/meeting_icons/google.png') == !NULL)
            <div class="meeting-icon"><img src="{{ asset('images/meeting_icons/google.png')}}" class="img-circle"
                alt=""></div>
            @endif

            <div class="view-dtl">
              <div class="view-heading"><a href="#">
                  {{ str_limit($meeting->meeting_title, $limit = 30, $end = '...') }}</a></div>
              <div class="user-name">
                <h6>By <span>{{ optional($meeting->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block-6{{$meeting['meeting_id']}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading"><a
                  href="{{ route('zoom.detail', $meeting->id) }}">{{ $meeting['meeting_title'] }}</a>
              </h5>
              <div class="protip-img">
                <h6 class="user-name">{{ __('by') }}
                  @if(isset($meeting->user)) {{ $meeting->user['fname'] }} @endif</h6>
                <p class="meeting-owner btm-10"><a herf="#">{{ __('Meeting Owner:') }}
                    {{ $meeting->owner_id }}</a></p>
              </div>
              <div class="main-des meeting-main-des">
                <div class="main-des-head">{{ __('Start At:') }} </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="main-des meeting-main-des">
                <div class="main-des-head">{{ __('End At: ') }}</div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['end_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i> {{ date('h:i:s A',strtotime($meeting['end_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="des-btn-block">
                <a href="{{ $meeting->meet_url }}" target="_blank" class="btn btn-light">Join
                  {{ __('Meeting') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      @endif

      @if( ! $jitsimeeting->isEmpty() )
      @foreach($jitsimeeting as $meeting)
      <div class="item student-view-block student-view-block-1" style="display:none;">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-6{{ $meeting['meeting_id'] }}">
          <div class="view-block">
            <div class="view-img">

              @if($meeting['image'] !== NULL && $meeting['image'] !== '')
              <a href="{{ route('jitsipage.detail', $meeting['id']) }}"><img
                  data-src="{{ asset('images/jitsimeet/'.$meeting['image']) }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @else
              <a href="{{ route('jitsipage.detail', $meeting['id']) }}"><img
                  data-src="{{ Avatar::create($meeting['meeting_title'])->toBase64() }}" alt="course"
                  class="img-fluid owl-lazy"></a>
              @endif


            </div>
            <div class="view-user-img">

              @if(optional($meeting->user)['user_img'] !== NULL && optional($meeting->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$meeting->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            @if(asset('images/meeting_icons/jitsi.png') == !NULL)
            <div class="meeting-icon"><img src="{{ asset('images/meeting_icons/jitsi.png')}}" class="img-circle" alt="">
            </div>
            @endif

            <div class="view-dtl">
              <div class="view-heading"><a href="#">
                  {{ str_limit($meeting->meeting_title, $limit = 30, $end = '...') }}</a></div>
              <div class="user-name">
                <h6>By <span>{{ optional($meeting->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block-6{{$meeting['meeting_id']}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading"><a
                  href="{{ route('zoom.detail', $meeting->id) }}">{{ $meeting['meeting_title'] }}</a>
              </h5>
              <div class="protip-img">
                <h6 class="user-name">{{ __('by') }}
                  @if(isset($meeting->user)) {{ $meeting->user['fname'] }} @endif</h6>
                <p class="meeting-owner btm-10"><a herf="#">{{ __('Meeting Owner')}}:
                    {{ $meeting->owner_id }}</a></p>
              </div>
              <div class="main-des meeting-main-des">
                <div class="main-des-head">Start At: </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($meeting['start_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="main-des meeting-main-des">
                <div class="main-des-head">{{ __('End At')}}: </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($meeting['end_time'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i> {{ date('h:i:s A',strtotime($meeting['end_time'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="des-btn-block">
                <a href="{{url('meetup-conferencing/'.$meeting->meeting_id) }}" target="_blank"
                  class="btn btn-light">{{ __('Join Meeting')}}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      @endif


    </div>

    @endif

  </div>
</section>
@endif
@endif
<!-- Zoom end -->

<!-- google class room start -->
@if(Schema::hasTable('googleclassrooms') && Module::has('Googleclassroom') &&
Module::find('Googleclassroom')->isEnabled())
@include('googleclassroom::frontend.home')
@endif

<!-- google class room end -->
<!-- Bundle start -->
@if($hsetting->blog_enable == 1 && ! $blogs->isEmpty() )
<section id="student" class="student-main-block" style="display:none;">
  <div class="container">

    <h4 class="student-heading">{{ __('Recent Blogs') }}</h4>
    <div id="blog-post-slider" class="student-view-slider-main-block owl-carousel">
      @foreach($blogs as $blog)


      <div class="item student-view-block student-view-block-1">
        <div class="genre-slide-image @if($gsetting['course_hover'] == 1) protip @endif" data-pt-placement="outside"
          data-pt-interactive="false" data-pt-title="#prime-next-item-description-block-8{{$blog->id}}">
          <div class="view-block">
            <div class="view-img">
              @if($blog['image'] !== NULL && $blog['image'] !== '')
              @if($blog->slug != NULL)
              <a href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => $blog->slug ]) }}">
                @else
                <a
                  href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => str_slug(str_replace('-','&',$blog->heading)) ]) }}">
                  @endif

                  <img data-src="{{ asset('images/blog/'.$blog['image']) }}" alt="course" class="img-fluid owl-lazy">
                </a>
                @else
                @if($blog->slug != NULL)
                <a href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => $blog->slug ]) }}">
                  @else
                  <a
                    href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => str_slug(str_replace('-','&',$blog->heading)) ]) }}">
                    @endif
                    <img data-src="{{ Avatar::create($blog->heading)->toBase64() }}" alt="course"
                      class="img-fluid owl-lazy">
                  </a>
                  @endif
            </div>
            <div class="view-user-img">

              @if(optional($blog->user)['user_img'] !== NULL && optional($blog->user)['user_img'] !== '')
              <a href="" title=""><img src="{{ asset('images/user_img/'.$blog->user['user_img']) }}"
                  class="img-fluid user-img-one" alt=""></a>
              @else
              <a href="" title=""><img src="{{ asset('images/default/user.png') }}" class="img-fluid user-img-one"
                  alt=""></a>
              @endif


            </div>
            <div class="view-dtl">
              <div class="view-heading">
                @if($blog->slug != NULL)
                <a href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => $blog->slug ]) }}">
                  {{ str_limit($blog['heading'], $limit = 25, $end = '...') }}
                  @else
                  <a
                    href="{{ route('blog.detail', ['id' => $blog->id, 'slug' => str_slug(str_replace('-','&',$blog->heading)) ]) }}">

                    {{ str_limit($blog['heading'], $limit = 25, $end = '...') }}
                    @endif
                  </a>
              </div>
              <div class="user-name">
                <h6>By <span>{{ optional($blog->user)['fname'] }}</span></h6>
              </div>
              <div class="view-footer">

                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-date">
                      <a href="#"><i data-feather="calendar"></i>
                        {{ date('d-m-Y',strtotime($blog['created_at'])) }}</a>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="view-time">
                      <a href="#"><i data-feather="clock"></i>
                        {{ date('h:i:s A',strtotime($blog['created_at'])) }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="prime-next-item-description-block-8{{$blog->id}}" class="prime-description-block">
          <div class="prime-description-under-block">
            <div class="prime-description-under-block">
              <h5 class="description-heading">{{ $blog['heading'] }}</h5>
              <div class="row btm-20">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <div class="view-date">
                    <a href="#"><i data-feather="calendar"></i> {{ date('d-m-Y',strtotime($blog['start_time'])) }}</a>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                  <div class="view-time">
                    <a href="#"><i data-feather="clock"></i> {{ date('h:i:s A',strtotime($blog['start_time'])) }}</a>
                  </div>
                </div>
              </div>
              <div class="main-des">
                <p>{{substr(preg_replace("/\r\n|\r|\n/",'',strip_tags(html_entity_decode($blog->detail))), 0, 400)}}
                </p>
              </div>
              <div class="des-btn-block">
                <div class="row">
                  <div class="col-lg-12">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @endforeach
    </div>

  </div>
</section>
@endif
<!-- Bundle end -->
<!-- recommendations start -->

<!-- @if($hsetting->became_enable == 1)
<section id="border-recommendation" class="border-recommendation">
  @php
  $gets = App\JoinInstructor::first();
  @endphp
  @if(isset($gets))
  <div class="recommendation-main-block  text-center">
    <div class="container-fluid p-0">
      <div class="row no-gutters">
        <div class="col-lg-4 col-sm-5">
          <div class="recommendations-block">
            <h4 class="recommendations-heading"> {{ $gets->text }}</h4>
            <p>{{ $gets->detail }}</p>
            <div class="recommendation-btn">
              <a href="" data-toggle="modal" data-target="#myModalinstructor" class="btn btn-primary"
                title="Become an Instructor">Become an Instructor</a>
            </div>
          </div>
        </div>
        <div class="col-lg-8 col-sm-7">
          <div id="recommendations-slider" class="recommendations-main-slider owl-carousel">
            <div class="item">
              <div class="recommendations-img">
                @if($gets['img'] !== NULL && $gets['img'] !== '')
                <img src="{{ url('/images/joininstructor/'.$gets->img) }}" height="100px;" width="100px;" />
                @else
                <img src="{{ Avatar::create($gets->text)->toBase64() }}" alt="course" class="img-fluid">
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</section>
@endif -->

<!-- recommendations end -->
<!-- categories start -->

@if($hsetting->featuredcategories_enable == 1 && !$category->isEmpty())
<section id="categories" class="categories-main-block" style="display:none;">
  <div class="container">
    @if( count($category->where('featured', '1')) > 0)

    <h3 class="categories-heading">{{ __('FeaturedCategories') }}</h3>
    <div class="row">
      @foreach($category as $t)
      @if($t->status == 1 && $t->featured == 1)

      <div class="col-lg-2 col-md-4 col-sm-4 col-6">

        <div class="image-container btm-20">
          <a
            href="{{ route('category.page',['id' => $t->id, 'category' => str_slug(str_replace('-','&',$t->slug))]) }}">
            <div class="image-overlay">
              <span><i class="fa {{ $t['icon'] }}"></i>{{ $t['title'] }}
                <div class="categories-img-count">{{ $t->courses->count() }} {{ __('Courses')}}</div>
              </span>
            </div>
            @if($t['cat_image'] == !NULL)
            <img src="{{ asset('images/category/'.$t['cat_image']) }}">
            @else
            <img src="{{ Avatar::create($t->title)->toBase64() }}">
            @endif
          </a>
        </div>

      </div>

      @endif
      @endforeach
    </div>

    @endif
  </div>
</section>
@endif

<!-- categories end -->
<!-- testimonial start -->

<!-- @if($hsetting->testimonial_enable == 1 && ! $testi->isEmpty() )
<section id="testimonial" class="testimonial-main-block">
  <div class="container">
    <h4>{{ __('HomeTestimonial') }}</h4>
    <div id="testimonial-slider" class="testimonial-slider-main-block owl-carousel">
      @foreach($testi as $tes)
      <div class="item testimonial-block text-center">
        <div class="testimonial-block-one">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
              <div class="testimonial-img">
                <img data-src="{{ asset('images/testimonial/'.$tes['image']) }}" alt="blog" class="img-fluid owl-lazy">
              </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 col-12">
              <div class="testimonial-name">
                <h5 class="testimonial-heading">{{ $tes['client_name'] }}</h5>
                <p class="testimonial-para">{{ $tes['designation'] }}</p>
              </div>
              <div class="testimonial-rating">
                @for($i = 1; $i <= 5; $i++) @if($i<=$tes->rating)
                  <i class='fa fa-star' style='color:orange'></i>
                  @else
                  <i class='fa fa-star' style='color:#ccc'></i>
                  @endif
                  @endfor
              </div>
            </div>
          </div>
          <p>
            {{ str_limit(preg_replace("/\r\n|\r|\n/",'',strip_tags(html_entity_decode($tes->details))) , $limit = 300, $end = '...') }}
          </p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif -->

<!-- testimonial end -->
<!-- video start -->

<!-- @if($hsetting->video_enable == 1 && isset($videosetting) )
<section id="video" class="video-main-block">
  <div class="container-fluid">
    <div class="video-block">
      @if($videosetting['image'] !== NULL && $videosetting['image'] !== '')
      <img src="{{ url('/images/videosetting/'.$videosetting->image) }}" class="img-fluid" />
      @else
      <img src="{{ Avatar::create($videosetting->tittle)->toBase64() }}" alt="course" class="img-fluid">
      @endif
      <div class="overlay-bg"></div>
    </div>
    <div class="video-play-btn">
      <a class="play-btn" href="#video_modal" data-toggle="modal"></a>
      <div id="video_modal" class="modal fade">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-body">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <div class="embed-responsive embed-responsive-16by9">
                <iframe id="elearningVideo" class="embed-responsive-item" width="560" height="315"
                  src="{{$videosetting->url}}" allowfullscreen></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="video-dtl text-center">
      <h3 class="video-heading text-white">{{ $videosetting->tittle }}</h3>
      <p class="text-white">{{ $videosetting->description }}</p>
    </div>
  </div>
</section>
@endif -->

<!-- video end -->
<!-- instructor start -->
@if( $hsetting->instructor_enable == 1 && !$instructors->isEmpty())
<section id="instructor-home" class="instructor-home-main-block">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-6">
        <h4 class="student-heading">{{ __('Instructor') }}</h4>
      </div>
      <div class="col-lg-6 col-6">
        <div class="instructor-button txt-rgt">
          <a href="{{ route('allinstructor/view') }}" class="btn btn-secondary" title="All Instructor">All Instructor
          </a>
        </div>
      </div>
    </div>

    <div id="instructor-home-slider" class="instructor-home-main-slider owl-carousel">
      @foreach($instructors as $inst)
      <div class="item">
        <div class="instructor-home-block text-center">
          <div class="instructor-home-block-one">
            @if($inst['user_img'] !== NULL && $inst['user_img'] !== '')
            <a href="#" title=""><img src="{{ url('/images/user_img/'.$inst->user_img) }}" class="img-fluid" /></a>
            @else
            <a href="#" title=""><img src="{{ Avatar::create($inst->fname)->toBase64() }}" alt="course"
                class="img-fluid"></a>
            @endif
            <div class="tooltip">
              <div class="tooltip-icon">
                <i data-feather="share-2"></i>
              </div>
              <span class="tooltiptext">
                <div class="instructor-home-social-icon">
                  <ul>
                    <li><a href="{{ $inst->fb_url }}"><i data-feather="facebook"></i></a></li>
                    <li><a href="{{ $inst->twitter_url }}"><i data-feather="twitter"></i></a></li>
                    <li><a href="{{ $inst->youtube_url }}"></a><i data-feather="youtube"></i></a></li>
                    <li><a href="{{ $inst->linkedin_url }}"><i data-feather="linkedin"></i></a></li>
                  </ul>
                </div>
              </span>
            </div>
            <div class="instructor-home-dtl">
              <h4 class="instructor-home-heading"><a href="#" title="">{{ $inst->fname }} {{ $inst->lname }}</a></h4>
              <p>{{ $inst->role }}</p>

              @php

              $followers = App\Followers::where('user_id', '!=', $inst->id)->where('follower_id', $inst->id)->count();
              $followings = App\Followers::where('user_id', $inst->id)->where('follower_id','!=', $inst->id)->count();
              
              $course = App\Course::where('user_id', $inst->id)->count();

              @endphp
              <div class="instructor-home-info">
                <ul>
                  <li>{{ $course }} {{ __('Courses') }}</li>
                  <!-- <li>{{ $followers }} {{ __('Follower') }}</li>
                  <li>{{ $followings }} {{ __('Following') }}</li> -->
                </ul>
              </div>
              <hr>
              <div class="instructor-home-btn">
                <a href="{{ route('allinstructor/profile',$inst->id) }}" class="btn btn-primary" title="View Profile"><i
                    data-feather="eye"></i>View Profile</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</section>
@endif
<!-- instructor end -->
<!-- Advertisement -->

<!-- @if(isset($advs))
@foreach($advs as $adv)
@if($adv->position == 'belowtestimonial')
<br>
<section id="student" class="student-main-block btm-40">
  <div class="container">
    <a href="{{ $adv->url1 }}" title="{{ __('Click to visit') }}">
      <img class="lazy img-fluid advertisement-img-one" data-src="{{ url('images/advertisement/'.$adv->image1) }}"
        alt="{{ $adv->image1 }}">
    </a>
  </div>
</section>

@endif
@endforeach
@endif -->


<!-- @if( !$trusted->isEmpty() )
<section id="trusted" class="trusted-main-block">
  <div class="container">
    <div class="patners-block">

      <h6 class="patners-heading text-center btm-40">{{ __('Trusted By Companies') }}</h6>
      <div id="patners-slider" class="patners-slider owl-carousel">
        @foreach($trusted as $trust)
        <div class="item-patners-img">
          <a href="{{ $trust['url'] }}" target="_blank"><img data-src="{{ asset('images/trusted/'.$trust['image']) }}"
              class="img-fluid owl-lazy" alt="patners-1"></a>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</section>
@endif -->

<!-- google adsense code -->
<!-- <section id="trusted" class="trusted-main-block">
  <div class="container-fluid" id="adsense">
    @php
    $ad = App\Adsense::first();
    @endphp
    <?php
            // if (isset($ad) ) {
            //  if ($ad->ishome==1 && $ad->status==1) {
            //     $code = $ad->code;
            //     echo html_entity_decode($code);
            //  }
            // }
        ?>
  </div>
</section> -->

@endsection

@section('custom-script')
<script>
(function($) {
  "use strict";
  $(function() {
    $("#home-tab").trigger("click");
  });
})(jQuery);

function showtab(id) {
  $.ajax({
    type: 'GET',
    url: '{{ url(' / tabcontent ') }}/' + id,
    dataType: 'json',
    success: function(data) {

      $('.btn_more').html(data.btn_view);
      $('#tabShow').html(data.tabview);

    }
  });
}
</script>

<script src="{{ url('js/colorbox-script.js')}}"></script>


<script>
"use Strict";
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(function() {
  $('.compare').on('click', function(e) {
    var id = $(this).data('id');
    $.ajax({
      type: "POST",
      dataType: "json",
      url: 'compare/dataput',
      data: {
        id: id
      },
      success: function(data) {}
    });
  });
});
</script>

<script>
$(document).ready(function() {
  /* Get iframe src attribute value i.e. YouTube video url
  and store it in a variable */
  var url = $("#elearningVideo").attr('src');

  /* Assign empty url value to the iframe src attribute when
  modal hide, which stop the video playing */
  $("#video_modal").on('hide.bs.modal', function() {
    $("#elearningVideo").attr('src', '');
  });

  /* Assign the initially stored url back to the iframe src
  attribute when modal is displayed again */
  $("#video_modal").on('show.bs.modal', function() {
    $("#elearningVideo").attr('src', url);
  });
});
</script>

@endsection