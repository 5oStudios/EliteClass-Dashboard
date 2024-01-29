@extends('admin.layouts.master')
@section('title', 'Edit live streaming - Admin')

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
            {{ __(' Edit live streaming') }}
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title"> {{ __('adminstaticword.Edit') }} {{ __('adminstaticword.Meeting') }}
                                    : #{{ $meeting->meetingid }}</h5>
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

                        <form autocomplete="off" action="{{ route('bbl.update', $meeting->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label> {{ __('adminstaticword.Meeting') }} {{ __('adminstaticword.Name') }}: <sup
                                            class="redstar">*</sup></label>
                                    <input id="meetingname" value="{{ old('meetingname', $meeting->meetingname) }}"
                                        type="text" name="meetingname" class="form-control" required
                                        placeholder="{{ __('Enter live streaming name') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.MeetingID') }}: <sup class="redstar">*</sup></label>
                                    <input id="meetingid" value="{{ old('meetingid', $meeting->meetingid) }}"
                                        type="text" name="meetingid" class="form-control" required
                                        placeholder="{{ __('Enter live streaming id') }}">
                                </div>

                                @if (Auth::User()->role == 'admin')
                                    <div class="form-group col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                                                class="redstar">*</span>
                                        </label>
                                        <select name="instructor_id" required class="form-control js-example-basic-single">
                                            <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                            @foreach ($users as $user)
                                                <option
                                                    {{ old('instructor_id', $meeting->instructor_id) == $user->id ? 'selected' : '' }}
                                                    value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                @if (Auth::User()->role == 'instructor')
                                    <div class="form-group col-md-6">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<span
                                                class="redstar">*</span>
                                        </label>
                                        <input type="text" value="{{ $users->fname }} {{ $users->lname }}" readonly
                                            class="form-control">
                                        <input type="hidden" name="instructor_id" value="{{ $users->id }}"
                                            class="form-control">
                                    </div>
                                @endif

                                <div class="form-group col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.LinkByCourse') }}:</label><br>
                                    <input type="checkbox" id="myCheck" name="link_by"
                                        {{ old('link_by', $meeting->link_by) == 'course' ? 'checked' : '' }}
                                        class="custom_toggle">
                                </div>
                                <div class="form-group col-md-6"
                                    style="{{ $meeting['link_by'] == 'course' ? '' : 'display:none' }}"
                                    id="update-password">
                                    <label>{{ __('adminstaticword.Courses') }}:<sup class="text-danger">*</sup></label>
                                    <select name="course_id" id="course_id" class="form-control select2">
                                        @foreach ($course as $caat)
                                            <option
                                                {{ old('course_id', $meeting->course_id) == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 indv-bbl"
                                    style="{{ $meeting->link_by == 'course' ? 'display:none' : '' }}">
                                    <label>{{ __('adminstaticword.Category') }}<span class="redstar">*</span></label>
                                    <select name="main_category" id="category_id"
                                        class="form-control js-example-basic-single">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($category as $caat)
                                            <option {{ $meeting->main_category == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 indv-bbl"
                                    style="{{ $meeting->link_by == 'course' ? 'display:none' : '' }}">
                                    <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                                    <select name="scnd_category_id" id="type_id"
                                        class="form-control js-example-basic-single">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($typecategory as $caat)
                                            <option {{ $meeting->scnd_category_id == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 indv-bbl"
                                    style="{{ $meeting->link_by == 'course' ? 'display:none' : '' }}">

                                    <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                                    <select name="sub_category" id="upload_id"
                                        class="form-control js-example-basic-single">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($subcategory as $caat)
                                            <option {{ $meeting->sub_category == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 indv-bbl"
                                    style="{{ $meeting->link_by == 'course' ? 'display:none' : '' }}">
                                    <label>{{ __('adminstaticword.ChildCategories') }}:<span
                                            class="redstar">*</span></label>
                                    <select name="ch_sub_category[]" id="grand" class="form-control select2"
                                        multiple="multiple">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>

                                        @foreach ($childcategory as $caat)
                                            @if (is_array($meeting['ch_sub_category']) || is_object($meeting['ch_sub_category']))
                                                <option value="{{ $caat->id }}"
                                                    {{ in_array($caat->id, $meeting['ch_sub_category'] ?: []) ? 'selected' : '' }}>
                                                    {{ $caat->title }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                                class="redstar">*</sup></label>
                                        <input type="number" step="1" min="0" required
                                             class="form-control"
                                            name="price" id="priceMain"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                            value="{{ old('price', $meeting->price ?? 0) }}">

                                </div>

                                <div class="form-group col-md-6">
                                <label for="discount_type">{{ __('discount_type') }}</label>
                                        <select value="old('discount_type', $meeting->discount_type ?? none)" name="discount_type" id="discount_type" class="form-control js-example-basic-single ">
                                            <option value="none"  disabled>
                                                {{ __('frontstaticword.SelectanOption') }}
                                            </option>
                                            <option value="percentage">{{ __('percentage') }}</option>
                                            <option value="fixed">{{ __('fixed') }}</option>
                                        </select>
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('Discounted price Zero(0) consider as no discount') }}
                                            </small>
                                        </label>

                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0" required class="form-control" name="discount_price" id="offerPrice"
                                                placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                value="{{ old('discount_price', $meeting->discount_price ?? 0) }}" />

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
                               

                                <div class="form-group col-md-12">
                                    <label>
                                        {{ __('Live Streaming Detail') }}:<sup class="redstar">*</sup>
                                    </label>
                                    <textarea id="detail" name="detail" rows="3" class="form-control">{{ old('detail', $meeting->detail) }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.Meeting') }} {{ __('adminstaticword.Duration') }}: <sup
                                            class="redstar">*</sup><i
                                            class="feather icon-help-circle text-secondary"></i><small class="text-muted">
                                            {{ __('It will be count in minutes.') }}</small></label>
                                    <input value="{{ old('duration', $meeting->duration) }}" type="number"
                                        name="duration" min="0" class="form-control" required
                                        placeholder="{{ __('Enter live streaming duration eg. 40') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>
                                        {{ __('Live Streaming Start Time') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <!-- getUserTimeZoneDateTime() is a helper function defined in App/Helpers/helper.php -->
                                    <div class="input-group">
                                        <input
                                            value="{{ old('start_time', date('Y-m-d h:i a', strtotime(getUserTimeZoneDateTime($meeting->start_time)))) }}"
                                            name="start_time" type="text" id="datetimepicker2" class="form-control"
                                            placeholder="yyyy-mm-dd hh:ii a" aria-describedby="basic-addon5" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon5"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>
                                        {{ __('Live Streaming Expire Date') }}:<sup class="redstar">*</sup>
                                    </label>

                                    <div class="input-group">
                                        <input value="{{ old('expire_date', $meeting->expire_date) }}" name="expire_date"
                                            type="text" class="form-control datepicker" placeholder="yyyy-mm-dd"
                                            aria-describedby="basic-addon5" required />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon5"><i
                                                    class="feather icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label> {{ __('Moderator Password') }}:<sup class="redstar">*</sup></label>
                                    <div class="input-group mb-3">

                                        <input id="password-field" value="{{ $meeting->modpw }}" type="password"
                                            name="modpw" class="form-control" placeholder="enter moderator password"
                                            required>
                                        <div class="input-group-prepend text-center">
                                            <span toggle="#password-field"
                                                class="fa fa-fw fa-eye field-icon toggle-password"></span></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Attandee Password') }}: <sup class="redstar">*</sup> <small
                                            class="text-muted"><br>
                                            (<b>Tip !</b> Share your attendee password to students using social handling
                                            networks.)</small></label>

                                    <input required id="attendeepw" value="{{ $meeting->attendeepw }}" type="password"
                                        name="attendeepw" class="form-control" placeholder="enter attandee password"
                                        required>

                                    <small class="text-muted">Should be diffrent from <b>Moderator</b> password</small>
                                </div> --}}
                                <div class="form-group col-md-6">
                                    <label>{{ __('Set Welcome Message') }}: </label>
                                    <input value="{{ old('welcomemsg', $meeting->welcomemsg) }}" type="text"
                                        class="form-control" name="welcomemsg"
                                        placeholder="{{ __('Enter welcome message') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('Set Max Participants') }}: <sup class="redstar">*</sup><i
                                            class="feather icon-help-circle text-secondary"></i><small class="text-muted">
                                            {{ __('It will be inclusive of admin or instructor.') }}</small></label>
                                    <input value="{{ old('setMaxParticipants', $meeting->setMaxParticipants) }}"
                                        type="number" min="0" class="form-control" name="setMaxParticipants"
                                        placeholder="{{ __('Enter maximum participant no., leave blank if want unlimited participant') }}"
                                        required />
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('adminstaticword.Image') }}:<sup class="redstar">*</sup>
                                        {{ __('size: 270x200') }}</label>
                                    <!-- ====================== -->
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                name="image" value="{{ $meeting->image }}">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ $meeting->image ?? __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    @if ($meeting['image'] !== null && $meeting['image'] !== '')
                                        <img src="{{ url('/images/bg/' . $meeting->image) }}" height="70px;"
                                            width="70px;" />
                                    @endif
                                    <!-- ====================== -->
                                </div>

                                <div class="form-group col-md-3">
                                    <label>{{ __('Set Mute on Start') }}:</label>
                                    <input
                                        {{ $meeting->setMuteOnStart == 1 || old('setMuteOnStart') == 'on' ? 'checked' : '' }}
                                        class="custom_toggle" type="checkbox" name="setMuteOnStart" />
                                </div>

                                {{-- <div class="d-none form-group col-md-3">
                                    <label>{{ __('Allow Record') }}:</label>
                                    <input {{ $meeting->allow_record == '1' ? "checked" : "" }} class="custom_toggle" type="checkbox" name="allow_record" />
                                </div> --}}

                                {{-- <input type='hidden' class="custom_toggle" value="1" name="allow_record" /> --}}

                                <div class="form-group col-md-6">
                                    <label>Allow Record: </label>
                                    @if ($meeting['allow_record'] == 1)
                                        <input class="custom_toggle" type="checkbox" name="allow_record" checked />
                                    @else
                                        <input class="custom_toggle" type="checkbox" name="allow_record" />
                                    @endif
                                </div>

                            </div>

                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                {{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                {{ __('Update') }}</button>

                            <div class="clear-both"></div>
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
                        $('#update-password').show('fast');
                        $('.indv-bbl').hide('fast');

                        $('#category_id').val('').trigger('change');
                        $('#upload_id').val('').trigger('change');
                        $('#type_id').val('').trigger('change');
                        $('#grand').val('').trigger('change');
                    } else {
                        $('#course_id').val('').trigger('change');
                        $('#update-password').hide('fast');
                        $('.indv-bbl').show('fast');
                    }
                });

            });
            $(function() {
                var urlLike = "{{ url('type/categories') }}";
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
                var urlLike = "{{ url('admin/dropdown') }}";
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
                var urlLike = "{{ url('admin/gcat') }}";
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
            $(function() {
                $('#meetingname').blur(function() {
                    if ("{{ app()->getLocale() }}" == 'en') {
                        var meetingname = $(this).val();
                        meetingname = meetingname.replaceAll(' ', '').toLowerCase();
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
