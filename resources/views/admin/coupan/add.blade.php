@extends('admin.layouts.master')
@section('title', __('Create a new Coupon'))

@section('breadcum')
    <div class="breadcrumbbar">
        <div class="row align-items-center">
            <div class="col-md-7 col-lg-7">
                <h4 class="page-title">{{ __('Coupons') }}</h4>
                <div class="breadcrumb-list">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/admins') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ __('Add Coupon') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a title="Back" href="{{ url('coupon') }}" class="btn btn-primary-rgba"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('maincontent')
    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if (Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert alert-danger') }}">{{ Session::get('message') }}
                    </p>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-
                                    label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-box">{{ __('adminstaticword.Add') }} {{ __('Coupon') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('coupon.store') }}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('adminstaticword.CouponCode') }}: <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" class="form-control" value={{ $coupon_code }}
                                        name="code">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('Coupon Type') }}: <span
                                            class="text-danger">*</span></label>
                                    <select required name="coupon_type" id="coupon_type" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="general">{{ __('General') }}</option>
                                        <option value="item"> {{ __('Item') }}</option>
                                    </select>
                                </div>

                                <div id="minAmountbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.MinAmount') }}: <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <span class="input-group-text" id="basic-addon2">{{ $currency->symbol }}</span>
                                        <input type="number" min="1" step="0.001" id="minAmount"
                                            class="form-control" name="minamount">
                                    </div>
                                    <small class="text-muted">
                                        {{ __('If Min amount is set, the coupon will apply only which price is greater than min amount.') }}</small>
                                </div>

                                <div id="linkbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.Linkedto') }}: <span
                                            class="text-danger">*</span></label>

                                    <select name="link_by" id="link_by" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="course">{{ __('adminstaticword.LinktoCourse') }}</option>
                                        <option value="bundle">{{ __('adminstaticword.LinktoBundle') }}</option>
                                        <option value="meeting">{{ __('adminstaticword.LinktoStreaming') }}
                                        </option>
                                        <option value="session">{{ __('adminstaticword.LinktoSession') }}</option>
                                    </select>
                                </div>

                                <div id="coursebox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.SelectCourse') }}: <span
                                            class="text-danger">*</span> </label>
                                    <br>
                                    <select id="course_id" name="course_id" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($courses as $product)
                                            <option value="{{ $product->id }}">{{ $product['title'] }}
                                                - {{ $product->discount_price }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="bundlebox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.SelectBundle') }}: <span
                                            class="text-danger">*</span> </label>
                                    <br>
                                    <select id="bundle_id" name="bundle_id" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($bundles as $product)
                                            <option value="{{ $product->id }}">{{ $product->title }}
                                                - {{ $product->discount_price }}
                                        @endforeach
                                    </select>
                                </div>
                                <div id="meetingbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.SelectMeeting') }}: <span
                                            class="text-danger">*</span> </label>
                                    <br>
                                    <select id="meeting_id" name="meeting_id" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($meetings as $product)
                                            <option value="{{ $product->id }}">{{ $product->meetingname }}
                                                - {{ $product->discount_price }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="sessionbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('adminstaticword.SelectSession') }}: <span
                                            class="text-danger">*</span> </label>
                                    <br>
                                    <select id="offline_session_id" name="offline_session_id"
                                        class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($sessions as $product)
                                            <option value="{{ $product->id }}">{{ $product->title }}
                                                - {{ $product->discount_price }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="paymentbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('Payment Type') }}: <span class="text-danger">*</span>
                                    </label>
                                    <br>
                                    <select id="payment_type" name="payment_type" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="full">{{ __('Full Payment') }}</option>
                                        <option value="installment">{{ __('Installment') }}</option>
                                    </select>
                                </div>

                                <div id="fullPaymentbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('Payment Type') }}: <span
                                            class="text-danger">*</span><small class="text-muted">
                                            {{ __('The selected course does not have any active installments') }}</small>
                                    </label>
                                    <br>
                                    <select id="full_payment" name="payment_type" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="full">{{ __('Full Payment') }}</option>
                                    </select>
                                </div>

                                <div id="installmentbox" class="col-md-6 form-group" style="display: none;">
                                    <label class="text-dark">{{ __('Installment Number') }}: <span
                                            class="text-danger">*</span> </label>
                                    <br>
                                    <select id="installment_no" name="installment_number" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="1">{{ __('Installment 01') }}</option>
                                        <option value="2">{{ __('Installment 02') }}</option>
                                        <option value="3">{{ __('installment 03') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('adminstaticword.DiscountType') }}: <span
                                            class="text-danger">*</span></label>
                                    <select required name="distype" id="distype" class="form-control select2">
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="fix"> {{ __('adminstaticword.FixAmount') }}</option>
                                        <option value="per">% {{ __('adminstaticword.Percentage') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('Coupon Amount/Percentage') }}: <span
                                            class="text-danger">*</span></label>
                                    <input id="amount-percentage" required type="number" min="1" step="0.001"
                                        type="text" class="form-control" name="amount">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('adminstaticword.ExpiryDate') }}: <span
                                            class="text-danger">*</span></label>

                                    <div class="input-group">
                                        <input type="text" id="default-date" required
                                            class="form-control default-datepicker" name="expirydate"
                                            placeholder="yyyy-mm-dd" aria-describedby="basic-addon2" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="text-dark">{{ __('Max Usage Limit') }}: <span
                                            class="text-danger">*</span></label>
                                    <input required type="number" min="1" class="form-control" name="maxusage">
                                </div>

                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="exampleInputDetails">{{ __('adminstaticword.CouponCodedisplayonfront') }}:</label>
                                        <input class="custom_toggle" type="checkbox" name="show_to_users" checked />
                                        <label class="tgl-btn" data-tg-off="No" data-tg-on="Yes" for="frees"></label>
                                        <small
                                            class="txt-desc">({{ __('If Choose Yes then Coupon Code shows to all users') }})
                                        </small>
                                    </div>
                                </div>
                                <br> --}}

                            </div>
                            <div class="row col-md-12">
                                <button type="reset" class="mr-2 btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    {{ __('Reset') }}</button>
                                <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                                <div class="clear-both"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        (function($) {
            "use strict";

            var type = '';

            $('#distype').on('change', function() {
                $("#amount-percentage").val('');
            });

            $('#amount-percentage').on('change', function() {
                let inputField = $(this);
                if ($("#distype").val() === 'per') {
                    $(this).attr({
                        max: 100,
                        min: 1,
                        step: 1
                    });
                    $(this).attr('title', 'Percentage number should be positive integer');
                    inputField.on('input', function() {
                        // Check the validity of the input field
                        if (!this.validity.valid) {
                            $(this).val(''); // Clear the input field
                        }
                    });
                }
            });

            $('#coupon_type').on('change', function() {

                $('#minAmount').attr('required', false);
                $('#link_by').attr('required', false);
                $('#course_id').attr('required', false);
                $('#bundle_id').attr('required', false);
                $('#meeting_id').attr('required', false);
                $('#offline_session_id').attr('required', false);
                $('#link_by').val('').trigger('change.select2');
                $('#course_id').val('').trigger('change.select2');
                $('#bundle_id').val('').trigger('change.select2');
                $('#meeting_id').val('').trigger('change.select2');
                $('#offline_session_id').val('').trigger('change.select2');
                $('#payment_type').val('').trigger('change.select2');
                $('#full_payment').val('').trigger('change.select2');
                $('#installment_no').val('').trigger('change.select2');
                $('#fullPaymentbox').hide();

                if ($(this).val() === 'item') {
                    $('#linkbox').show();
                    $('#minAmountbox').hide();
                    $('#link_by').attr('required', true);
                } else {
                    $('#minAmountbox').show();
                    $('#linkbox').hide();
                    $('#coursebox').hide();
                    $('#bundlebox').hide();
                    $('#meetingbox').hide();
                    $('#sessionbox').hide();
                    $('#paymentbox').hide();
                    $('#installmentbox').hide();
                    $('#minAmount').attr('required', true);
                }
            });

            $('#link_by').on('change', function() {
                $('#paymentbox').hide();
                $('#fullPaymentbox').hide();
                $('#minAmountbox').hide();
                $('#coursebox').hide();
                $('#bundlebox').hide();
                $('#meetingbox').hide();
                $('#sessionbox').hide();
                $('#course_id').val('').trigger('change.select2');
                $('#bundle_id').val('').trigger('change.select2');
                $('#meeting_id').val('').trigger('change.select2');
                $('#offline_session_id').val('').trigger('change.select2');
                $('#payment_type').val('').trigger('change.select2');
                $('#full_payment').val('').trigger('change.select2');
                $('#installment_no').val('').trigger('change.select2');
                $('#minAmount').attr('required', false);
                $('#course_id').attr('required', false);
                $('#bundle_id').attr('required', false);
                $('#meeting_id').attr('required', false);
                $('#offline_session_id').attr('required', false);
                $('#payment_type').attr('required', false);
                $('#full_payment').attr('required', false);

                let opt = $(this).val();

                if (opt === 'course') {
                    type = 'course';
                    $('#coursebox').show();
                    $('#paymentbox').show();
                    $('#course_id').attr('required', true);
                    $('#payment_type').attr('required', true);
                } else if (opt === 'bundle') {
                    type = 'bundle';
                    $('#bundlebox').show();
                    $('#paymentbox').show();
                    $('#bundle_id').attr('required', true);
                    $('#payment_type').attr('required', true);
                } else if (opt === 'meeting') {
                    $('#meetingbox').show();
                    $('#fullPaymentbox').show();
                    $('#meeting_id').attr('required', true);
                    $('#full_payment').attr('required', true);
                } else if (opt === 'session') {
                    $('#sessionbox').show();
                    $('#fullPaymentbox').show();
                    $('#offline_session_id').attr('required', true);
                    $('#full_payment').attr('required', true);
                } else {
                    $('#minAmountbox').show();
                    $('#minAmount').attr('required', true);

                }
            });

            $('#distype').on('change', function() {

            });

            $('#course_id, #bundle_id').on('change', function() {

                $('#installmentbox').hide();
                $('#fullPaymentbox').hide();
                $('#payment_type').val('').trigger('change.select2');
                $('#full_payment').val('').trigger('change.select2');
                $('#installment_no').val('').trigger('change.select2');
                $('#payment_type').attr('required', false);
                $('#full_payment').attr('required', false);
                $('#installment_no').attr('required', false);

                var id = $(this).val();

                if (id != '' && type != '') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('find.course') }}",
                        data: {
                            id: id,
                            type: type,
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.installment == '0') {
                                $('#paymentbox').hide();
                                $('#fullPaymentbox').show();
                                $('#full_payment').attr('required', true);

                            } else {
                                $('#paymentbox').show();
                                $('#fullPaymentbox').hide();
                                $('#payment_type').attr('required', true);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log('ERROR: ', XMLHttpRequest);
                        }
                    });
                }
            });

            $('#payment_type').on('change', function() {

                $('#installmentbox').hide();
                $('#installment_no').val('').trigger('change.select2');
                $('#installment_no').attr('required', false);

                if ($(this).val() === 'installment') {
                    $('#installmentbox').show();
                    $('#installment_no').attr('required', true);
                }
            });

            $(function() {
                $("#expirydate").datepicker({
                    dateFormat: 'yy-m-d',
                    minDate: new Date()
                });
            });
        })(jQuery);
    </script>
@endsection
