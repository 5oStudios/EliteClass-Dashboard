@extends('admin.layouts.master')
@section('title', 'Push Notification Setting')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Push Notification') }}
        @endslot
        @slot('menu1')
            {{ __('Push Notification') }}
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-md-8">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <form id="submitForm" action="{{ route('admin.push.notif') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{ __('adminstaticword.SelectUserGroup') }}: <span
                                        class="text-danger">*</span> </label>
                                <select required data-placeholder="Please select user group" name="user_group"
                                    id="user_group" class="select2 form-control">
                                    <option value="" selected disabled>{{ __('adminstaticword.SelectUserGroup') }}
                                    </option>
                                    <option {{ old('user_group') == 'all_users' ? 'selected' : '' }} value="all_users">
                                        {{ __('adminstaticword.All') }} {{ __('adminstaticword.Users') }} </option>
                                    <option {{ old('user_group') == 'all_instructors' ? 'selected' : '' }}
                                        value="all_instructors">
                                        {{ __('adminstaticword.All') }} {{ __('adminstaticword.Instructors') }} </option>
                                    <option {{ old('user_group') == 'all_admins' ? 'selected' : '' }} value="all_admins">
                                        {{ __('adminstaticword.All') }} {{ __('adminstaticword.Admin') }} </option>
                                    <option {{ old('user_group') == 'users' ? 'selected' : '' }} value="users">
                                        {{ __('adminstaticword.SpecificEnrolledUsers') }} </option>
                                    <option {{ old('user_group') == 'all' ? 'selected' : '' }} value="all">
                                        {{ __('adminstaticword.All') }}
                                        ({{ __('adminstaticword.Users') }} + {{ __('adminstaticword.Instructors') }} +
                                        {{ __('adminstaticword.Admin') }})</option>
                                </select>
                            </div>

                            <div class="form-group d-none" id="type">
                                <label for="filter_by_type" class="mr-1">{{ __('Order Type') }}: <span
                                        class="text-danger">*</span></label>
                                <select name="type" class="select2 form-control filter_by_type">
                                    <option value="" selected disabled>
                                        {{ __('adminstaticword.PleaseSelect') }}
                                    </option>
                                    <option value="course_id">{{ __('Course') }} </option>
                                    <option value="chapter_id">{{ __('Chapter') }} </option>
                                    <option value="bundle_id">{{ __('Package') }} </option>
                                    <option value="meeting_id">{{ __('Live Streaming') }} </option>
                                    <option value="offline_session_id">{{ __('In-Person Session') }} </option>
                                </select>
                            </div>

                            <div class="form-group d-none" id="type_orders">
                                <label for="filter_by_type_orders" class="mr-1">{{ __('Title') }}: <span
                                        class="text-danger">*</span></label>
                                <select name="type_ids[]" class="form-control select2 filter_by_type_orders"
                                    multiple="multiple">
                                    <option value="">{{ __('adminstaticword.PleaseSelect') }}</option>
                                </select>
                            </div>

                            <div class="form-group d-none" id="users_div">
                                <label for="" class="">{{ __('Users') }}:</label>
                                <select name="user_ids[]" class="form-control select2 user_list" multiple="multiple">
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('adminstaticword.Subject') }}: <span
                                        class="text-danger">*</span></label>
                                <input placeholder="" type="text" class="form-control" required name="subject"
                                    value="{{ old('subject') }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('adminstaticword.NotificationBody') }}: <span
                                        class="text-danger">*</span> </label>
                                <textarea required placeholder="" class="form-control" name="message" id="" cols="3" rows="5">{{ old('message') }}</textarea>
                            </div>
                            {{-- <div class="form-group">
                                <label for="">{{ __('adminstaticword.TargetURL') }} : </label>
                                <input value="{{ old('target_url') }}" class="form-control" name="target_url"
                                    type="url" placeholder="{{ url('/') }}">
                                <small class="text-info">
                                    <i class="fa fa-question-circle"></i>
                                    {{ __('On click of notification where you want to redirect the user') }}.
                                </small>
                            </div> --}}
                            <div class="form-group">
                                <label for="">{{ __('adminstaticword.NotificationIcon') }}: <small
                                        class="text-secondary"> {{ __('(optional)') }}
                                    </small></label>
                                <input value="{{ old('icon') }}" name="icon" class="form-control" type="url"
                                    placeholder="https://someurl/icon.png">
                                <small class="text-info">
                                    <i class="fa fa-question-circle"></i>
                                    {{ __('If not enter than default icon will use which you upload at time of create one signal app') }}
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="">{{ __('adminstaticword.Image') }}: <small class="text-secondary">
                                        {{ __('(optional)') }}
                                    </small></label>
                                <input value="{{ old('image') }}" class="form-control" name="image" type="url"
                                    placeholder="https://someurl/image.png">
                                <small class="text-info">
                                    <i class="fa fa-question-circle"></i> {{ __('adminstaticword.RecommnadedSize') }}:
                                    450x228 px.
                                </small>
                            </div>
                            <div class="from-group">
                                <label for="">{{ __('adminstaticword.ShowButton') }}: <small
                                        class="text-secondary"> {{ __('(optional)') }}
                                    </small></label>
                                <br>
                                <label class="switch">
                                    <input type="checkbox" class="push" name="show_button"
                                        {{ old('show_button') ? 'checked' : '' }} />
                                    <span class="knob"></span>
                                </label>
                            </div>
                            <div style="display: {{ old('show_button') ? 'block' : 'none' }};" id="buttonBox">
                                <div class="form-group">
                                    <label for="btn_text">{{ __('adminstaticword.ButtonText') }}: <span
                                            class="text-danger">*</span></label>
                                    <input value="{{ old('btn_text') }}" class="form-control" id="btn_text"
                                        name="btn_text" type="text" placeholder="Grab Now !">
                                </div>
                                <div class="form-group">
                                    <label for="btn_url">{{ __('adminstaticword.ButtonTargetURL') }}: <span
                                            class="text-danger">*</span></label>
                                    <input value="{{ old('btn_url') }}" class="form-control" id="btn_url"
                                        name="btn_url" type="url" placeholder="https://someurl/image.png">
                                    <small class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                        {{ __('On click of button where you want to redirect the user') }}.
                                    </small>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <button id="submitBtn" type="submit" class="btn btn-primary">
                                    <i class="loading-icon fa fa-circle-o-notch fa-spin fa-1x fa-fw d-none"></i> &nbsp;
                                    <i class="tick-icon fa fa-check-circle mr-2 ml-n1"></i>
                                    <span class="btn-txt">{{ __('Send') }}</span>
                                </button>
                            </div>
                        </form>
                        <div class="clear-both"></div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-4">
                <div class="card m-b-30">
                    <div class="card-header">
                        <a title="Get one signal keys" href="https://onesignal.com/" class="pull-right"
                            target="__blank">
                            <i class="fa fa-key"></i> {{ __('adminstaticword.Getyourkeysfromhere') }}
                        </a>
                        <br>
                        <form action="{{ route('onesignal.update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="ONE_SIGNAL_APP_ID">{{ __('adminstaticword.ONESIGNALAPPID') }}: <span
                                        class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input id="password-field" value="{{ env('ONE_SIGNAL_APP_ID') }}" type="password"
                                        name="ONE_SIGNAL_APP_ID" class="form-control"
                                        placeholder="Enter ONESIGNAL APP ID">
                                    <div class="input-group-prepend text-center">
                                        <span toggle="#password-field"
                                            class="fa fa-fw fa-eye field-icon toggle-password"></span></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ONE_SIGNAL_REST_API_KEY"> {{ __('adminstaticword.ONESIGNALRESTAPIKEY') }}:
                                    <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input id="password-fieldscds" value="{{ env('ONE_SIGNAL_REST_API_KEY') }}"
                                        type="password" name="ONE_SIGNAL_REST_API_KEY" class="form-control"
                                        placeholder="Enter ONESIGNAL REST API KEY">
                                    <div class="input-group-prepend text-center">
                                        <span toggle="#password-fieldscds"
                                            class="fa fa-fw fa-eye field-icon toggle-password"></span></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    Reset</button>
                                <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                    {{ __('Update') }}</button>
                            </div>
                        </form>
                        <div class="clear-both"></div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $('.push').on('change', function() {
            if ($(this).is(":checked")) {
                $('input[name=btn_text]').attr('required', 'required');
                $('#buttonBox').show('fast');
                $('#btn_text').attr('required', true);
                $('#btn_url').attr('required', true);
            } else {
                $('input[name=btn_text]').removeAttr('required');
                $('#buttonBox').hide('fast');
                $('#btn_text').attr('required', false);
                $('#btn_url').attr('required', false);
            }
        });

        $(function() {
            $("#submitForm").submit(function() {
                $(".loading-icon").removeClass("d-none");
                $(".tick-icon").addClass("d-none");
                $("#submitBtn").attr("disabled", true);
                $(".btn-txt").text("Sending...");
                $("#submitBtn").css('cursor', 'not-allowed');
            });
            $('#user_group').change(function() {
                if ($(this).val() === 'users') {
                    $('#type').removeClass('d-none');
                    $('.filter_by_type').attr('required', true);
                    $('.user_list').empty();

                } else {
                    $('#type').addClass('d-none');
                    $('#type_orders').addClass('d-none');
                    $('#users_div').addClass('d-none');
                    $('.filter_by_type').attr('required', false);
                    // $('.filter_by_type').empty();
                    $('.filter_by_type_orders').attr('required', false);
                    $('.filter_by_type_orders').empty();
                    $('.user_list').empty();
                }
            });

            $('.filter_by_type').change(function() {
                $('#type_orders').removeClass('d-none');
                $('#users_div').addClass('d-none');
                $('.filter_by_type_orders').attr('required', true);

                let type = $(this).val();

                $('.user_list').empty();
                let up = $('.filter_by_type_orders').empty();

                if (type && type != '') {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ url('admin/type/orders') }}",
                        data: {
                            type: type,
                        },
                        success: function(data) {
                            up.select2({
                                placeholder: "{{ __('adminstaticword.PleaseSelect') }}",
                            });
                            $.each(data, function(key, row) {
                                up.append($('<option>', {
                                    value: row.type_id,
                                    text: row.title
                                }));
                            });
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log('ERROR: ', XMLHttpRequest);
                        }
                    });

                } else if (type === '') {
                    $('#type_orders').addClass('d-none')
                    $('#users_div').removeClass('d-none');
                    $('.user_list').empty();
                }
            });

            $('.filter_by_type_orders').change(function() {
                $('#users_div').removeClass('d-none');

                let type = $('.filter_by_type').val();
                let type_ids = $(this).val();

                let up = $('.user_list').empty();

                if (type && type_ids) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ url('admin/type/orders/users') }}",
                        data: {
                            type: type,
                            type_id: type_ids,
                        },
                        success: function(data) {
                            up.select2({
                                placeholder: "{{ __('All') }}",
                            });
                            $.each(data, function(key, value) {
                                up.append($('<option>', {
                                    value: value.user.id,
                                    text: `${value.user.fname} ${value.user.lname} | ${value.user.mobile}`
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
    </script>
@endsection
