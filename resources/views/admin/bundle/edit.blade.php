@extends('admin.layouts.master')
@section('title', 'Edit Package')

@section('maincontent')

    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Package') }}
        @endslot

        @slot('menu1')
            {{ __('Package') }}
        @endslot

        @slot('menu2')
            {{ __(' Edit Package') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <a href="{{ url('bundle') }}" class="float-right btn btn-primary-rgba mr-2"><i
                        class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('Package') }}</h5>
                            </div>
                            <!-- language start -->
                            @php
                                $languages = App\Language::all();
                            @endphp
                            <div class="col-md-6">
                                <li class="list-inline-item pull-right">
                                    <div class="languagebar">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" role="button" id="languagelink"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span
                                                    class="live-icon"> {{ __('Selected Language') }}
                                                    ({{ Session::has('changed_language') ? Session::get('changed_language') : '' }})</span></a>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languagelink">
                                                @if (isset($languages) && count($languages) > 0)
                                                    @foreach ($languages as $language)
                                                        <a class="dropdown-item"
                                                            href="{{ route('languageSwitch', $language->local) }}">
                                                            <i class="feather icon-globe"></i>
                                                            {{ $language->name }} ({{ $language->local }})</a>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                            <!-- language end -->
                        </div>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" action="{{ route('bundle.update', $cor->id) }}" method="post"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.BundleName') }}:<sup
                                                class="text-danger">*</sup></label>
                                        <input type="text" class="form-control" name="title" id="exampleInputTitle"
                                            value="{{ $cor->title }}" required>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.Instructor') }}:<sup
                                            class="text-danger">*</sup></label>
                                    <select name="user_id" class="form-control js-example-basic-single" required>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($users as $user)
                                            <option {{ $cor->user_id == $user->id ? 'selected' : '' }}
                                                value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputDetails">{{ __('adminstaticword.Detail') }}:<sup
                                                class="text-danger">*</sup></label>
                                        <textarea id="detail" name="detail" rows="3" class="form-control">{!! $cor->detail !!}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.SelectCourse') }}: <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="course_id[]" multiple="multiple"
                                            size="5" row="5"
                                            placeholder="{{ __('adminstaticword.SelectCourse') }}" required>

                                            @foreach ($courses as $cat)
                                                @if ($cat->status == 1)
                                                    <option value="{{ $cat->id }}"
                                                        {{ in_array($cat->id, $cor['course_id'] ?: []) ? 'selected' : '' }}>
                                                        {{ $cat->title }}
                                                    </option>
                                                @endif
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>
                                        {{ __('Start Date') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input type="text" required class="form-control datepicker" name="start_date"
                                            value="{{ $cor->start_date }}" placeholder="yyyy-mm-dd"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>
                                        {{ __('End Date') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input type="text" required class="form-control datepicker" name="end_date"
                                            value="{{ $cor->end_date }}" placeholder="yyyy-mm-dd"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                                class="redstar">*</sup></label>
                                        <input type="number" class="form-control" name="price" min="0"
                                            step="0.001" required
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                            value="{{ $cor->price }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                <label for="discount_type">{{ __('discount_type') }}</label>
                                        <select name="discount_type" id="discount_type" class="form-control js-example-basic-single col-md-7 col-xs-12 mb-2">
                                            <option value="none" disabled {{ ($cor->discount_type ?? null) == 'none' ? 'selected' : '' }}>
                                                {{ __('frontstaticword.SelectanOption') }}
                                            </option>
                                            <option value="percentage" {{ ($cor->discount_type ?? null) == 'percentage' ? 'selected' : '' }}>
                                                {{ __('percentage') }}
                                            </option>
                                            <option value="fixed" {{ ($cor->discount_type ?? null) == 'fixed' ? 'selected' : '' }}>
                                                {{ __('fixed') }}
                                            </option>
                                        </select>

                                </div>
                                        <br>
                                        <div class="col-md-6">

                                        <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('Discounted price Zero(0) consider as no discount') }}
                                            </small>
                                        </label>

                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0" required class="form-control" name="discount_price" id="offerPrice"
                                                placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                value="{{ $cor->discount_price ?? 0 }}" />

                                            <div class="input-group-append">
                                                <span class="input-group-text" id="prefix">
                                                    @if(old('discount_type') == 'percentage')
                                                        %
                                                    @elseif(old('discount_type') == 'fixed')
                                                        KWD
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" id="installment-box">
                                    <div class="form-group">
                                        <label for="exampleInputDetails">{{ __('adminstaticword.Installment') }}:</label>
                                        <input class="custom_toggle" type="checkbox" value="1" id="installments"
                                            name="installment" {{ $cor->installment == 1 ? 'checked' : '' }} />
                                        <br>
                                    </div>
                                    <div style="{{ $cor->installment == 0 ? 'display:none' : '' }}"
                                        id="installments-pricebox">
                                        <label for="exampleInputSlug">{{ __('adminstaticword.InstallmentsTotalPrice') }}:
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('readonly') }} </small></label>
                                        <input class="form-control" id="installments-price"
                                            value="{{ $installments->sum('amount') != 0 ? $installments->sum('amount') : 'Installments did not define yet' }}"
                                            readonly><br>

                                        @if ($orderExists)
                                            <label
                                                for="exampleInputDetails">{{ __('adminstaticword.TotalInstallments') }}:<sup
                                                    class="redstar">*</sup><small class="text-muted"><i
                                                        class="fa fa-question-circle"></i>
                                                    {{ __("Can't modify installments due to existing orders.") }}
                                                </small></label>
                                            <input class="form-control" id="total_installments"
                                                value="{{ $cor->total_installments }}" readonly>
                                        @else
                                            <label
                                                for="exampleInputDetails">{{ __('adminstaticword.TotalInstallments') }}:<sup
                                                    class="redstar">*</sup></label>
                                            <select class="form-control select2" id="total_installments"
                                                name="total_installments">
                                                <option value="" selected disabled hidden>
                                                    {{ __('Select an option') }}</option>
                                                <option value="2"
                                                    {{ $cor->total_installments == '2' ? 'selected' : '' }}>2
                                                </option>
                                                <option value="3"
                                                    {{ $cor->total_installments == '3' ? 'selected' : '' }}>3
                                                </option>
                                                <option value="4"
                                                    {{ $cor->total_installments == '4' ? 'selected' : '' }}>4
                                                </option>
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        @if (Auth::User()->role == 'admin')
                                            <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label>
                                            <input id="status" type="checkbox" class="custom_toggle" name="status"
                                                {{ $cor->status == 1 ? 'checked' : '' }} />
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">{{ __('adminstaticword.Duration') }}: </label>
                                        <input id="duration1" type="checkbox" class="custom_toggle" value="m"
                                            name="duration_type" {{ $cor->duration_type == 'm' ? 'checked' : '' }} /><br>
                                        <small class="text-info"><i class="fa fa-question-circle"></i>
                                            {{ __('If enabled duration can be in months') }}.</small><br>
                                        <small class="text-info">
                                            {{ __('when Disabled duration can be in days') }}.</small>
                                        <div>
                                            <label
                                                for="exampleInputSlug">{{ __('adminstaticword.BundleExpireDuration') }}</label>
                                            <input min="1" class="form-control" name="duration" type="number"
                                                id="duration2" value="{{ $cor->duration }}"
                                                placeholder="{{ __('adminstaticword.Enter') }} Duration">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="cbToggleSubscription">{{ __('adminstaticword.Subscription') }}:</label>
                                        <input id="subscription1" type="checkbox" name="is_subscription_enabled"
                                            class="custom_toggle"
                                            {{ $cor->is_subscription_enabled ? 'checked' : '' }} /><br />
                                        <small class="text-muted"><i
                                                class="fa fa-question-circle"></i>{{ __('Subscription bundle works with stripe payment only') }}
                                            .</small><br>
                                        <small class="text-muted">{{ __('Enable it only when you have setup stripe') }}
                                            .</small>
                                        <br>
                                        <div id="subscription"
                                            style="{{ $cor['is_subscription_enabled'] ? '' : 'display:none' }}">

                                            @php
                                                $selectedPeriod = $cor->billing_interval;
                                            @endphp
                                            <label>{{ __('adminstaticword.BillingPeriod') }}</label>
                                            <select class="form-control" name="billing_interval">
                                                <option value="day" {{ $selectedPeriod == 'day' ? 'selected' : '' }}>
                                                    {{ __('Daily') }}</option>
                                                <option value="week" {{ $selectedPeriod == 'week' ? 'selected' : '' }}>
                                                    {{ __('Weekly') }}</option>
                                                <option value="month" {{ $selectedPeriod == 'month' ? 'selected' : '' }}>
                                                    {{ __('Monthly') }}</option>
                                                <option value="year" {{ $selectedPeriod == 'year' ? 'selected' : '' }}>
                                                    {{ __('Yearly') }}</option>
                                            </select>

                                        </div>
                                    </div>

                                </div>

                            </div> --}}

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.Image') }}:<sup class="redstar">*</sup></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image"
                                                name="preview_image" aria-describedby="inputGroupFileAddon01"
                                                value="{{ $cor->preview_image }}">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ $cor->preview_image ?? __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    @if ($cor['preview_image'] !== null && $cor['preview_image'] !== '')
                                        <img src="{{ url('/images/bundle/' . $cor->preview_image) }}" height="70px;"
                                            width="70px;" />
                                    @else
                                        <img src="{{ Avatar::create($cor->title)->toBase64() }}" alt="course"
                                            class="img-fluid">
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    {{ __('Reset') }}</button>
                                <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                    {{ __('Update') }}</button>
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
            $(function() {
                $('.js-example-basic-single').select2();
            });

            $(function() {
                $('#cb1').change(function() {
                    $('#f').val(+$(this).prop('checked'))
                })
            })

            $(function() {
                $('#cb3').change(function() {
                    $('#test').val(+$(this).prop('checked'))
                })
            })

            $(function() {
                $('#murl').change(function() {
                    if ($('#murl').val() == 'yes') {
                        $('#doab').show();
                    } else {
                        $('#doab').hide();
                    }
                });

            });
            $(function() {
                $('#murll').change(function() {
                    if ($('#murll').val() == 'yes') {
                        $('#doabb').show();
                    } else {
                        $('#doab').hide();
                    }
                });

            });

            $('#preview').on('change', function() {
                if ($('#preview').is(':checked')) {
                    $('#document1').show('fast');
                    $('#document2').hide('fast');
                } else {
                    $('#document2').show('fast');
                    $('#document1').hide('fast');
                }
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            $(function() {
                $('#subscription1').change(function() {
                    if ($('#subscription1').is(':checked')) {
                        $('#subscription').show('fast');
                    } else {
                        $('#subscription').hide('fast');
                    }
                });
            });
        })(jQuery);
    </script>

    <script>
        $('#cb111').on('change', function() {
            if ($('#cb111').is(':checked')) {
                $('#doabox').addClass('d-block').removeClass('d-none');
                $('#priceMain').attr('required', false);
            } else {
                $('#doabox').addClass('d-none').removeClass('d-block');
                $('#priceMain').val('');
                $('#discount_price').val('');
                $('#priceMain').attr('required', false);
            }
        });

        $('#installments').on('change', function() {
            if ($('#installments').is(':checked')) {
                $('#installments-pricebox').show('fast');
                $('#total_installments').attr('required', true);
            } else {
                $('#installments-pricebox').hide('fast');
                $('#total_installments').attr('required', false);
            }
        });

        $('#duration1').on('change', function() {
            if ($('#duration1').is(':checked')) {
                $('#duration').addClass('d-block').removeClass('d-none');
                $('#duration2').prop('required', 'required');
            } else {
                $('#duration').addClass('d-none').removeClass('d-block');
                $('#duration2').removeAttr('required');
            }
        });
    </script>

@endsection
