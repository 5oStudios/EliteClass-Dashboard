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
                <a href="{{ route('user.enroll.installment') }}" class="float-right btn btn-primary-rgba mr-2"><i
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
                            <div class="row m-1">
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
                                        <small>{{ __('adminstaticword.Date') }}:&nbsp;{{ date('jS F Y', strtotime($payInInstallment['created_at'])) }}</small>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-billing">
                            <div class="row">
                                <div class="col-sm-12 col-md-4 col-lg-4 ml-3">
                                    <div class="invoice-address">
                                        @if ($payInInstallment->course_id != null)
                                            <h6>{{ $payInInstallment->user['fname'] }}
                                                {{ $payInInstallment->user['lname'] }}</h6>
                                            <ul class="list-unstyled">
                                                <li><b>{{ __('Mobile Number') }}:</b>
                                                    {{ $payInInstallment->user['mobile'] }}</li>
                                                <li><b>{{ __('Email') }}:</b> {{ $payInInstallment->user['email'] }}
                                                </li>
                                            </ul>
                                        @elseif($payInInstallment->meeting != null)
                                            <h6>{{ $payInInstallment->user['fname'] }}
                                                {{ $payInInstallment->user['lname'] }}</h6>
                                            <ul class="list-unstyled">
                                                <li><b>{{ __('Mobile Number') }}:</b>
                                                    {{ $payInInstallment->user['mobile'] }}</li>
                                                <li><b>{{ __('Email') }}:</b> {{ $payInInstallment->user['email'] }}
                                                </li>
                                            </ul>
                                        @else
                                            <h6>{{ $payInInstallment->user['fname'] }}
                                                {{ $payInInstallment->user['lname'] }}</h6>
                                            <ul class="list-unstyled">
                                                <li><b>{{ __('Mobile Number') }}:</b>
                                                    {{ $payInInstallment->user['mobile'] }}</li>
                                                <li><b>{{ __('Email') }}:</b> {{ $payInInstallment->user['email'] }}
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4 col-lg-4 ml-3">
                                    <b>{{ __('adminstaticword.Enrolled') }}:</b>
                                    {{ date('jS F Y', strtotime($payInInstallment['created_at'])) }}<br>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12 ml-3">

                                    @if ($payInInstallment->course_id)
                                        <b>{{ __('adminstaticword.Course') }}:</b>
                                        {{ $payInInstallment->courses->_title() }}
                                    @elseif($payInInstallment->bundle_id)
                                        <b>{{ __('adminstaticword.BundleName') }}:</b>
                                        {{ $payInInstallment->bundle->_title() }}
                                    @elseif($payInInstallment->meeting_id)
                                        <b>{{ __('Live Streaming') }}:</b> {{ $payInInstallment->meeting->_title() }}
                                    @elseif($payInInstallment->offline_session_id)
                                        <b>{{ __('In-Person Session') }}:</b>
                                        {{ $payInInstallment->offlinesession->_title() }}
                                    @elseif($payInInstallment->chapter_id)
                                        <b>{{ __('Chapter') }}:</b> {{ $payInInstallment->chapter->_title() }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12 ml-3">
                                    @if ($payInInstallment->bundle_id)
                                        <b>{{ __('adminstaticword.Courses') }}: </b>
                                        @foreach ($bundleOrder->course_id as $bundle_course)
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
                                    {{ date('jS F Y', strtotime($payInInstallment['enroll_start'])) }}<br>
                                    <b>
                                        @if ($payInInstallment->enroll_expire != null)
                                            {{ __('adminstaticword.EndDate') }}:
                                    </b> {{ date('jS F Y', strtotime($payInInstallment['enroll_expire'])) }}<br>
                                    @endif
                                </div>
                            </div><br>
                        </div>
                        <div class="invoice-summary">
                            <div class="table-responsive ml-2">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>{{ __('adminstaticword.Installment') }}</th>
                                            <th>{{ __('adminstaticword.Amount') }}</th>
                                            <th>{{ __('adminstaticword.DueDate') }}</th>
                                            <th>{{ __('adminstaticword.Status') }}</th>
                                            <th>{{ __('adminstaticword.TransactionId') }}</th>
                                            <th>{{ __('adminstaticword.PaymentMethod') }}</th>
                                            <th>{{ __('adminstaticword.PaymentGatewayServiceCharges') }}</th>
                                            <th>{{ __('adminstaticword.PaymentDate') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payInInstallment->payment_plan->whereNotNull('status') as $key => $insta)
                                            <tr>
                                                @php
                                                    $contains = Illuminate\Support\Str::contains($payInInstallment->currency_icon, 'fa');
                                                @endphp

                                                <td> {{ $key + 1 }}</td>

                                                <td>
                                                    @if ($contains)
                                                        <i
                                                            class="fa {{ $payInInstallment['currency_icon'] }}"></i>{{ $insta['amount'] }}
                                                    @else
                                                        {{ $payInInstallment['currency_icon'] }}
                                                        {{ $insta['amount'] }}
                                                    @endif
                                                </td>

                                                <td>{{ $insta['due_date'] }}</td>

                                                <td>{{ $insta['status'] ?? __('N/A') }}</td>

                                                <td>{{ $insta['wallet_trans_id'] ? $insta->order_installment->transaction_id : __('N/A') }}
                                                </td>

                                                <td>{{ $insta['wallet_trans_id'] ? $insta->order_installment->payment_method : __('N/A') }}
                                                </td>

                                                <td>{{ $insta->order_installment->payment_charges ?? __('N/A') }}
                                                </td>

                                                <td>{{ $insta['payment_date'] ?? __('N/A') }}</td>
                                            </tr>
                                        @endforeach

                                        @foreach ($payInInstallment->payment_plan->whereNull('status') as $key => $insta)
                                            <tr>
                                                @php
                                                    $contains = Illuminate\Support\Str::contains($payInInstallment->currency_icon, 'fa');
                                                @endphp

                                                <td> {{ $key + 1 }}</td>

                                                <td>
                                                    @if ($contains)
                                                        <i
                                                            class="fa {{ $payInInstallment['currency_icon'] }}"></i>{{ $insta['amount'] }}
                                                    @else
                                                        {{ $payInInstallment['currency_icon'] }}
                                                        {{ $insta['amount'] }}
                                                    @endif
                                                </td>

                                                <td>{{ $insta['due_date'] }}</td>

                                                <td>{{ $insta['status'] ?? __('Not Paid') }}</td>

                                                <td>{{ __('N/A') }}</td>
                                                <td>{{ __('N/A') }}</td>

                                                <td>{{ __('N/A') }}</td>

                                                <td>{{ $insta['payment_date'] ?? __('N/A') }}</td>
                                            </tr>
                                        @endforeach
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
        var printContents = document.getElementById("printContent").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        //  document.body.innerHTML = originalContents;
    }
</script>
@endsection
