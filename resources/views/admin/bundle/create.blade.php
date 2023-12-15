@extends('admin.layouts.master')
@section('title', 'Create a new package')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Package') }}
        @endslot
        @slot('menu1')
            {{ __('Package') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ url('bundle') }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
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
                        <h5 class="card-box">{{ __('Add') }} {{ __('Package') }}</h5>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" action="{{ url('bundle/') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            {{-- <input type="hidden" class="form-control" name="user_id" id="exampleInputTitle"
                                value="{{ Auth::User()->id }}" required> --}}
                            <div class="row">
                                <div class=" col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.BundleName') }}: <sup
                                                class="text-danger">*</sup></label>
                                        <input type="title" class="form-control" name="title" id="exampleInputTitle"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('package name') }}"
                                            value="{{ old('title') }}" required>

                                    </div>
                                </div>

                                {{-- <div class="col-md-3">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}: <sup
                                            class="text-danger">*</sup></label>
                                    <select name="user_id" class="form-control js-example-basic-single" required>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class=" col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.SelectCourse') }}: <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="course_id[]" multiple="multiple"
                                            size="5" row="5"
                                            placeholder="{{ __('adminstaticword.SelectCourse') }}"
                                            value="{{ old('course_id[]') }}" required>

                                            @foreach ($courses as $cat)
                                                @if ($cat->status == 1)
                                                    <option value="{{ $cat->id }}">{{ $cat->title }}
                                                    </option>
                                                @endif
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Detail') }}: <sup
                                                class="text-danger">*</sup></label>
                                        <textarea id="detail" name="detail" rows="5" class="form-control"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Detail') }}" value="{{ old('detail') }}"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>
                                                {{ __('Start Date') }}:<sup class="redstar">*</sup>
                                            </label>

                                            <div class="input-group">
                                                <input type="text" required class="default-datepicker form-control"
                                                    name="start_date" placeholder="yyyy-mm-dd"
                                                    value="{{ old('start_date') }}" aria-describedby="basic-addon2">

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
                                                <input type="text" required class="default-datepicker form-control"
                                                    name="end_date" placeholder="yyyy-mm-dd" value="{{ old('end_date') }}"
                                                    aria-describedby="basic-addon2">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2"><i
                                                            class="feather icon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{-- <label for="exampleInputDetails">{{ __('adminstaticword.Paid') }}: <sup
                                                        class="redstar">*</sup></label>
                                                <input id="cb111" type="checkbox" class="custom_toggle"
                                                    name="type" required />
                                                <br> --}}
                                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                                        class="redstar">*</sup></label>
                                                <input type="number" class="form-control" name="price" min="0"
                                                    step="0.001" id="priceMain" required
                                                    oninput="javascript:discount_price.value = this.value;"
                                                    placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                                    value="{{ old('price') }}">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}:
                                                    <sup class="redstar">*</sup><small class="text-muted"><i
                                                            class="fa fa-question-circle"></i>
                                                        {{ __('Discounted price Zero(0) consider as free') }}
                                                    </small>
                                                </label>
                                                <input type="number" class="form-control" name="discount_price"
                                                    min="0" step="0.001" id="discount_price" required
                                                    placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                    value="{{ old('discount_price') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <br>


                                    <div class="row">
                                        <div class="col-md-3">
                                            <label
                                                for="exampleInputDetails">{{ __('adminstaticword.Installment') }}:</label>
                                            <input type="checkbox" class="custom_toggle" id="installments"
                                                name="installment" value="1" /><br><br>
                                            <div id="totalInstallments" style="display: none;">
                                                <label
                                                    for="exampleInputDetails">{{ __('adminstaticword.TotalInstallments') }}:<sup
                                                        class="redstar">*</sup></label>
                                                <select class="form-control select2" id="total_installments" name="total_installments">
                                                    <option value="" selected disabled hidden>
                                                        {{ __('Select an option') }}</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-3">
                                            <div class="form-group">
                                                @if (Auth::User()->role == 'admin')
                                                    <label
                                                        for="exampleInputDetails">{{ __('adminstaticword.Featured') }}:</label>
                                                    <input id="status_toggle" type="checkbox" name="featured"
                                                        class="custom_toggle" id="cb1" />
                                                @endif
                                            </div>
                                        </div> --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                @if (Auth::User()->role == 'admin')
                                                    <label
                                                        for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label>
                                                    <input type="checkbox" name="status" class="custom_toggle"
                                                        id="cb3" value="{{ old('status') }}" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    {{-- <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ __('adminstaticword.Duration') }}: </label>
                                            <input id="duration1" type="checkbox" class="custom_toggle" value="m"
                                                name="duration_type" checked />
                                            <label class="tgl-btn" data-tg-off="days" data-tg-on="month"
                                                for="duration_type"></label>
                                            <br>
                                            <small class="text-info"><i class="fa fa-question-circle"></i>
                                                {{ __('If enabled duration can be in months') }}.</small><br>
                                            <small class="text-info">
                                                {{ __('when Disabled duration can be in days') }}.</small>
                                            <div class="form-group" id="duration">
                                                <label
                                                    for="exampleInputSlug">{{ __('adminstaticword.BundleExpireDuration') }}
                                                </label>
                                                <input min="1" class="form-control" name="duration"
                                                    type="number"
                                                    placeholder="{{ __('adminstaticword.Enter') }} Duration">
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label
                                                    for="cbToggleSubscription">{{ __('adminstaticword.Subscription') }}:</label>
                                                <input id="subscription1" type="checkbox" name="is_subscription_enabled"
                                                    class="custom_toggle" />
                                                <br>

                                                <small class="text-info"><i class="fa fa-question-circle"></i>
                                                    {{ __('Subscription bundle works with stripe payment only') }}.</small><br>
                                                <small class="text-info">
                                                    {{ __('Enable it only when you have setup stripe') }}.</small>


                                                <br>
                                                <div id="subscription" style="display: none;">
                                                    <div class="form-group">
                                                        <label>{{ __('adminstaticword.BillingPeriod') }}</label>
                                                        <select class="form-control select2" name="billing_interval">
                                                            <option value="day">{{ __('Daily') }}</option>
                                                            <option value="week">{{ __('Weekly') }}</option>
                                                            <option value="month">{{ __('Monthly') }}</option>
                                                            <option value="year">{{ __('Yearly') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{{ __('adminstaticword.PreviewImage') }}: <sup
                                                    class="redstar">*</sup></label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" name="preview_image" class="custom-file-input"
                                                        id="image" aria-describedby="inputGroupFileAddon01" required>
                                                    <label class="custom-file-label"
                                                        for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-group">
                                        <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                            {{ __('Reset') }}</button>
                                        <button type="submit" class="btn btn-primary-rgba"><i
                                                class="fa fa-check-circle"></i>
                                            {{ __('Create') }}</button>
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
            $(function() {
                $('.js-example-basic-single').select2();
            });
            $(function() {
                $('#cb1').change(function() {
                    $('#j').val(+$(this).prop('checked'))
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
            $("#cb3").on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).attr('value', '1');
                } else {
                    $(this).attr('value', '0');
                }
            });
            $(function() {
                $('#ms').change(function() {
                    if ($('#ms').val() == 'yes') {
                        $('#doabox').show();
                    } else {
                        $('#doabox').hide();
                    }
                });
            });
            $(function() {
                $('#ms').change(function() {
                    if ($('#ms').val() == 'yes') {
                        $('#doaboxx').show();
                    } else {
                        $('#doaboxx').hide();
                    }
                });
            });
            $(function() {
                $('#msd').change(function() {
                    if ($('#msd').val() == 'yes') {
                        $('#doa').show();
                    } else {
                        $('#doa').hide();
                    }
                });
            });
            $(function() {
                var urlLike = "{{ url('admin/dropdown') }}";
                $('#category_id').change(function() {
                    var up = $('#upload_id').empty();
                    var cat_id = $(this).val();
                    if (cat_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            url: urlLike,
                            data: {
                                catId: cat_id
                            },
                            success: function(data) {
                                console.log(data);
                                up.append('<option value="">Please Choose</option>');
                                $.each(data, function(id, title) {
                                    up.append($('<option>', {
                                        value: id,
                                        text: title
                                    }));
                                });
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                console.log(XMLHttpRequest);
                            }
                        });
                    }
                });
            });
            $(function() {
                var urlLike = "{{ url('admin/gcat') }}";
                $('#upload_id').change(function() {
                    var up = $('#grand').empty();
                    var cat_id = $(this).val();
                    if (cat_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            url: urlLike,
                            data: {
                                catId: cat_id
                            },
                            success: function(data) {
                                console.log(data);
                                up.append('<option value="">Please Choose</option>');
                                $.each(data, function(id, title) {
                                    up.append($('<option>', {
                                        value: id,
                                        text: title
                                    }));
                                });
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                console.log(XMLHttpRequest);
                            }
                        });
                    }
                });
            });
        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            $(function() {
                $('#cb111').change(function() {
                    if ($('#cb111').is(':checked')) {
                        $('#pricebox').show('fast');
                        $('#priceMain').attr('required', true);
                        $('#discount_price').attr('required', true);
                    } else {
                        $('#pricebox').hide('fast');
                    }
                });
                $('#installments').on('change', function() {
                    if ($('#installments').is(':checked')) {
                        $('#totalInstallments').show('fast');
                        $('#total_installments').attr('required', true);
                    } else {
                        $('#totalInstallments').hide('fast');
                        $('#total_installments').attr('required', false);
                    }
                });
            });
        })(jQuery);
    </script>
    <script>
        (function($) {
            "use strict";
            $(function() {
                $('#duration1-dis').change(function() {
                    if ($('#duration1').is(':checked')) {
                        $('#duration').show('fast');
                    } else {
                        $('#duration').hide('fast');
                    }
                });
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

@endsection
