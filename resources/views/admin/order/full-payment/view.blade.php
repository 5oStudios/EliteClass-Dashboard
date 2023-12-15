@extends('admin.layouts.master')
@section('title', __('View Invoice'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Invoice') }}
        @endslot
        @slot('menu1')
            {{ __('Invoice') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('full.payments') }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <!-- End row -->
        <div class="row justify-content-center">
            <!-- Start col -->
            <div class="col-md-12 col-lg-10 col-xl-10">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div id="printContent" class="invoice">
                            <div class="invoice-head">
                                <div class="row">
                                    <div class="col-12 col-md-7 col-lg-7">
                                        @if ($setting->logo_type == 'L')
                                            <div class="logo-invoice">
                                                <img src="{{ asset('images/logo/' . $setting->logo) }}"
                                                    style="width:100px;height:50px">
                                            </div>
                                        @else()
                                            <a href="{{ url('/') }}"><b>
                                                    <div class="logotext">{{ $setting->project_title }}</div>
                                                </b></a>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-5 col-lg-5">
                                        <div class="invoice-name pull-right">
                                            <h5 class="text-uppercase mb-3">{{ __('Invoice') }}</h5>
                                            <small>{{ __('adminstaticword.Date') }}:&nbsp;{{ date('jS F Y', strtotime($show['created_at'])) }}</small>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="invoice-billing">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 col-lg-4 ml-3">
                                        <div class="invoice-address">
                                            @if ($show->course_id != null)
                                                <h6>{{ $show->user['fname'] }} {{ $show->user['lname'] }}</h6>
                                                <ul class="list-unstyled">
                                                    <li><b>{{ __('Mobile Number') }}:</b> {{ $show->user['mobile'] }}</li>
                                                    <li><b>{{ __('Email') }}:</b> {{ $show->user['email'] }}</li>
                                                </ul>
                                            @elseif($show->meeting != null)
                                                <h6>{{ $show->user['fname'] }} {{ $show->user['lname'] }}</h6>
                                                <ul class="list-unstyled">
                                                    <li><b>{{ __('Mobile Number') }}:</b> {{ $show->user['mobile'] }}</li>
                                                    <li><b>{{ __('Email') }}:</b> {{ $show->user['email'] }}</li>
                                                </ul>
                                            @else
                                                <h6>{{ $show->user['fname'] }} {{ $show->user['lname'] }}</h6>
                                                <ul class="list-unstyled">
                                                    <li><b>{{ __('Mobile Number') }}:</b> {{ $show->user['mobile'] }}</li>
                                                    <li><b>{{ __('Email') }}:</b> {{ $show->user['email'] }}</li>
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 col-lg-4 ml-3">
                                        <b>{{ __('adminstaticword.Enrolled') }}:</b>
                                        {{ date('jS F Y', strtotime($show['created_at'])) }}<br>
                                    </div>
                                </div><br>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 ml-3">
                                        @if ($show->course_id)
                                            <b>{{ __('adminstaticword.Course') }}:</b> {{ $show->courses->_title() }}
                                        @elseif($show->bundle_id)
                                            <b>{{ __('adminstaticword.BundleName') }}:</b> {{ $show->bundle->_title() }}
                                        @elseif($show->meeting_id)
                                            <b>{{ __('adminstaticword.Meeting') }}:</b> {{ $show->meeting->_title() }}
                                        @elseif($show->offline_session_id)
                                            <b>{{ __('In-Person Session') }}:</b> {{ $show->offlinesession->_title() }}
                                        @elseif($show->chapter_id)
                                            <b>{{ __('Chapter') }}:</b> {{ $show->chapter->_title() }}
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 ml-3">
                                        @if ($show->bundle_id)
                                            <b>{{ __('adminstaticword.Courses') }}: </b>
                                            @foreach ($bundle_order->course_id as $bundle_course)
                                                @php
                                                    $coursess = App\Course::where('id', $bundle_course)->first();
                                                @endphp
                                                {{ $coursess->title }}<br />&emsp;&emsp;&emsp;&emsp;
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 col-lg-4 ml-3">
                                        <b>{{ __('adminstaticword.StartDate') }}:</b>
                                        {{ date('jS F Y', strtotime($show['enroll_start'])) }}<br>
                                        <b>
                                            @if ($show->enroll_expire != null)
                                                {{ __('adminstaticword.EndDate') }}:
                                        </b> {{ date('jS F Y', strtotime($show['enroll_expire'])) }}<br>
                                        @endif
                                    </div>
                                </div><br>
                            </div>

                            <div class="invoice-summary">
                                <div class="table-responsive ml-2">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>{{ __('adminstaticword.OrderId') }}</th>
                                                <th>{{ __('adminstaticword.TransactionId') }}</th>
                                                <th>{{ __('adminstaticword.PaymentMethod') }}</th>
                                                <th>{{ __('adminstaticword.PaymentGatewayServiceCharges') }}</th>
                                                <th>{{ __('adminstaticword.Currency') }}</th>
                                                @if ($show->coupon_discount != 0 || $show->coupon_discount != null)
                                                    <th>{{ __('adminstaticword.CouponDiscount') }}</th>
                                                @endif
                                                <th>{{ __('adminstaticword.PaidAmount') }}</th>
                                                <th>{{ __('adminstaticword.Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $show['order_id'] }} </td>
                                                <td>{{ $show->transaction->transaction_id ?? '' }}</td>
                                                <td>{{ $show->transaction->payment_method }}</td>

                                                <td>{{ $show->transaction->payment_charges ?? __('N/A') }}
                                                </td>

                                                <td>{{ $show['currency'] }}</td>

                                                @php
                                                    $contains = Illuminate\Support\Str::contains($show->currency_icon, 'fa');
                                                @endphp

                                                @if ($show->coupon_discount != 0 || $show->coupon_discount != null)
                                                    <td>
                                                        @if ($contains)
                                                            <i class="fa {{ $show['currency_icon'] }}"></i>
                                                            {{ $show['coupon_discount'] }}
                                                        @else
                                                            {{ $show['currency_icon'] }} {{ $show['coupon_discount'] }}
                                                        @endif
                                                    </td>
                                                @endif

                                                <td>
                                                    @if ($contains)
                                                        <i
                                                            class="fa {{ $show['currency_icon'] }}"></i>{{ $show['paid_amount'] }}
                                                    @else
                                                        {{ $show['currency_icon'] }} {{ $show['paid_amount'] }}
                                                    @endif
                                                </td>
                                                <td>{{ __('Paid') }}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-footer mt-4">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="invoice-footer-btn pull-right m-4">
                                        <a href="" onclick="printDiv()" class="btn btn-primary-rgba py-1 font-16"><i
                                                class="feather icon-printer mr-2"></i>{{ __('Print') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->
        </div>
        <!-- End row -->
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>

    <script lang='javascript'>
        function printDiv() {
            var printContents = document.getElementById('printContent').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
