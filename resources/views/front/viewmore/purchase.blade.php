@extends('theme.master')
@section('title')
@section('content')
@include('admin.message')
<!-- breadcumb start -->
@php
$gets = App\Breadcum::first();
@endphp
@if(isset($gets))
<section id="business-home" class="business-home-main-block">
    <div class="business-img">
        @if($gets['img'] !== NULL && $gets['img'] !== '')
        <img src="{{ url('/images/breadcum/'.$gets->img) }}" class="img-fluid" alt="" />
        @else
        <img src="{{ Avatar::create($gets->text)->toBase64() }}" alt="{{ __('course')}}" class="img-fluid">
        @endif
    </div>
    <div class="overlay-bg"></div>
    <div class="container-fluid">
        <div class="business-dtl">
            <div class="row">
                <div class="col-lg-6">
                    <div class="bredcrumb-dtl">
                        <h1 class="wishlist-home-heading">{{ __('My Courses') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
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
@if( isset($enroll) )
<section id="student" class="student-main-block top-40">
    <div class="container">

        @if(count($enroll) > 0)
        <div class="row">
            <div class="col-lg-6">
                <h4 class="student-heading">{{ __('My Purchased Courses') }}</h4>
            </div>
        </div>
        
            <div class="row">
                <div class="col-lg-3">
                    @foreach($enroll as $enrol)
                    @if(isset($enrol->courses) && $enrol->courses['status'] == 1 )
                    <div class="item student-view-block student-view-block-1">
                        <div class="genre-slide-image">
                            <div class="view-block">
                                <div class="view-img">
                                    @if($enrol->courses['preview_image'] !== NULL && $enrol->courses['preview_image'] !== '')
                                    <a
                                        href="{{ route('course.content',['id' => $enrol->courses->id, 'slug' => $enrol->courses->slug ]) }}">
                                        <img src="{{ asset('images/course/'.$enrol->courses['preview_image']) }}"
                                            alt="course" class="img-fluid owl-lazy"></a>
                                    @else
                                    <a
                                        href="{{ route('course.content',['id' => $enrol->courses->id, 'slug' => $enrol->courses->slug ]) }}">
                                        <img src="{{ Avatar::create($enrol->courses->title)->toBase64() }}" alt="course"
                                            class="img-fluid owl-lazy"></a>
                                    @endif
                                </div>
                                <div class="view-user-img">

                                    @if($enrol->user['user_img'] !== NULL && $enrol->user['user_img'] !== '')
                                    <a href="" title=""><img src="{{ asset('images/user_img/'.$enrol->user['user_img']) }}"
                                            class="img-fluid user-img-one" alt=""></a>
                                    @else
                                    <a href="" title=""><img src="{{ asset('images/default/user.png') }}"
                                            class="img-fluid user-img-one" alt=""></a>
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
                                                    <div class="star-ratings-sprite"><span
                                                            style="width:<?php echo $ratings_var; ?>%"
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
                                                
                                                if($count != "")
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
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                                @if( $enrol->courses->type == 1)
                                                <div class="rate text-right">
                                                    <ul>


                                                        @if($enrol->courses->discount_price == !NULL)

                                                        <li><a><b>{{ activeCurrency()->symbol }}{{ price_format( currency($enrol->courses->discount_price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}</b></a>
                                                        </li>

                                                        <li><a><b><strike>{{ activeCurrency()->symbol }}{{ price_format( currency($enrol->courses->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}</strike></b></a>
                                                        </li>




                                                        @else

                                                        <li><a><b>
                                                            {{ activeCurrency()->symbol }}{{ price_format( currency($enrol->courses->price, $from = $currency->code, $to = Session::has('changed_currency') ? Session::get('changed_currency') : $currency->code, $format = false)) }}</b></a>
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
            </div>
        @endif

    </div>
</section>
@endif
@endif
<section id="instructor-info" class="instructor-info-main-block">
    <div class="container">
        
    </div>
</section>
@endsection