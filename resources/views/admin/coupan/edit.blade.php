@extends('admin.layouts.master')
@section('title', 'Edit Coupon')

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __(' Edit Coupon') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <a href="{{ url('coupon') }}" class="float-right btn btn-primary-rgba mr-2"><i
                        class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
            </div>
        @endslot
    @endcomponent

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
                        <h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('Coupon') }}</h5>
                    </div>
                    <div class="card-body ml-2">
                        <form action="{{ route('coupon.update', $coupon->id) }}" method="POST">
                            @csrf
                            {{ method_field('PUT') }}

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.CouponCode') }}: <span
                                                class="text-danger">*</span></label>
                                        <input value="{{ $coupon->code }}" type="text" class="form-control"
                                            name="code" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-dark">{{ __('Coupon Type') }}: <span
                                                class="text-danger">*</span><small class="text-muted"><i
                                                    class="fa fa-question-circle"></i>
                                                {{ __('readonly') }} </small></label>
                                        <select id="coupon_type" class="form-control select2" disabled>
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            <option {{ $coupon->coupon_type === 'general' ? 'selected' : '' }}
                                                value="general">{{ __('General') }}</option>
                                            <option {{ $coupon->coupon_type === 'item' ? 'selected' : '' }} value="item">
                                                {{ __('Item') }}</option>
                                        </select>
                                        <input type="hidden" name="coupon_type" value="{{ $coupon->coupon_type }}">
                                    </div>

                                    <div style="{{ $coupon->coupon_type === 'general' ? '' : 'display: none' }}"
                                        id="minAmountbox" class="form-group">
                                        <label>{{ __('adminstaticword.MinAmount') }}: </label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="{{ $currency->icon }}"></i></span>
                                            <input id="minAmount" type="number" min="1"
                                                value="{{ $coupon->minamount }}" step="0.001" class="form-control"
                                                name="minamount" @if ($coupon->minamount) required @endif>
                                        </div>
                                        <small class="text-muted">
                                            {{ __('If Min amount is set, the coupon will apply only which price is greater than min amount.') }}</small>
                                    </div>

                                    <div style="{{ $coupon->link_by != null ? '' : 'display: none' }}" id="linkbox"
                                        class="form-group">
                                        <label>{{ __('adminstaticword.Linkedto') }}: <span class="text-danger">*</span>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('readonly') }} </small></label>

                                        <select required id="link_by" class="form-control select2" disabled>
                                            <option {{ $coupon->link_by === 'course' ? 'selected' : '' }} value="course">
                                                {{ __('adminstaticword.LinktoCourse') }}</option>
                                            <option {{ $coupon->link_by === 'bundle' ? 'selected' : '' }} value="bundle">
                                                {{ __('adminstaticword.LinktoBundle') }}</option>
                                            <option {{ $coupon->link_by === 'meeting' ? 'selected' : '' }} value="meeting">
                                                {{ __('adminstaticword.LinktoStreaming') }}</option>
                                            <option {{ $coupon->link_by === 'session' ? 'selected' : '' }} value="session">
                                                {{ __('adminstaticword.LinktoSession') }}</option>
                                        </select>
                                        <input type="hidden" name="link_by" value="{{ $coupon->link_by }}">

                                    </div>

                                    <div style="{{ $coupon->link_by === 'course' ? '' : 'display: none' }}" id="coursebox"
                                        class="form-group">
                                        <label>{{ __('adminstaticword.SelectCourse') }}: <span class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <select id="course_id" name="course_id" class="form-control select2">
                                            <option value="none" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            @foreach ($courses as $product)
                                                @if ($product->type == 1)
                                                    <option {{ $coupon->course_id == $product->id ? 'selected' : '' }}
                                                        value="{{ $product->id }}">{{ $product['title'] }} -
                                                        {{ $product->discount_price }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="{{ $coupon->link_by === 'bundle' ? '' : 'display: none' }}" id="bundlebox"
                                        class="form-group">
                                        <label>{{ __('adminstaticword.SelectBundle') }}: <span class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <select id="bundle_id" name="bundle_id" class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            @foreach ($bundles as $product)
                                                @if ($product->type == 1)
                                                    <option {{ $coupon->bundle_id == $product->id ? 'selected' : '' }}
                                                        value="{{ $product->id }}">{{ $product['title'] }}
                                                        - {{ $product->discount_price }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="{{ $coupon->link_by === 'meeting' ? '' : 'display: none' }}"
                                        id="meetingbox" class="form-group">
                                        <label>{{ __('adminstaticword.SelectMeeting') }}: <span
                                                class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <select id="meeting_id" name="meeting_id" class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            @foreach ($meetings as $product)
                                                <option {{ $coupon->meeting_id == $product->id ? 'selected' : '' }}
                                                    value="{{ $product->id }}">{{ $product['title'] }}
                                                    - {{ $product->discount_price }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="{{ $coupon->link_by === 'session' ? '' : 'display: none' }}"
                                        id="sessionbox" class="form-group">
                                        <label>{{ __('adminstaticword.SelectSession') }}: <span
                                                class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <select id="offline_session_id" name="offline_session_id"
                                            class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            @foreach ($sessions as $product)
                                                <option {{ $coupon->offline_session_id == $product->id ? 'selected' : '' }}
                                                    value="{{ $product->id }}">{{ $product['title'] }}
                                                    - {{ $product->discount_price }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="{{ $coupon->link_by === 'course' || $coupon->link_by === 'bundle' ? '' : 'display: none' }}"
                                        id="paymentbox" class="form-group">
                                        <label class="text-dark">{{ __('Payment Type') }}: <span
                                                class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <select id="payment_type" name="payment_type" class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>
                                            <option {{ $coupon->payment_type === 'full' ? 'selected' : '' }}
                                                value="full">{{ __('Full Payment') }}</option>
                                            <option {{ $coupon->payment_type === 'installment' ? 'selected' : '' }}
                                                value="installment">{{ __('Installment') }}</option>
                                        </select>
                                    </div>

                                    <div id="fullPaymentbox" class="form-group" style="display: none;">
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

                                    @isset($installment)
                                        <div style="{{ isset($installment) ? '' : 'display: none' }}" id="installmentbox"
                                            class="form-group">
                                            <label class="text-dark">{{ __('Installment Number') }}: <span
                                                    class="text-danger">*</span> </label>
                                            <br>
                                            <select id="installment_no" name="installment_number"
                                                class="form-control select2">
                                                <option value="" selected disabled hidden>
                                                    {{ __('adminstaticword.SelectanOption') }}
                                                </option>
                                                <option {{ $installment->sort === '1' ? 'selected' : '' }} value="1">
                                                    {{ __('Installment 01') }}</option>
                                                <option {{ $installment->sort === '2' ? 'selected' : '' }} value="2">
                                                    {{ __('Installment 02') }}</option>
                                                <option {{ $installment->sort === '3' ? 'selected' : '' }} value="3">
                                                    {{ __('installment 03') }}</option>
                                            </select>
                                        </div>
                                    @endisset

                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.DiscountType') }}: <span
                                                class="text-danger">*</span></label>

                                        <select required name="distype" id="distype" class="form-control select2">
                                            <option {{ $coupon->distype === 'fix' ? 'selected' : '' }} value="fix">
                                                {{ __('adminstaticword.FixAmount') }}</option>
                                            <option {{ $coupon->distype === 'per' ? 'selected' : '' }} value="per">%
                                                {{ __('adminstaticword.Percentage') }}</option>
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Coupon Amount/Percentage') }}: <span
                                                class="text-danger">*</span></label>
                                        <input id="amount-percentage" type="number" value="{{ $coupon->amount }}"
                                            class="form-control" min="1" step="0.001" name="amount" required>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.MaxUsageLimit') }}: <span
                                                class="text-danger">*</span></label>
                                        <input value="{{ $coupon->maxusage }}" type="number" min="1"
                                            class="form-control" name="maxusage" required>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.ExpiryDate') }}: <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input value="{{ $coupon->expirydate }}" type="text"
                                                class="form-control datepicker" name="expirydate" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                            {{ __('Reset') }}</button>
                                        <button type="submit" class="btn btn-primary-rgba"><i
                                                class="fa fa-check-circle"></i>
                                            {{ __('Update') }}</button>
                                    </div>
                                    <div class="clear-both"></div>
                                </div>
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

            $('#payment_type').on('change', function() {

                $('#installmentbox').hide();
                $('#installment_no').val('').trigger('change.select2');
                $('#installment_no').attr('required', false);

                if ($(this).val() === 'installment') {
                    $('#installmentbox').show();
                    $('#installment_no').attr('required', true);
                }
            });

            $('#course_id, #bundle_id').on('change', function() {

                let type = "{{ $coupon->link_by }}";

                $('#installmentbox').hide();
                $('#fullPaymentbox').hide();
                $('#payment_type').val('').trigger('change.select2');
                $('#full_payment').val('').trigger('change.select2');
                $('#installment_no').val('').trigger('change.select2');
                $('#payment_type').attr('required', false);
                $('#full_payment').attr('required', false);
                $('#installment_no').attr('required', false);

                let id = $(this).val();

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

            $(function() {
                $("#expirydate").datepicker({
                    dateFormat: 'yy-m-d',
                    nimDate: new Date()
                });
            });
        })(jQuery);
    </script>

@endsection
