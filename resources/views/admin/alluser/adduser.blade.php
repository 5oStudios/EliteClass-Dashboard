@extends('admin.layouts.master')
@section('title','Create a new student')
@section('breadcum')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Student') }}
@endslot
@slot('menu1')
{{ __('Student') }}
@endslot
@slot('button')
<div class="col-md-5 col-lg-5">
    <div class="widgetbar">
        <a href="{{route('alluser.index')}}" class="float-right btn btn-primary-rgba mr-2"><i
                class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a> </div>
</div>
@endslot
@endcomponent
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())  
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)     
                <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                @endforeach  
            </div>
            @endif
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-tittle">{{ __('adminstaticword.Add') }} {{ __('Student') }}</h5>
                </div>
                <div class="card-body">
                    <form autocomplete="off" action="{{ route('alluser.store') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="fname">
                                        {{ __('adminstaticword.FirstName') }}:<sup class="text-danger">*</sup>
                                    </label>
                                    <input value="{{ old('fname') }}" autofocus required name="fname" type="text" class="form-control"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.FirstName') }}" />
                                </div>

                                <div class="form-group">
                                    <label class="text-dark" for="institute">{{ __('adminstaticword.Institute') }}: 
                                        <sup class="text-danger">*</sup></label>
                                    <select id="institute" class="form-control select2" name="institute">
                                        <option value="none" selected disabled hidden>
                                            {{ __('adminstaticword.Please') }} {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($instituteCategories as $institute)
                                        <option value="{{ $institute->slug }}">{{ $institute->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="text-dark" for="mobile">{{ __('adminstaticword.Email') }}: <sup
                                            class="text-danger">*</sup></label>
                                    <input value="{{ old('email')}}" required type="email" name="email"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Email') }}"
                                           class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label class="text-dark" for="exampleInputSlug">{{ __('adminstaticword.Image') }}: </label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" name="user_img" class="custom-file-input" id="user_img"
                                                   aria-describedby="inputGroupFileAddon01">
                                            <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label class="text-dark" for="twitter_url">
                                            {{ __('adminstaticword.TwitterUrl') }}:
                                        </label>
                                        <input autofocus name="twitter_url" type="text" class="form-control" placeholder="https://twitter.com" />
                                    </div>
                                    <div class="form-group">
                                        <label class="text-dark" for="linkedin_url">
                                            {{ __('adminstaticword.LinkedInUrl') }}:
                                        </label>
                                        <input autofocus name="linkedin_url" type="text" class="form-control" placeholder="https://linkedin.com" />
                                    </div> --}}
                                </div>

                                <div class="form-group">
                                    <label>{{ __('TimeZone') }}: <sup class="redstar">*</sup></label>
                                    <select class="form-control select2" name="timezone" required>
                                        <option value="">{{ __('Choose your timezone') }}</option>
                                        <option value="Pacific/Midway">Midway Island, Samoa</option>
                                        <option value="Pacific/Pago_Pago">Pago Pago</option>
                                        <option value="Pacific/Honolulu">Hawaii</option>
                                        <option value="America/Anchorage">Alaska</option>
                                        <option value="America/Vancouver">Vancouver</option>
                                        <option value="America/Los_Angeles">Pacific Time (US and Canada)</option>
                                        <option value="America/Tijuana">Tijuana</option>
                                        <option value="America/Edmonton">Edmonton</option>
                                        <option value="America/Denver">Mountain Time (US and Canada)</option>
                                        <option value="America/Phoenix">Arizona</option>
                                        <option value="America/Mazatlan">Mazatlan</option>
                                        <option value="America/Winnipeg">Winnipeg</option>
                                        <option value="America/Regina">Saskatchewan</option>
                                        <option value="America/Chicago">Central Time (US and Canada)</option>
                                        <option value="America/Mexico_City">Mexico City</option>
                                        <option value="America/Guatemala">Guatemala</option>
                                        <option value="America/El_Salvador">El Salvador</option>
                                        <option value="America/Managua">Managua</option>
                                        <option value="America/Costa_Rica">Costa Rica</option>
                                        <option value="America/Montreal">Montreal</option>
                                        <option value="America/New_York">Eastern Time (US and Canada)</option>
                                        <option value="America/Indianapolis">Indiana (East)</option>
                                        <option value="America/Panama">Panama</option>
                                        <option value="America/Bogota">Bogota</option>
                                        <option value="America/Lima">Lima</option>
                                        <option value="America/Halifax">Halifax</option>
                                        <option value="America/Puerto_Rico">Puerto Rico</option>
                                        <option value="America/Caracas">Caracas</option>
                                        <option value="America/Santiago">Santiago</option>
                                        <option value="America/St_Johns">Newfoundland and Labrador</option>
                                        <option value="America/Montevideo">Montevideo</option>
                                        <option value="America/Araguaina">Brasilia</option>
                                        <option value="America/Argentina/Buenos_Aires">Buenos Aires, Georgetown</option>
                                        <option value="America/Godthab">Greenland</option>
                                        <option value="America/Sao_Paulo">Sao Paulo</option>
                                        <option value="Atlantic/Azores">Azores</option>
                                        <option value="Canada/Atlantic">Atlantic Time (Canada)</option>
                                        <option value="Atlantic/Cape_Verde">Cape Verde Islands</option>
                                        <option value="UTC">Universal Time UTC</option>
                                        <option value="Etc/Greenwich">Greenwich Mean Time</option>
                                        <option value="Europe/Belgrade">Belgrade, Bratislava, Ljubljana</option>
                                        <option value="CET">Sarajevo, Skopje, Zagreb</option>
                                        <option value="Atlantic/Reykjavik">Reykjavik</option>
                                        <option value="Europe/Dublin">Dublin</option>
                                        <option value="Europe/London">London</option>
                                        <option value="Europe/Lisbon">Lisbon</option>
                                        <option value="Africa/Casablanca">Casablanca</option>
                                        <option value="Africa/Nouakchott">Nouakchott</option>
                                        <option value="Europe/Oslo">Oslo</option>
                                        <option value="Europe/Copenhagen">Copenhagen</option>
                                        <option value="Europe/Brussels">Brussels</option>
                                        <option value="Europe/Berlin">Amsterdam, Berlin, Rome, Stockholm, Vienna</option>
                                        <option value="Europe/Helsinki">Helsinki</option>
                                        <option value="Europe/Amsterdam">Amsterdam</option>
                                        <option value="Europe/Rome">Rome</option>
                                        <option value="Europe/Stockholm">Stockholm</option>
                                        <option value="Europe/Vienna">Vienna</option>
                                        <option value="Europe/Luxembourg">Luxembourg</option>
                                        <option value="Europe/Paris">Paris</option>
                                        <option value="Europe/Zurich">Zurich</option>
                                        <option value="Europe/Madrid">Madrid</option>
                                        <option value="Africa/Bangui">West Central Africa</option>
                                        <option value="Africa/Algiers">Algiers</option>
                                        <option value="Africa/Tunis">Tunis</option>
                                        <option value="Africa/Harare">Harare, Pretoria</option>
                                        <option value="Africa/Nairobi">Nairobi</option>
                                        <option value="Europe/Warsaw">Warsaw</option>
                                        <option value="Europe/Prague">Prague Bratislava</option>
                                        <option value="Europe/Budapest">Budapest</option>
                                        <option value="Europe/Sofia">Sofia</option>
                                        <option value="Europe/Istanbul">Istanbul</option>
                                        <option value="Europe/Athens">Athens</option>
                                        <option value="Europe/Bucharest">Bucharest</option>
                                        <option value="Asia/Nicosia">Nicosia</option>
                                        <option value="Asia/Beirut">Beirut</option>
                                        <option value="Asia/Damascus">Damascus</option>
                                        <option value="Asia/Jerusalem">Jerusalem</option>
                                        <option value="Asia/Amman">Amman</option>
                                        <option value="Africa/Tripoli">Tripoli</option>
                                        <option value="Africa/Cairo">Cairo</option>
                                        <option value="Africa/Johannesburg">Johannesburg</option>
                                        <option value="Europe/Moscow">Moscow</option>
                                        <option value="Asia/Baghdad">Baghdad</option>
                                        <option value="Asia/Kuwait">Kuwait</option>
                                        <option value="Asia/Riyadh">Riyadh</option>
                                        <option value="Asia/Bahrain">Bahrain</option>
                                        <option value="Asia/Qatar">Qatar</option>
                                        <option value="Asia/Aden">Aden</option>
                                        <option value="Asia/Tehran">Tehran</option>
                                        <option value="Africa/Khartoum">Khartoum</option>
                                        <option value="Africa/Djibouti">Djibouti</option>
                                        <option value="Africa/Mogadishu">Mogadishu</option>
                                        <option value="Asia/Dubai">Dubai</option>
                                        <option value="Asia/Muscat">Muscat</option>
                                        <option value="Asia/Baku">Baku, Tbilisi, Yerevan</option>
                                        <option value="Asia/Kabul">Kabul</option>
                                        <option value="Asia/Yekaterinburg">Yekaterinburg</option>
                                        <option value="Asia/Karachi">Islamabad, Karachi</option>
                                        <option value="Asia/Calcutta">India</option>
                                        <option value="Asia/Kathmandu">Kathmandu</option>
                                        <option value="Asia/Novosibirsk">Novosibirsk</option>
                                        <option value="Asia/Almaty">Almaty</option>
                                        <option value="Asia/Dacca">Dacca</option>
                                        <option value="Asia/Krasnoyarsk">Krasnoyarsk</option>
                                        <option value="Asia/Dhaka">Astana, Dhaka</option>
                                        <option value="Asia/Bangkok">Bangkok</option>
                                        <option value="Asia/Saigon">Vietnam</option>
                                        <option value="Asia/Jakarta">Jakarta</option>
                                        <option value="Asia/Irkutsk">Irkutsk, Ulaanbaatar</option>
                                        <option value="Asia/Shanghai">Beijing, Shanghai</option>
                                        <option value="Asia/Hong_Kong">Hong Kong</option>
                                        <option value="Asia/Taipei">Taipei</option>
                                        <option value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
                                        <option value="Asia/Singapore">Singapore</option>
                                        <option value="Australia/Perth">Perth</option>
                                        <option value="Asia/Yakutsk">Yakutsk</option>
                                        <option value="Asia/Seoul">Seoul</option>
                                        <option value="Asia/Tokyo">Osaka, Sapporo, Tokyo</option>
                                        <option value="Australia/Darwin">Darwin</option>
                                        <option value="Australia/Adelaide">Adelaide</option>
                                        <option value="Asia/Vladivostok">Vladivostok</option>
                                        <option value="Pacific/Port_Moresby">Guam, Port Moresby</option>
                                        <option value="Australia/Brisbane">Brisbane</option>
                                        <option value="Australia/Sydney">Canberra, Melbourne, Sydney</option>
                                        <option value="Australia/Hobart">Hobart</option>
                                        <option value="Asia/Magadan">Magadan</option>
                                        <option value="SST">Solomon Islands</option>
                                        <option value="Pacific/Noumea">New Caledonia</option>
                                        <option value="Asia/Kamchatka">Kamchatka</option>
                                        <option value="Pacific/Fiji">Fiji Islands, Marshall Islands</option>
                                        <option value="Pacific/Auckland">Auckland, Wellington</option>
                                        <option value="Asia/Kolkata">Mumbai, Kolkata, New Delhi</option>
                                        <option value="Europe/Kiev">Kiev</option>
                                        <option value="America/Tegucigalpa">Tegucigalpa</option>
                                        <option value="Pacific/Apia">Independent State of Samoa</option>
                                    </select>

                                    </div>

                                {{-- <div class="form-group">
                                    <label class="text-dark" for="exampleInputDetails">{{ __('adminstaticword.Address') }}:</label>
                                    <textarea name="address" rows="1" class="form-control"
                                              placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} address"></textarea>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="city_id">{{ __('adminstaticword.Country') }}: </label>
                                    <select id="country_id" class="form-control select2" name="country_id">
                                        <option value="none" selected disabled hidden>
                                            {{ __('adminstaticword.Please') }} {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($countries as $coun)
                                        <option value="{{ $coun->country_id }}">{{ $coun->nicename }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="state_id">{{ __('adminstaticword.State') }}: </label>
                                    <select id="upload_id" class="form-control select2" name="state_id">
                                        <option value="
                                                ">{{ __('Please Select an Option') }}</option>
                                    </select>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="city_id">{{ __('adminstaticword.City') }}: </label>
                                    <select id="grand" class="form-control select2" name="city_id">
                                        <option value="
                                                ">{{ __('Please Select an Option') }} </option>
                                    </select>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="pin_code">{{ __('adminstaticword.Pincode') }}:</sup></label>
                                    <input value="{{ old('pin_code')}}" placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} pincode"
                                           type="text" name="pin_code" class="form-control">
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="fb_url">
                                        {{ __('adminstaticword.FacebookUrl') }}:
                                    </label>
                                    <input autofocus name="fb_url" type="text" class="form-control" placeholder="https://facebook.com" />
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label class="text-dark" for="youtube_url">
                                        {{ __('adminstaticword.YoutubeUrl') }}:
                                    </label>
                                    <input autofocus name="youtube_url" type="text" class="form-control" placeholder="https://youtube.com" />
                                </div> --}}
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="lname">
                                        {{ __('adminstaticword.LastName') }}:<sup class="text-danger">*</sup>
                                    </label>
                                    <input value="{{ old('lname')}}" required name="lname" type="text" class="form-control"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.LastName') }}" />
                                </div>

                                <div class="form-group">
                                    <label class="text-dark" for="major">{{ __('adminstaticword.Major') }}: 
                                        <sup class="text-danger">*</sup></label>
                                    <select id="major" class="form-control select2" name="major">
                                        <option value="none" selected disabled hidden>
                                            {{ __('adminstaticword.Please') }} {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($majorCategories as $major)
                                        <option value="{{ $major->slug }}">{{ $major->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                               
                                <div class="form-group">
                                    <label class="text-dark" for="mobile">{{ __('adminstaticword.Mobile') }}: <sup
                                            class="text-danger">*</sup></label><br>
                                    <input id="phone" value="{{ old('full_phone')}}" required type="tel" name="mobile" min="0"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Mobile') }}"
                                           class="form-control">
                                    <span id="valid-msg" class="d-none text-success">✓ Valid</span>
                                    <span id="error-msg" class="hide text-danger"></span>
                                </div>
                                
                                <div class="form-group">
                                    <label class="text-dark" for="role">{{ __('adminstaticword.Role') }}: <sup
                                            class="text-danger">*</sup></label>
                                    <input required type="text" name="role" value="user" readonly class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="text-dark" for="mobile">{{ __('adminstaticword.Password') }}: <sup
                                            class="text-danger">*</sup> </label>
                                    <input required type="password" name="password"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Password') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="text-dark" for="exampleInputDetails">
                                {{ __('adminstaticword.ShortInfo') }}:
                            </label>
                            <textarea id="short_info" name="short_info" rows="3" class="form-control"
                                      placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.ShortInfo') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="text-dark" for="exampleInputDetails">{{ __('adminstaticword.Detail') }}:</label>
                            <textarea id="detail" name="detail" rows="3" class="form-control"
                                      placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Detail') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}</label><br>
                            <input id="status_toggle" type="checkbox" class="custom_toggle" name="status" checked />

                        </div>
                        <div class="form-group">
                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i> {{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                {{ __('Create') }}</button>
                        </div>
                        <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

<link rel="stylesheet" href={{ url('admin_assets/intl-tel-input-18.1.6/css/intlTelInput.css') }} />

@section('scripts')
<script src="{{ url('admin_assets/intl-tel-input-18.1.6/js/intlTelInput.js') }}"></script>

<script>
    var input = document.querySelector("#phone"),
      errorMsg = document.querySelector("#error-msg"),
      validMsg = document.querySelector("#valid-msg");

    // here, the index maps to the error code returned from getValidationError - see readme
    var errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

    // initialise plugin
    var iti = window.intlTelInput(input, {
    hiddenInput: "full_phone",
    separateDialCode: true,
    utilsScript: "{{ url('admin_assets/intl-tel-input-18.1.6/js/utils.js') }}"
    });

    var reset = function() {
    input.classList.remove("error");
    errorMsg.innerHTML = "";
    errorMsg.classList.add("hide");
    validMsg.classList.add("d-none");
    };

    // on blur: validate
    input.addEventListener('blur', function() {
    reset();
    if (input.value.trim()) {
        if (iti.isValidNumber()) {
        validMsg.classList.remove("d-none");
        } else {
        input.classList.add("error");
        var errorCode = iti.getValidationError();
        errorMsg.innerHTML = errorMap[errorCode];
        errorMsg.classList.remove("hide");
        }
    }
    });

    // on keyup / change flag: reset
    input.addEventListener('change', reset);
    input.addEventListener('keyup', reset);

    (function ($) {
    "use strict";
    $('#married_status').change(function () {

    if ($(this).val() == 'Married') {
    $('#doaboxxx').show();
    } else {
    $('#doaboxxx').hide();
    }
    });
    $(function () {
    $("#dob,#doa").datepicker({
    changeYear: true,
            yearRange: "-100:+0",
            dateFormat: 'yy/mm/dd',
    });
    });
    $(function () {
    $('#country_id').change(function () {
    var up = $('#upload_id').empty();
    var cat_id = $(this).val();
    if (cat_id) {
    $.ajax({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
            type: "GET",
            url: @json(url('country/dropdown')),
            data: {
            catId: cat_id
            },
            success: function (data) {
            console.log(data);
            up.append('<option value="0">Please Choose</option>');
            $.each(data, function (id, title) {
            up.append($('<option>', {
            value: id,
                    text: title
            }));
            });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest);
            }
    });
    }
    });
    });
    $(function () {

    $('#upload_id').change(function () {
    var up = $('#grand').empty();
    var cat_id = $(this).val();
    if (cat_id) {
    $.ajax({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
            type: "GET",
            url: @json(url('country/gcity')),
            data: {
            catId: cat_id
            },
            success: function (data) {
            console.log(data);
            up.append('<option value="0">Please Choose</option>');
            $.each(data, function (id, title) {
            up.append($('<option>', {
            value: id,
                    text: title
            }));
            });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest);
            }
    });
    }
    });
    });
    })(jQuery);
</script>

@endsection