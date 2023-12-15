@extends('admin.layouts.master')
@section('title', 'Payment Charges - Admin')

@section('maincontent')
    @component('components.breadcumb', ['fourthactive' => 'active'])
        @slot('heading')
            {{ __('Payment Gateway Charges') }}
        @endslot
        @slot('menu1')
            {{ __('Payment Gateway Charges') }}
        @endslot
    @endcomponent
    <div class="contentbar">
        <div class="row">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                    @endforeach
                </div>
            @endif

            <div class="col-md-3">
                <div class="card p-3 mb-5">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-visa-master-tab" data-toggle="pill"
                            href="#v-pills-visa-master" role="tab" aria-controls="v-pills-visa-master"
                            aria-selected="true"><i
                                class="feather icon-credit-card mr-2"></i>{{ __('VISA/MASTER Payment') }}</a>
                        <a class="nav-link" id="v-pills-knet-tab" data-toggle="pill" href="#v-pills-knet" role="tab"
                            aria-controls="v-pills-knet" aria-selected="false"><i
                                class="feather icon-credit-card mr-2"></i>{{ __('KNET Payment') }}</a>

                    </div>
                </div>
            </div>
            <div class="col-md-9 mb-3">
                <div class="card p-3">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-visa-master" role="tabpanel"
                            aria-labelledby="v-pills-visa-master-tab">
                            <form action="{{ route('payment-charges.update', $visa_payment->id) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}

                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label class="text-dark"
                                            for="visa-master">{{ __('adminstaticword.VISA/MASTERPAYMENT') }}</label><br>
                                        <input type="hidden" name="payment_method" value="VISA/MASTER">
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-dark"
                                                for="Payment_charges_type">{{ __('adminstaticword.PaymentChargesType') }}
                                                <span class="text-danger">*</span></label>
                                            <select required name="type" id="type" class="form-control select2">
                                                <option value="fixed"
                                                    {{ $visa_payment->type == 'fixed' ? 'selected' : '' }}>
                                                    {{ __('adminstaticword.FixedAmount') }}</option>
                                                <option value="percentage"
                                                    {{ $visa_payment->type == 'percentage' ? 'selected' : '' }}>%
                                                    {{ __('adminstaticword.Percentage') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-dark"
                                                for="payment_charges">{{ __('adminstaticword.Charges') }} <span
                                                    class="text-danger">*</span></label>
                                            <input value="{{ $visa_payment->charges }}" step="0.001" autofocus
                                                name="charges" type="text" class="form-control"
                                                placeholder="Enter payment charges" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn btn-primary-rgba"><i
                                                class="fa fa-check-circle"></i>
                                            {{ __('Update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-knet" role="tabpanel" aria-labelledby="v-pills-knet-tab">
                            <form action="{{ route('payment-charges.update', $knet_payment->id) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('PUT') }}

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-dark"
                                                for="knet">{{ __('adminstaticword.KNETPAYMENT') }}</label><br>
                                            <input type="hidden" name="payment_method" value="KNET">
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="text-dark"
                                                    for="Payment_charges_type">{{ __('adminstaticword.PaymentChargesType') }}
                                                    <span class="text-danger">*</span></label>
                                                <select required name="type" id="type" class="form-control select2">
                                                    <option value="fixed"
                                                        {{ $knet_payment->type == 'fixed' ? 'selected' : '' }}>
                                                        {{ __('adminstaticword.FixedAmount') }}</option>
                                                    <option value="percentage"
                                                        {{ $knet_payment->type == 'percentage' ? 'selected' : '' }}>%
                                                        {{ __('adminstaticword.Percentage') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="text-dark"
                                                    for="payment_charges">{{ __('adminstaticword.Charges') }} <span
                                                        class="text-danger">*</span></label>
                                                <input value="{{ $knet_payment->charges }}" step="0.001" autofocus
                                                    name="charges" type="text" class="form-control"
                                                    placeholder="Enter payment charges" />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary-rgba"><i
                                                    class="fa fa-check-circle"></i>
                                                {{ __('Update') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        // (function($) {
        //     "use strict";

        //     $(function() {

        //     });

        // })(jQuery);
    </script>
