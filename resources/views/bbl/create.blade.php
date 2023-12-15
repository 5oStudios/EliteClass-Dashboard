@extends('admin.layouts.master')
@section('title', 'Create a new live stream - Admin')

@section('maincontent')
    @component('components.breadcumb', ['fourthactive' => 'active'])
        @slot('heading')
            {{ __('List all Live Streamings') }}
        @endslot
        @slot('menu1')
            {{ __('Live Streamings') }}
        @endslot
        @slot('menu2')
            {{ __('Big Blue') }}
        @endslot
        @slot('menu3')
            {{ __(' List all Live Streamings') }}
        @endslot
        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ url('bigblue/meetings') }}" class="btn btn-primary-rgba"><i
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
                        <h5 class="card-title">{{ __('Create new live streaming') }}</h5>
                    </div>
                    <div class="card-body">

                        <form autocomplete="off" action="{{ route('bbl.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label> {{ __('adminstaticword.Meeting') }} {{ __('adminstaticword.Name') }}: <sup
                                            class="redstar">*</sup></label>
                                    <input id="meetingname" value="{{ old('meetingname') }}" type="text"
                                        name="meetingname" class="form-control" required
                                        placeholder="{{ __('Enter live streaming name') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.MeetingID') }}: <sup class="redstar">*</sup></label>
                                    <input id="meetingid" value="{{ old('meetingid') }}" type="text" name="meetingid"
                                        class="form-control" required placeholder="{{ __('Enter live streaming id') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.LinkByCourse') }}:</label><br>
                                    <input type="checkbox" id="myCheck" name="link_by" class="custom_toggle">
                                </div>
                                <div style="display: none" class="form-group col-md-6 course-enable">
                                    <label>{{ __('adminstaticword.Courses') }}:<span class="redstar">*</span></label>
                                    <select name="course_id" id="course_id" class="select2 form-control">
                                        @foreach ($course as $cor)
                                            <option value="{{ $cor->id }}">{{ $cor->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="indv-bbl form-group col-md-6">
                                    <label>{{ __('adminstaticword.Category') }}:<span class="redstar">*</span></label>
                                    <select name="main_category" id="category_id" class="form-control select2">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($category as $cate)
                                            <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="indv-bbl form-group col-md-6">
                                    <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                                    <select name="scnd_category_id" id="type_id" class="form-control select2">
                                    </select>
                                </div>

                                <div class="indv-bbl form-group col-md-6">
                                    <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                                    <select name="sub_category" id="upload_id" class="form-control select2">
                                    </select>
                                </div>

                                <div class="indv-bbl form-group col-md-6">
                                    <label>{{ __('adminstaticword.ChildCategories') }}:<span
                                            class="redstar">*</span></label>
                                    <select name="ch_sub_category[]" id="grand" class="form-control select2"
                                        multiple="multiple"></select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="image">{{ __('adminstaticword.Image') }}: <sup class="redstar">*</sup>
                                        {{ __('size: 270x200') }}</label><br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                name="image">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ __('Choose file') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="sec4_four">
                                    <label>
                                        {{ __('Live Streaming Start Time') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input id="datetimepicker1" name="start_time" type="text" class="form-control"
                                            placeholder="yyyy-mm-dd hh:mm a" aria-describedby="basic-addon5" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon5"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="sec4_four">
                                    <label>
                                        {{ __('Live Streaming Expire Date') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input name="expire_date" type="text" class="form-control default-datepicker"
                                            placeholder="yyyy-mm-dd" aria-describedby="basic-addon5" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon5"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.Meeting') }} {{ __('adminstaticword.Duration') }}: <sup
                                            class="redstar">*</sup><i
                                            class="feather icon-help-circle text-secondary"></i><small class="text-muted">
                                            {{ __('It will be count in minutes.') }}</small></label>
                                    <input value="{{ old('duration') }}" type="number" name="duration" min="0"
                                        class="form-control" required
                                        placeholder="{{ __('Enter live streaming duration eg. 40') }}">
                                </div>

                                @if (Auth::User()->role == 'admin')
                                    <div class="form-group col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                                                class="redstar">*</span></label>
                                        <select name="instructor_id" required
                                            class="form-control js-example-basic-single">
                                            <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->fname }}
                                                    {{ $user->lname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                @if (Auth::User()->role == 'instructor')
                                    <div class="form-group col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                                                class="redstar">*</span></label>
                                        <input type="text" value="{{ $users->fname }} {{ $users->lname }}" readonly
                                            class="form-control">
                                        <input type="hidden" name="instructor_id" value="{{ $users->id }}"
                                            class="form-control">
                                    </div>
                                @endif

                                <div class="form-group col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}:<sup
                                            class="text-danger">*</sup></label>
                                    <input type="number" class="form-control" name="price"
                                        pattern="^(0|[1-9]\d*(\.\d+)?[eE]?[1-9]\d*)$" id="priceMain" required
                                        oninput="javascript:offerPrice.value = this.value;"
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('price') }}"
                                        value="{{ old('price') ?? 0 }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup
                                            class="text-danger">*</sup></label> <small class="text-muted">Discounted price
                                        Zero(0) consider as free</small>
                                    <input type="number" step="0.001" class="form-control" name="discount_price"
                                        min="0" id="offerPrice" required
                                        placeholder="{{ __('adminstaticword.Enter') }} {{ __('discount price') }}"
                                        value="{{ old('discount_price') ?? 0 }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>
                                        {{ __('Live Streaming Detail') }}:<sup class="redstar">*</sup>
                                    </label>
                                    <textarea id="detail" name="detail" rows="3" class="form-control"></textarea>
                                </div>

                                {{-- <div class="form-group col-md-6">
                                    <label> {{ __('Moderator Password') }}: <sup class="redstar">*</sup></label>
                                    <div class="input-group mb-3">

                                        <input id="password-field" type="password" name="modpw" class="form-control"
                                            placeholder="Enter moderator password" required>
                                        <div class="input-group-prepend text-center">
                                            <span toggle="#password-field"
                                                class="fa fa-fw fa-eye field-icon toggle-password"></span></i></span>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('Attandee Password') }}: <sup class="redstar">*</sup> <small
                                            class="text-muted"><br>
                                            (<b>Tip !</b> Share your attendee password to students using social handling
                                            networks.)</small></label>

                                    <input required id="attendeepw" value="" type="password" name="attendeepw"
                                        class="form-control" placeholder="Enter attandee password" required>

                                    <small class="text-muted">Should be diffrent from <b>Moderator</b> password</small>

                                </div> --}}

                                <div class="form-group col-md-6">
                                    <label>{{ __('Set Welcome Message') }}: </label>
                                    <input value="{{ old('welcomemsg') }}" type="text" class="form-control"
                                        name="welcomemsg" placeholder="{{ __('Enter welcome message') }}">

                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('Set Max Participants') }}: <sup class="redstar">*</sup><i
                                            class="feather icon-help-circle text-secondary"></i><small class="text-muted">
                                            {{ __('It will be inclusive of admin or instructor.') }}</small></label>
                                    <input value="{{ old('setMaxParticipants') }}" type="number" min="0"
                                        class="form-control" name="setMaxParticipants"
                                        placeholder="{{ __('Enter maximum participant no.') }}" required />

                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('Set Mute on Start') }}:</label>
                                    <input class="custom_toggle" type="checkbox" name="setMuteOnStart" />

                                </div>
                                <div class="form-group col-md-6 d-none">
                                    <label>{{ __('Allow Record') }}:</label>

                                </div>
                                <input type='hidden' class="custom_toggle" value="1" name="allow_record" />
                            </div>
                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                {{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                {{ __('Create') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        (function($) {
            "use strict";

            $(function() {
                $('#myCheck').change(function() {
                    if ($('#myCheck').is(':checked')) {
                        $('.course-enable').show('fast');
                        $('.indv-bbl').hide('fast');

                        $('#category_id').val('').trigger('change');
                        $('#type_id').val('').trigger('change');
                        $('#upload_id').val('').trigger('change');
                        $('#grand').val('').trigger('change');

                    } else {
                        $('#course_id').val('').trigger('change');
                        $('.course-enable').hide('fast');
                        $('.indv-bbl').show('fast');
                    }
                });
            });
            $(function() {
                $('#link_by').change(function() {
                    if ($('#link_by').is(':checked')) {
                        $('#sec1_one').show('fast');
                    } else {
                        $('#sec1_one').hide('fast');
                    }
                });
                $('#recurring_meeting').change(function() {
                    if ($('#recurring_meeting').is(':checked')) {
                        $('#sec4_four').hide('fast');
                        $('#sec5_four').hide('fast');
                        $('#sec3_three').hide('fast');
                    } else {
                        $('#sec4_four').show('fast');
                        $('#sec5_four').show('fast');
                        $('#sec3_three').show('fast');
                    }
                });
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
                                subId: sub_id,
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
            $(function() {
                $('#meetingname').blur(function() {
                    if ("{{ app()->getLocale() }}" == 'en') {
                        var meetingname = $(this).val();
                        meetingname = meetingname.replaceAll(' ', '').toLowerCase();
                        // alert(meetingname);
                        if (meetingname.length > 20) {
                            var meetingid = meetingname.slice(0, 19) + Math.random().toString(36)
                                .substr(3);
                            $('#meetingid').val(meetingid);

                        } else {
                            var meetingid = meetingname + Math.random().toString(36).substr(3);
                            $('#meetingid').val(meetingid);
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
