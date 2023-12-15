@extends('admin.layouts.master')
@section('title', __('View Installments'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Installments') }}
        @endslot
        @slot('menu1')
            {{ __('Installments') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ $show->user->test_user == '1' ? route('testuser.enrollments', $show->user_id) : route('order.enrollments') }}"
                        class="float-right btn btn-primary-rgba mr-2"><i
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

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="invoice">
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
                                            <b>{{ __('Live Streaming') }}:</b> {{ $show->meeting->_title() }}
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

                                                <th>{{ __('adminstaticword.Installment') }}</th>
                                                <th>{{ __('adminstaticword.Amount') }}</th>
                                                <th>{{ __('adminstaticword.DueDate') }}</th>
                                                <th>{{ __('adminstaticword.Status') }}</th>
                                                <th>{{ __('adminstaticword.TransactionId') }}</th>
                                                <th>{{ __('adminstaticword.PaymentMethod') }}</th>
                                                <th>{{ __('adminstaticword.PaidAmount') }}</th>
                                                <th>{{ __('adminstaticword.CouponDiscount') }}</th>
                                                <th>{{ __('adminstaticword.PaymentDate') }}</th>
                                                <th>{{ __('adminstaticword.Action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($paidInstallments as $key => $insta)
                                                <tr>
                                                    @php
                                                        $contains = Illuminate\Support\Str::contains($show->currency_icon, 'fa');
                                                    @endphp

                                                    <td> {{ $key + 1 }}</td>

                                                    <td>
                                                        @if ($contains)
                                                            <i
                                                                class="fa {{ $show['currency_icon'] }}"></i>{{ $insta['amount'] }}
                                                        @else
                                                            {{ $show['currency_icon'] }} {{ $insta['amount'] }}
                                                        @endif
                                                    </td>

                                                    <td>{{ $insta['due_date'] }}</td>

                                                    <td>{{ $insta['status'] ?? '' }}</td>

                                                    <td>{{ $insta['wallet_trans_id'] ? $insta->order_installment->transaction_id : '' }}
                                                    </td>

                                                    <td>{{ $insta['wallet_trans_id'] ? $insta->order_installment->payment_method : '' }}
                                                    </td>
                                                    <td>{{ $insta->amount }}
                                                    <td>{{ $insta['order_installment_id'] ? $insta->paidInstallment->coupon_discount : __('N/A') }}

                                                    <td>{{ $insta['payment_date'] ?? __('N/A') }}</td>

                                                </tr>
                                            @endforeach

                                            @foreach ($pendingInstallments as $key => $insta)
                                                <tr>
                                                    @php
                                                        $contains = Illuminate\Support\Str::contains($show->currency_icon, 'fa');
                                                    @endphp

                                                    <td> {{ $key + 1 }}</td>

                                                    <td>
                                                        @if ($contains)
                                                            <i
                                                                class="fa {{ $show['currency_icon'] }}"></i>{{ $insta['amount'] }}
                                                        @else
                                                            {{ $show['currency_icon'] }} {{ $insta['amount'] }}
                                                        @endif
                                                    </td>

                                                    <td>{{ $insta['due_date'] }}</td>

                                                    <td>{{ $insta['status'] ?? __('Not Paid') }}</td>

                                                    <td>{{ __('N/A') }}</td>
                                                    <td>{{ __('N/A') }}</td>
                                                    <td>{{ __('N/A') }}</td>
                                                    <td>{{ __('N/A') }}</td>

                                                    <td>{{ $insta['payment_date'] ?? __('N/A') }}</td>

                                                    <td>
                                                        <a class="dropdown-item btn btn-primary text-white"
                                                            data-toggle="modal"
                                                            data-target="#pay_installment{{ $insta->id }}">{{ __('Want to Pay') }}</a>

                                                        <div id="pay_installment{{ $insta->id }}"
                                                            class="delete-modal modal fade" role="dialog">
                                                            <div class="modal-dialog modal-sm">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"> &times; </button>
                                                                        <div class="delete-icon"></div>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <h4 class="modal-heading">
                                                                            {{ __('Are You Sure ?') }}</h4>
                                                                        <p>{{ __('This process') }}
                                                                            <b>{{ __('can not be undo.') }}</b>
                                                                            {{ __('Do you really want to pay this installment?') }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <form method="post"
                                                                            action="{{ route('manual.pay.installment', $insta->id) }}">
                                                                            @csrf
                                                                            @method('POST')

                                                                            <button type="reset"
                                                                                class="btn btn-primary translate-y-3"
                                                                                data-dismiss="modal">{{ __('No') }}</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">{{ __('Yes') }}</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
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
@endsection
