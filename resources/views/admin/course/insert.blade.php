@extends('admin.layouts.master')
@section('title', __('Create a new course'))

@section('breadcum')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Course') }}
        @endslot

        @slot('menu1')
            {{ __('Course') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('course.index') }}" class="float-right btn btn-primary-rgba mr-2"><i
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
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-tittle">{{ __('adminstaticword.Add') }} {{ __('adminstaticword.Course') }}</h5>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" action="{{ url('course/') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('adminstaticword.Category') }}:<span class="redstar">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control select2" required>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($category as $cate)
                                            <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                                    <select name="scnd_category_id" id="type_id" class="form-control select2" required>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                                    <select name="subcategory_id" id="upload_id" class="form-control select2" required>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ __('adminstaticword.ChildCategories') }}:<sup class="redstar">*</sup></label>
                                    <select name="childcategory_id[]" id="grand" class="form-control select2"
                                        multiple="multiple" required></select>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                @if (Auth::user()->role == 'admin')
                                    <div class="col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<sup
                                                class="redstar">*</sup></label>
                                        <select name="user_id"
                                            class="form-control js-example-basic-single col-md-7 col-xs-12" required>
                                            <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->fname }}
                                                    {{ $user->lname }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                @endif
                                @if (Auth::user()->role == 'instructor')
                                    <div class="col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<sup
                                                class="redstar">*</sup></label>
                                        <input type="text" class="form-control"
                                            value="{{ $users->fname }} {{ $users->lname }}" readonly>
                                        <input type="hidden" class="form-control" name="user_id"
                                            value="{{ $users->id }}">

                                    </div>
                                @endif

                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.CourseName') }}: <sup
                                            class="redstar">*</sup></label>
                                    <input type="title" class="form-control" name="title" id="exampleInputTitle"
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.CourseName') }}"
                                        value="{{ old('title') }}" required>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label
                                        for="exampleInputTit1e">{{ __('adminstaticword.CourseWhtsapGrupLink') }}:</label>
                                    <input type="text" class="form-control" name="wtsap_link" id="wtsap_link"
                                        value="{{ old('wtsap_link') }}">
                                </div>
                            </div>
                            <br>

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Requirements') }}: <sup
                                            class="redstar">*</sup></label>
                                    <textarea name="requirement" rows="3" class="form-control"
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Requirements') }}" required>{{ old('requirement') }}</textarea>
                                </div>
                            </div>
                            <br> --}}

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Detail') }}: <sup
                                            class="text-danger">*</sup></label>
                                    <textarea id="detail" name="detail" rows="5" class="form-control"
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Detail') }}"></textarea>
                                </div>
                            </div>
                            <br>

                            <!-- country start -->
                            {{-- <div class="row">
                                <div class="col-md-12">

                                    <label>{{ __('Country') }}: </label>
                                    <select class="select2-multi-select form-control" name="country[]" multiple="multiple">
                                        @foreach ($countries as $country)
                                            <option>{{ $country->name }}</option>
                                        @endforeach
                                    </select>

                                    <small class="text-info"><i class="fa fa-question-circle"></i>
                                        ({{ __('Select those countries where you want to block courses') }} )</small>

                                </div>
                            </div>
                            <br> --}}
                            <!-- country end -->

                            {{-- @if (Auth::User()->role == 'admin')
                                <div class="row">
                                    <div class="col-md-12">

                                        <label for="exampleInputSlug">{{ __('adminstaticword.SelectTags') }}:</label>
                                        <select class="form-control js-example-basic-single" name="level_tags">
                                            <option value="none" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}
                                            </option>

                                            <option value="trending">{{ __('Trending') }}</option>

                                            <option value="onsale">{{ __('Onsale') }}</option>

                                            <option value="bestseller">{{ __('Bestseller') }}</option>

                                            <option value="beginner">{{ __('Beginner') }}</option>

                                            <option value="intermediate">{{ __('Intermediate') }}</option>

                                            <option value="expert">{{ __('Expert') }}</option>

                                        </select>

                                    </div>

                                </div>
                            @endif
                            <br> --}}

                            <div class="row">
                                <div class="col-md-12">

                                    <label>{{ __('adminstaticword.CourseTags') }}:</label>
                                    <select class="select2-multi-select form-control" name="course_tags[]"
                                        multiple="multiple" size="5" row="5"
                                        placeholder="{{ __('Please Enter Skills') }}">

                                        <option></option>

                                    </select>

                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>
                                        {{ __('Start Date') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input type="text" required class="form-control default-datepicker"
                                            name="start_date" placeholder="yyyy-mm-dd" aria-describedby="basic-addon2">
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
                                        <input type="text" required class="form-control default-datepicker"
                                            name="end_date" placeholder="yyyy-mm-dd" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            {{-- <div class="row">
                                <div class="col-md-12 d-none">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.ReturnAvailable') }}</label>
                                    <select name="refund_enable"
                                        class="form-control js-example-basic-single col-md-7 col-xs-12">
                                        <option value="none" selected disabled hidden>
                                            {{ __('frontstaticword.SelectanOption') }}
                                        </option>

                                        <option value="1">{{ __('Return Available') }}</option>
                                        <option value="0">{{ __('Return Not Available') }}</option>

                                    </select>

                                </div>
                            </div>
                            <br> --}}

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Paid') }}:</label>
                                    <input type="checkbox" class="custom_toggle" id="cb111" name="type" />

                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.Free') }}"
                                        data-tg-on="{{ __('adminstaticword.Paid') }}" for="cb111"></label>

                                    <br>
                                    <div style="display: none;" id="pricebox">
                                        <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                                class="redstar">*</sup></label>
                                        <input type="number" step="0.001" min="0" required
                                            oninput="javascript:offerPrice.value = this.value;" class="form-control"
                                            name="price" id="priceMain"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                            value="{{ old('price') ?? 0 }}">
                                        <br>
                                        <label for="discount_type">{{ __('discount_type') }}</label>
                                        <select name="discount_type" id="discount_type" class="form-control js-example-basic-single col-md-7 col-xs-12 mb-2">
                                            <option value="none" selected disabled>
                                                {{ __('frontstaticword.SelectanOption') }}
                                            </option>
                                            <option value="percentage">{{ __('percentage') }}</option>
                                            <option value="fixed">{{ __('fixed') }}</option>
                                        </select>

                                        <br>

                                        <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('Discounted price Zero(0) consider as no discount') }}
                                            </small>
                                        </label>

                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0" required class="form-control" name="discount_price" id="offerPrice"
                                                placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                value="{{ old('discount_price') ?? 0 }}" />

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
                                <br>

                                <div class="col-md-3" id="installment-box" style="display: none;">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Installment') }}: </label>
                                    <input type="checkbox" class="custom_toggle" id="installments" name="installment" />
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

                                {{-- <div class="col-md-3 d-none">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.MoneyBack') }}:</label>
                                    <input type="checkbox" class="custom_toggle" id="cb01" name="type"
                                        checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.No') }}"
                                        data-tg-on="{{ __('adminstaticword.Yes') }}" for="cb01"></label>
                                    <input type="hidden" name="free" value="0" id="cb10">
                                    <br>
                                    <div class="display-none" id="dooa">

                                        <label for="exampleInputSlug">{{ __('adminstaticword.Days') }}: <sup
                                                class="redstar">*</sup></label>
                                        <input type="number" min="1" class="form-control" name="day"
                                            id="exampleInputPassword1"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Days') }}"
                                            value="">
                                    </div> --}}

                                <div class="col-md-3">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label>
                                    <input type="checkbox" class="custom_toggle" name="status" id="cb3"
                                        checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.Deactive') }}"
                                        data-tg-on="{{ __('adminstaticword.Active') }}" for="cb3"></label>
                                </div>

                                {{-- <div class="col-md-3">
                                    @if (Auth::User()->role == 'admin')
                                        <label for="exampleInputDetails">{{ __('adminstaticword.Featured') }}:</label>
                                        <input type="checkbox" class="custom_toggle" id="cb1" name="featured"
                                            checked />
                                        <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.OFF') }}"
                                            data-tg-on="{{ __('adminstaticword.ON') }}" for="cb1"></label>
                                        <input type="hidden" name="featured" value="0" id="j">
                                    @endif
                                </div> --}}
                                {{-- <div class="col-md-3">
                                    <label
                                        for="exampleInputDetails">{{ __('adminstaticword.InvolvementRequest') }}:</label>
                                    <input name="involvement_request" type="checkbox" class="custom_toggle"
                                        id="involve" checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.OFF') }}"
                                        data-tg-on="{{ __('adminstaticword.ON') }}" for="involve"></label>
                                </div> --}}
                            </div>
                            <br>

                            <div class="row" style="display:none;">
                                <div class="col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.PreviewVideo') }}:</label>
                                    <input id="preview" type="checkbox" class="custom_toggle" name="preview_type" />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.URL') }}"
                                        data-tg-on="{{ __('adminstaticword.Upload') }}" for="preview"></label>

                                    <div style="display: none;" id="document1">
                                        <label for="exampleInputSlug">{{ __('adminstaticword.UploadVideo') }}:</label>
                                        <input type="file" name="video" id="video" value=""
                                            class="form-control">
                                    </div>
                                    <div id="document2">
                                        <label for="">{{ __('adminstaticword.URL') }}: </label>
                                        <input type="url" name="url" id="url"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.URL') }}"
                                            class="form-control" value="{{ old('url') }}">
                                    </div>
                                </div>

                                {{-- <div class="col-md-6">
                                    <label for="">{{ __('adminstaticword.Duration') }}: </label>
                                    <input id="duration_type" type="checkbox" class="custom_toggle" name="duration_type"
                                        checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.Days') }}"
                                        data-tg-on="{{ __('adminstaticword.Month') }}" for="duration_type"></label>
                                    <small class="text-muted"><i class="fa fa-question-circle"></i>
                                        {{ __('If enabled duration can be in months') }},</small>
                                    <small class="text-muted"> {{ __('when Disabled duration can be in days') }}.</small>
                                    <br>
                                    <label for="exampleInputSlug">{{ __('adminstaticword.CourseExpireDuration') }}</label>
                                    <input min="1" class="form-control" name="duration" type="number"
                                        id="duration"
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.CourseExpireDuration') }}"
                                        value="{{ old('duration') }}">
                                </div> --}}
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-dark"
                                        for="exampleInputSlug">{{ __('adminstaticword.PreviewImage') }}: <sup
                                            class="redstar">*</sup></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="file">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" name="preview_image" class="custom-file-input"
                                                id="file" aria-describedby="inputGroupFileAddon01" required>
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ __('Choose file') }}</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-md-6">
                                    @if (Auth::User()->role == 'admin')
                                        <label for="Revenue">{{ __('adminstaticword.InstructorRevenue') }}:</label>
                                        <div class="input-group">
                                            <input min="1" class="form-control" name="instructor_revenue"
                                                type="number" id="revenue"
                                                placeholder="{{ __('Enter revenue percentage') }} "
                                                class="{{ $errors->has('instructor_revenue') ? ' is-invalid' : '' }} form-control"
                                                value="{{ old('instructor_revenue') }}">
                                            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                        </div>
                                    @endif
                                </div> --}}
                            </div>
                            <br>
                            <br>

                            {{-- <div class="row">
                                <div class="col-sm-3">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Assignment') }}:</label>
                                    <input {{ old('assignment_enable') == '0' ? '' : 'checked' }} id="frees"
                                        type="checkbox" class="custom_toggle" name="assignment_enable" checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.No') }}"
                                        data-tg-on="{{ __('adminstaticword.Yes') }}" for="frees"></label>
                                </div>
                                <div class="col-sm-3">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Appointment') }}:</label>
                                    <input {{ old('appointment_enable') == '0' ? '' : 'checked' }} id="frees1"
                                        type="checkbox" class="custom_toggle" name="appointment_enable" checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.No') }}"
                                        data-tg-on="{{ __('adminstaticword.Yes') }}" for="frees1"></label>

                                </div>
                                <div class="col-sm-3">
                                    <label
                                        for="exampleInputDetails">{{ __('adminstaticword.CertificateEnable') }}:</label>
                                    <input {{ old('certificate_enable') == '0' ? '' : 'checked' }} id="frees2"
                                        type="checkbox" class="custom_toggle" name="certificate_enable" checked />
                                    <label class="tgl-btn" data-tg-off="{{ __('adminstaticword.No') }}"
                                        data-tg-on="{{ __('adminstaticword.Yes') }}" for="frees2"></label>
                                </div>
                                <div class="col-sm-3">
                                    <label for="">{{ __('adminstaticword.DripContent') }}: </label>
                                    <input id="drip_enable" type="checkbox" class="custom_toggle" name="drip_enable"
                                        checked />
                                    <label class="tgl-btn" data-tg-off="Disable" data-tg-on="Enable"
                                        for="drip_enable"></label>
                                </div>
                            </div>
                            <br>
                            <br> --}}
                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    {{ __('Reset') }}</button>
                                <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>
                            <div class="clear-both"></div>
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
                $('.js-example-basic-single').select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                });
            });
            $(function() {
                $('#cb1').change(function() {
                    $('#j').val(+$(this).prop('checked'))
                })
            })
            $('#cb111').on('change', function() {
                if ($('#cb111').is(':checked')) {
                    $('#pricebox').show('fast');
                    $('#installment-box').show('fast');
                    $('#priceMain').attr('required', true);
                } else {
                    $('#pricebox').hide('fast');
                    $('#installment-box').show('fast');
                    $('#priceMain').val(0);
                    $('#priceMain').attr('required', false);
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
                var urlLike = '{{ url('type/categories') }}';
                $('#category_id').change(function() {
                    var up = $('#type_id').empty();
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
                                up.append(
                                    "<option value=''>{{ __('Please Choose') }}</option>"
                                );
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
            function updatePrefix() {
            console.log('asdasds');

                var discountType = document.getElementById('discount_type').value;
                var prefixElement = document.getElementById('prefix');
                console.log(discountType);
                if (discountType === 'percentage') {
                    prefixElement.textContent = '%';
                    console.log('%');
                } else if (discountType === 'fixed') {
                    prefixElement.textContent = 'KWD';
                    console.log('KWD');
                } else {
                    prefixElement.textContent = '';
                }
            };

            // Add an event listener to the discount type select element
            document.getElementById('discount_type').addEventListener('change', ()=>{
                console.log('test');
            } );

            // Initial call to set the prefix based on the default selected value
            updatePrefix();
            $(function() {
                var urlLike = '{{ url('admin/dropdown') }}';
                $('#type_id').change(function() {
                    var up = $('#upload_id').empty();
                    var cat_id = $('#category_id').val();
                    var type_id = $(this).val();
                    if (type_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            url: urlLike,
                            data: {
                                catId: cat_id,
                                typeId: type_id
                            },
                            success: function(data) {
                                console.log(data);
                                up.append(
                                    "<option value=''>{{ __('Please Choose') }}</option>"
                                );
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
                var urlLike = '{{ url('admin/gcat') }}';
                $('#upload_id').change(function() {
                    var up = $('#grand').empty();
                    var cat_id = $('#category_id').val();
                    var type_id = $('#type_id').val();
                    var sub_id = $(this).val();
                    if (sub_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            url: urlLike,
                            data: {
                                catId: cat_id,
                                typeId: type_id,
                                subId: sub_id
                            },
                            success: function(data) {
                                console.log(data);
                                up.select2({
                                    placeholder: "{{ __('Please Choose') }}",
                                    allowClear: true
                                });
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
        $(".midia-toggle").midia({
            base_url: '{{ url('') }}',
            title: "{{ __('Choose Course Image') }}",
            dropzone: {
                acceptedFiles: '.jpg,.png,.jpeg,.webp,.bmp,.gif'
            },
            directory_name: 'course'
        });
    </script>
@endsection
