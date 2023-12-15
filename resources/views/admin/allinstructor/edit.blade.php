@extends('admin.layouts.master')
@section('title','Edit Instructor')
@section('maincontent')

@component('components.breadcumb',['thirdactive' => 'active'])

@slot('heading')
{{ __('Home') }}
@endslot

@slot('menu1')
{{ __('Admin') }}
@endslot

@slot('menu2')
{{ __(' Edit Instructor') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
    <a href="{{ route('allinstructor.index') }}" class="float-right btn btn-primary-rgba mr-2"><i
            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
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
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="box-title">{{ __('adminstaticword.Edit') }} {{ __('adminstaticword.Instructor') }}</h5>
                        </div>
                        <!-- language start -->
                        @php
                        $languages = App\Language::all(); 
                        @endphp
                        <div class="col-md-6">
                            <li class="list-inline-item {{ (app()->getLocale() == 'en')? 'pull-right' : 'pull-left' }}">
                                <div class="languagebar">
                                    <div class="dropdown">
                                    <a class="dropdown-toggle" href="#" role="button" id="languagelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="live-icon"> {{__('Selected Language')}} ({{Session::has('changed_language') ? Session::get('changed_language') : ''}})</span></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languagelink">
                                            @if (isset($languages) && count($languages) > 0)
                                            @foreach ($languages as $language)
                                            <a class="dropdown-item" href="{{ route('languageSwitch', $language->local) }}">
                                                <i class="feather icon-globe"></i>
                                                {{$language->name}} ({{$language->local}})</a>
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
                <div class="card-body ml-2">
                    <form autocomplete="off" action="{{ route('allinstructor.update',$user->id) }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">
                                        {{ __('adminstaticword.FirstName') }}:
                                        <sup class="text-danger">*</sup>
                                    </label>
                                    <input value="{{ $user->fname }}" autofocus required name="fname" type="text" class="form-control"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.FirstName') }}" />
                                </div>
                                
                                <div class="form-group">
                                    <label for="mobile">{{ __('adminstaticword.Email') }}:<sup class="text-danger">*</sup> </label>
                                    <input value="{{ $user->email }}" required type="email" name="email"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Email') }}"
                                           class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="text-dark" for="city_id">{{ __('adminstaticword.Country') }}: <sup class="text-danger">*</sup></label>
                                    <select id="main_category" class="form-control select2" name="main_category" required>
                                        <option value="" selected>
                                            {{ __('adminstaticword.Please') }} {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        @foreach ($categories as $caat)
                                        <option {{ $user->main_category == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('TimeZone') }}: <sup class="redstar">*</sup></label>
                                    <select class="form-control select2" name="timezone" required>
                                        <option value="">{{ __('Choose your timezone') }}</option>
                                        <option {{ ($user->timezone == 'Pacific/Midway') ? 'selected' : '' }} value="Pacific/Midway">Midway Island, Samoa</option>
                                        <option {{ ($user->timezone == 'Pacific/Pago_Pago') ? 'selected' : '' }} value="Pacific/Pago_Pago">Pago Pago</option>
                                        <option {{ ($user->timezone == 'Pacific/Honolulu') ? 'selected' : '' }} value="Pacific/Honolulu">Hawaii</option>
                                        <option {{ ($user->timezone == 'America/Anchorage') ? 'selected' : '' }} value="America/Anchorage">Alaska</option>
                                        <option {{ ($user->timezone == 'America/Vancouver') ? 'selected' : '' }} value="America/Vancouver">Vancouver</option>
                                        <option {{ ($user->timezone == 'America/Los_Angeles') ? 'selected' : '' }} value="America/Los_Angeles">Pacific Time (US and Canada)</option>
                                        <option {{ ($user->timezone == 'America/Tijuana') ? 'selected' : '' }} value="America/Tijuana">Tijuana</option>
                                        <option {{ ($user->timezone == 'America/Edmonton') ? 'selected' : '' }} value="America/Edmonton">Edmonton</option>
                                        <option {{ ($user->timezone == 'America/Denver') ? 'selected' : '' }} value="America/Denver">Mountain Time (US and Canada)</option>
                                        <option {{ ($user->timezone == 'America/Phoenix') ? 'selected' : '' }} value="America/Phoenix">Arizona</option>
                                        <option {{ ($user->timezone == 'America/Mazatlan') ? 'selected' : '' }} value="America/Mazatlan">Mazatlan</option>
                                        <option {{ ($user->timezone == 'America/Winnipeg') ? 'selected' : '' }} value="America/Winnipeg">Winnipeg</option>
                                        <option {{ ($user->timezone == 'America/Regina') ? 'selected' : '' }} value="America/Regina">Saskatchewan</option>
                                        <option {{ ($user->timezone == 'America/Chicago') ? 'selected' : '' }} value="America/Chicago">Central Time (US and Canada)</option>
                                        <option {{ ($user->timezone == 'America/Mexico_City') ? 'selected' : '' }} value="America/Mexico_City">Mexico City</option>
                                        <option {{ ($user->timezone == 'America/Guatemala') ? 'selected' : '' }} value="America/Guatemala">Guatemala</option>
                                        <option {{ ($user->timezone == 'America/El_Salvador') ? 'selected' : '' }} value="America/El_Salvador">El Salvador</option>
                                        <option {{ ($user->timezone == 'America/Managua') ? 'selected' : '' }} value="America/Managua">Managua</option>
                                        <option {{ ($user->timezone == 'America/Costa_Rica') ? 'selected' : '' }} value="America/Costa_Rica">Costa Rica</option>
                                        <option {{ ($user->timezone == 'America/Montreal') ? 'selected' : '' }} value="America/Montreal">Montreal</option>
                                        <option {{ ($user->timezone == 'America/New_York') ? 'selected' : '' }} value="America/New_York">Eastern Time (US and Canada)</option>
                                        <option {{ ($user->timezone == 'America/Indianapolis') ? 'selected' : '' }} value="America/Indianapolis">Indiana (East)</option>
                                        <option {{ ($user->timezone == 'America/Panama') ? 'selected' : '' }} value="America/Panama">Panama</option>
                                        <option {{ ($user->timezone == 'America/Bogota') ? 'selected' : '' }} value="America/Bogota">Bogota</option>
                                        <option {{ ($user->timezone == 'America/Lima') ? 'selected' : '' }} value="America/Lima">Lima</option>
                                        <option {{ ($user->timezone == 'America/Halifax') ? 'selected' : '' }} value="America/Halifax">Halifax</option>
                                        <option {{ ($user->timezone == 'America/Puerto_Rico') ? 'selected' : '' }} value="America/Puerto_Rico">Puerto Rico</option>
                                        <option {{ ($user->timezone == 'America/Caracas') ? 'selected' : '' }} value="America/Caracas">Caracas</option>
                                        <option {{ ($user->timezone == 'America/Santiago') ? 'selected' : '' }} value="America/Santiago">Santiago</option>
                                        <option {{ ($user->timezone == 'America/St_Johns') ? 'selected' : '' }} value="America/St_Johns">Newfoundland and Labrador</option>
                                        <option {{ ($user->timezone == 'America/Montevideo') ? 'selected' : '' }} value="America/Montevideo">Montevideo</option>
                                        <option {{ ($user->timezone == 'America/Araguaina') ? 'selected' : '' }} value="America/Araguaina">Brasilia</option>
                                        <option {{ ($user->timezone == 'America/Argentina/Buenos_Aires') ? 'selected' : '' }} value="America/Argentina/Buenos_Aires">Buenos Aires, Georgetown</option>
                                        <option {{ ($user->timezone == 'America/Godthab') ? 'selected' : '' }} value="America/Godthab">Greenland</option>
                                        <option {{ ($user->timezone == 'America/Sao_Paulo') ? 'selected' : '' }} value="America/Sao_Paulo">Sao Paulo</option>
                                        <option {{ ($user->timezone == 'Atlantic/Azores') ? 'selected' : '' }} value="Atlantic/Azores">Azores</option>
                                        <option {{ ($user->timezone == 'Canada/Atlantic') ? 'selected' : '' }} value="Canada/Atlantic">Atlantic Time (Canada)</option>
                                        <option {{ ($user->timezone == 'Atlantic/Cape_Verde') ? 'selected' : '' }} value="Atlantic/Cape_Verde">Cape Verde Islands</option>
                                        <option {{ ($user->timezone == 'UTC') ? 'selected' : '' }} value="UTC">Universal Time UTC</option>
                                        <option {{ ($user->timezone == 'Etc/Greenwich') ? 'selected' : '' }} value="Etc/Greenwich">Greenwich Mean Time</option>
                                        <option {{ ($user->timezone == 'Europe/Belgrade') ? 'selected' : '' }} value="Europe/Belgrade">Belgrade, Bratislava, Ljubljana</option>
                                        <option {{ ($user->timezone == 'CET') ? 'selected' : '' }} value="CET">Sarajevo, Skopje, Zagreb</option>
                                        <option {{ ($user->timezone == 'Atlantic/Reykjavik') ? 'selected' : '' }} value="Atlantic/Reykjavik">Reykjavik</option>
                                        <option {{ ($user->timezone == 'Europe/Dublin') ? 'selected' : '' }} value="Europe/Dublin">Dublin</option>
                                        <option {{ ($user->timezone == 'Europe/London') ? 'selected' : '' }} value="Europe/London">London</option>
                                        <option {{ ($user->timezone == 'Europe/Lisbon') ? 'selected' : '' }} value="Europe/Lisbon">Lisbon</option>
                                        <option {{ ($user->timezone == 'Africa/Casablanca') ? 'selected' : '' }} value="Africa/Casablanca">Casablanca</option>
                                        <option {{ ($user->timezone == 'Africa/Nouakchott') ? 'selected' : '' }} value="Africa/Nouakchott">Nouakchott</option>
                                        <option {{ ($user->timezone == 'Europe/Oslo') ? 'selected' : '' }} value="Europe/Oslo">Oslo</option>
                                        <option {{ ($user->timezone == 'Europe/Copenhagen') ? 'selected' : '' }} value="Europe/Copenhagen">Copenhagen</option>
                                        <option {{ ($user->timezone == 'Europe/Brussels') ? 'selected' : '' }} value="Europe/Brussels">Brussels</option>
                                        <option {{ ($user->timezone == 'Europe/Berlin') ? 'selected' : '' }} value="Europe/Berlin">Amsterdam, Berlin, Rome, Stockholm, Vienna</option>
                                        <option {{ ($user->timezone == 'Europe/Helsinki') ? 'selected' : '' }} value="Europe/Helsinki">Helsinki</option>
                                        <option {{ ($user->timezone == 'Europe/Amsterdam') ? 'selected' : '' }} value="Europe/Amsterdam">Amsterdam</option>
                                        <option {{ ($user->timezone == 'Europe/Rome') ? 'selected' : '' }} value="Europe/Rome">Rome</option>
                                        <option {{ ($user->timezone == 'Europe/Stockholm') ? 'selected' : '' }} value="Europe/Stockholm">Stockholm</option>
                                        <option {{ ($user->timezone == 'Europe/Vienna') ? 'selected' : '' }} value="Europe/Vienna">Vienna</option>
                                        <option {{ ($user->timezone == 'Europe/Luxembourg') ? 'selected' : '' }} value="Europe/Luxembourg">Luxembourg</option>
                                        <option {{ ($user->timezone == 'Europe/Paris') ? 'selected' : '' }} value="Europe/Paris">Paris</option>
                                        <option {{ ($user->timezone == 'Europe/Zurich') ? 'selected' : '' }} value="Europe/Zurich">Zurich</option>
                                        <option {{ ($user->timezone == 'Europe/Madrid') ? 'selected' : '' }} value="Europe/Madrid">Madrid</option>
                                        <option {{ ($user->timezone == 'Africa/Bangui') ? 'selected' : '' }} value="Africa/Bangui">West Central Africa</option>
                                        <option {{ ($user->timezone == 'Africa/Algiers') ? 'selected' : '' }} value="Africa/Algiers">Algiers</option>
                                        <option {{ ($user->timezone == 'Africa/Tunis') ? 'selected' : '' }} value="Africa/Tunis">Tunis</option>
                                        <option {{ ($user->timezone == 'Africa/Harare') ? 'selected' : '' }} value="Africa/Harare">Harare, Pretoria</option>
                                        <option {{ ($user->timezone == 'Africa/Nairobi') ? 'selected' : '' }} value="Africa/Nairobi">Nairobi</option>
                                        <option {{ ($user->timezone == 'Europe/Warsaw') ? 'selected' : '' }} value="Europe/Warsaw">Warsaw</option>
                                        <option {{ ($user->timezone == 'Europe/Prague') ? 'selected' : '' }} value="Europe/Prague">Prague Bratislava</option>
                                        <option {{ ($user->timezone == 'Europe/Budapest') ? 'selected' : '' }} value="Europe/Budapest">Budapest</option>
                                        <option {{ ($user->timezone == 'Europe/Sofia') ? 'selected' : '' }} value="Europe/Sofia">Sofia</option>
                                        <option {{ ($user->timezone == 'Europe/Istanbul') ? 'selected' : '' }} value="Europe/Istanbul">Istanbul</option>
                                        <option {{ ($user->timezone == 'Europe/Athens') ? 'selected' : '' }} value="Europe/Athens">Athens</option>
                                        <option {{ ($user->timezone == 'Europe/Bucharest') ? 'selected' : '' }} value="Europe/Bucharest">Bucharest</option>
                                        <option {{ ($user->timezone == 'Asia/Nicosia') ? 'selected' : '' }} value="Asia/Nicosia">Nicosia</option>
                                        <option {{ ($user->timezone == 'Asia/Beirut') ? 'selected' : '' }} value="Asia/Beirut">Beirut</option>
                                        <option {{ ($user->timezone == 'Asia/Damascus') ? 'selected' : '' }} value="Asia/Damascus">Damascus</option>
                                        <option {{ ($user->timezone == 'Asia/Jerusalem') ? 'selected' : '' }} value="Asia/Jerusalem">Jerusalem</option>
                                        <option {{ ($user->timezone == 'Asia/Amman') ? 'selected' : '' }} value="Asia/Amman">Amman</option>
                                        <option {{ ($user->timezone == 'Africa/Tripoli') ? 'selected' : '' }} value="Africa/Tripoli">Tripoli</option>
                                        <option {{ ($user->timezone == 'Africa/Cairo') ? 'selected' : '' }} value="Africa/Cairo">Cairo</option>
                                        <option {{ ($user->timezone == 'Africa/Johannesburg') ? 'selected' : '' }} value="Africa/Johannesburg">Johannesburg</option>
                                        <option {{ ($user->timezone == 'Europe/Moscow') ? 'selected' : '' }} value="Europe/Moscow">Moscow</option>
                                        <option {{ ($user->timezone == 'Asia/Baghdad') ? 'selected' : '' }} value="Asia/Baghdad">Baghdad</option>
                                        <option {{ ($user->timezone == 'Asia/Kuwait') ? 'selected' : '' }} value="Asia/Kuwait">Kuwait</option>
                                        <option {{ ($user->timezone == 'Asia/Riyadh') ? 'selected' : '' }} value="Asia/Riyadh">Riyadh</option>
                                        <option {{ ($user->timezone == 'Asia/Bahrain') ? 'selected' : '' }} value="Asia/Bahrain">Bahrain</option>
                                        <option {{ ($user->timezone == 'Asia/Qatar') ? 'selected' : '' }} value="Asia/Qatar">Qatar</option>
                                        <option {{ ($user->timezone == 'Asia/Aden') ? 'selected' : '' }} value="Asia/Aden">Aden</option>
                                        <option {{ ($user->timezone == 'Asia/Tehran') ? 'selected' : '' }} value="Asia/Tehran">Tehran</option>
                                        <option {{ ($user->timezone == 'Africa/Khartoum') ? 'selected' : '' }} value="Africa/Khartoum">Khartoum</option>
                                        <option {{ ($user->timezone == 'Africa/Djibouti') ? 'selected' : '' }} value="Africa/Djibouti">Djibouti</option>
                                        <option {{ ($user->timezone == 'Africa/Mogadishu') ? 'selected' : '' }} value="Africa/Mogadishu">Mogadishu</option>
                                        <option {{ ($user->timezone == 'Asia/Dubai') ? 'selected' : '' }} value="Asia/Dubai">Dubai</option>
                                        <option {{ ($user->timezone == 'Asia/Muscat') ? 'selected' : '' }} value="Asia/Muscat">Muscat</option>
                                        <option {{ ($user->timezone == 'Asia/Baku') ? 'selected' : '' }} value="Asia/Baku">Baku, Tbilisi, Yerevan</option>
                                        <option {{ ($user->timezone == 'Asia/Kabul') ? 'selected' : '' }} value="Asia/Kabul">Kabul</option>
                                        <option {{ ($user->timezone == 'Asia/Yekaterinburg') ? 'selected' : '' }} value="Asia/Yekaterinburg">Yekaterinburg</option>
                                        <option {{ ($user->timezone == 'Asia/Karachi') ? 'selected' : '' }} value="Asia/Karachi">Islamabad, Karachi</option>
                                        <option {{ ($user->timezone == 'Asia/Calcutta') ? 'selected' : '' }} value="Asia/Calcutta">India</option>
                                        <option {{ ($user->timezone == 'Asia/Kathmandu') ? 'selected' : '' }} value="Asia/Kathmandu">Kathmandu</option>
                                        <option {{ ($user->timezone == 'Asia/Novosibirsk') ? 'selected' : '' }} value="Asia/Novosibirsk">Novosibirsk</option>
                                        <option {{ ($user->timezone == 'Asia/Almaty') ? 'selected' : '' }} value="Asia/Almaty">Almaty</option>
                                        <option {{ ($user->timezone == 'Asia/Dacca') ? 'selected' : '' }} value="Asia/Dacca">Dacca</option>
                                        <option {{ ($user->timezone == 'Asia/Krasnoyarsk') ? 'selected' : '' }} value="Asia/Krasnoyarsk">Krasnoyarsk</option>
                                        <option {{ ($user->timezone == 'Asia/Dhaka') ? 'selected' : '' }} value="Asia/Dhaka">Astana, Dhaka</option>
                                        <option {{ ($user->timezone == 'Asia/Bangkok') ? 'selected' : '' }} value="Asia/Bangkok">Bangkok</option>
                                        <option {{ ($user->timezone == 'Asia/Saigon') ? 'selected' : '' }} value="Asia/Saigon">Vietnam</option>
                                        <option {{ ($user->timezone == 'Asia/Jakarta') ? 'selected' : '' }} value="Asia/Jakarta">Jakarta</option>
                                        <option {{ ($user->timezone == 'Asia/Irkutsk') ? 'selected' : '' }} value="Asia/Irkutsk">Irkutsk, Ulaanbaatar</option>
                                        <option {{ ($user->timezone == 'Asia/Shanghai') ? 'selected' : '' }} value="Asia/Shanghai">Beijing, Shanghai</option>
                                        <option {{ ($user->timezone == 'Asia/Hong_Kong') ? 'selected' : '' }} value="Asia/Hong_Kong">Hong Kong</option>
                                        <option {{ ($user->timezone == 'Asia/Taipei') ? 'selected' : '' }} value="Asia/Taipei">Taipei</option>
                                        <option {{ ($user->timezone == 'Asia/Kuala_Lumpur') ? 'selected' : '' }} value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
                                        <option {{ ($user->timezone == 'Asia/Singapore') ? 'selected' : '' }} value="Asia/Singapore">Singapore</option>
                                        <option {{ ($user->timezone == 'Australia/Perth') ? 'selected' : '' }} value="Australia/Perth">Perth</option>
                                        <option {{ ($user->timezone == 'Asia/Yakutsk') ? 'selected' : '' }} value="Asia/Yakutsk">Yakutsk</option>
                                        <option {{ ($user->timezone == 'Asia/Seoul') ? 'selected' : '' }} value="Asia/Seoul">Seoul</option>
                                        <option {{ ($user->timezone == 'Asia/Tokyo') ? 'selected' : '' }} value="Asia/Tokyo">Osaka, Sapporo, Tokyo</option>
                                        <option {{ ($user->timezone == 'Australia/Darwin') ? 'selected' : '' }} value="Australia/Darwin">Darwin</option>
                                        <option {{ ($user->timezone == 'Australia/Adelaid') ? 'selected' : '' }} value="Australia/Adelaide">Adelaide</option>
                                        <option {{ ($user->timezone == 'Asia/Vladivostok') ? 'selected' : '' }} value="Asia/Vladivostok">Vladivostok</option>
                                        <option {{ ($user->timezone == 'Pacific/Port_Moresby') ? 'selected' : '' }} value="Pacific/Port_Moresby">Guam, Port Moresby</option>
                                        <option {{ ($user->timezone == 'Australia/Brisbane') ? 'selected' : '' }} value="Australia/Brisbane">Brisbane</option>
                                        <option {{ ($user->timezone == 'Australia/Sydney') ? 'selected' : '' }} value="Australia/Sydney">Canberra, Melbourne, Sydney</option>
                                        <option {{ ($user->timezone == 'Australia/Hobart') ? 'selected' : '' }} value="Australia/Hobart">Hobart</option>
                                        <option {{ ($user->timezone == 'Asia/Magadan') ? 'selected' : '' }} value="Asia/Magadan">Magadan</option>
                                        <option {{ ($user->timezone == 'SST') ? 'selected' : '' }} value="SST">Solomon Islands</option>
                                        <option {{ ($user->timezone == 'Pacific/Noumea') ? 'selected' : '' }} value="Pacific/Noumea">New Caledonia</option>
                                        <option {{ ($user->timezone == 'Asia/Kamchatka') ? 'selected' : '' }} value="Asia/Kamchatka">Kamchatka</option>
                                        <option {{ ($user->timezone == 'Pacific/Fiji') ? 'selected' : '' }} value="Pacific/Fiji">Fiji Islands, Marshall Islands</option>
                                        <option {{ ($user->timezone == 'Pacific/Auckland') ? 'selected' : '' }} value="Pacific/Auckland">Auckland, Wellington</option>
                                        <option {{ ($user->timezone == 'Asia/Kolkata') ? 'selected' : '' }} value="Asia/Kolkata">Mumbai, Kolkata, New Delhi</option>
                                        <option {{ ($user->timezone == 'Europe/Kiev') ? 'selected' : '' }} value="Europe/Kiev">Kiev</option>
                                        <option {{ ($user->timezone == 'America/Tegucigalpa') ? 'selected' : '' }} value="America/Tegucigalpa">Tegucigalpa</option>
                                        <option {{ ($user->timezone == 'Pacific/Apia') ? 'selected' : '' }} value="Pacific/Apia">Independent State of Samoa</option>
                                    </select>

                                </div>
                                <!-- <div class="form-group">
                                    <label for="address">{{ __('adminstaticword.Address') }}: </label>
                                    <textarea name="address" class="form-control" rows="1"
                                              placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} adderss" value="">{{ $user->address }}</textarea>
                                </div> -->
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">
                                        {{ __('adminstaticword.LastName') }}:
                                        <sup class="text-danger">*</sup>
                                    </label>
                                    <input value="{{ $user->lname }}" required name="lname" type="text" class="form-control"
                                           placeholder=" {{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.LastName') }}" />
                                </div>

                                <div class="form-group">
                                    <label for="mobile"> {{ __('adminstaticword.Mobile') }}: <sup class="text-danger">*</sup></label><br>
                                    <input id="phone" value="{{ $user->mobile }}" type="tel" name="mobile" min="0"
                                           placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Mobile') }}"
                                           class="form-control" required>
                                    <span id="valid-msg" class="d-none text-success">✓ Valid</span>
                                    <span id="error-msg" class="hide text-danger"></span>
                                </div>
                                <div class="form-group">
                                    <label for="role">{{ __('adminstaticword.SelectRole') }}: <sup class="text-danger">*</sup></label>
                                    <input required type="text" name="role" value="instructor" readonly class="form-control">

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="text-dark" for="exampleInputDetails">
                                {{ __('adminstaticword.ShortInfo') }}:
                                <sup class="text-danger">*</sup>
                            </label>
                            <textarea id="short_info" name="short_info" rows="3" class="form-control"
                                        placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.ShortInfo') }}"
                                        required>{{ $user->short_info }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="detail">{{ __('adminstaticword.Detail') }}:</label>
                            <textarea id="detail" name="detail" class="form-control" rows="5"
                                        placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Detail') }}"
                                        value="" >{{ $user->detail }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="exampleInputDetails">{{ __('adminstaticword.Verified') }}:</label><br>
                                <input id="verified" type="checkbox" class="custom_toggle" name="verified"
                                       {{  $user->email_verified_at != NULL ? 'checked' : '' }} />
                            </div>
                            <div class="col-md-6">
                                <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
                                <input type="checkbox" class="custom_toggle" name="status"
                                       {{ $user->status == '1' ? 'checked' : '' }} />
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-6">
                                <label>{{ __('Preview Image') }}:</label>
                                <small class="text-muted"><i class="fa fa-question-circle"></i>
                                    {{ __('adminstaticword.Recommendedsize') }} (1375 x 409px)</small>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01" name="user_img"
                                                aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label" for="inputGroupFile01">{{ $user->user_img?? __('Choose file') }}</label>
                                    </div>
                                </div>
                                @if($user->user_img != null || $user->user_img !='')
                                <div class="edit-user-img">
                                    <img src="{{ url('/images/user_img/'.$user->user_img) }}"  alt="User Image" class="img-responsive image_size">
                                </div>
                                @else
                                <div class="edit-user-img">
                                    <img src="{{ asset('images/default/user.jpg')}}"  alt="User Image" class="img-responsive img-circle">
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="update-password">
                                    <label for="box1"> {{ __('adminstaticword.UpdatePassword') }}:</label>
                                    <input type="checkbox" id="myCheck" name="update_pass" class="custom_toggle" onclick="myFunction()">
                                </div>
                                    
                                <div style="display: none" id="update-password">
                                    <div class="form-group">
                                        <label>{{ __('adminstaticword.Password') }}</label>
                                        <input type="password" name="password" class="form-control"
                                                placeholder="{{ __('adminstaticword.Please') }} {{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Password') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="form-group">
                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                {{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                {{ __('Update') }}</button>
                        </div>

                        <div class="clear-both"></div>
                    </form>
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

</script>

<script>

    (function ($) {
    "use strict";
    $(function () {
    $("#dob,#doa").datepicker({
    changeYear: true,
            yearRange: "-100:+0",
            dateFormat: 'yy/mm/dd',
    });
    });
    $('#married_status').change(function () {

    if ($(this).val() == 'Married') {
    $('#doaboxxx').show();
    } else {
    $('#doaboxxx').hide();
    }
    });
    $(function () {
    var urlLike = '{{ url('
            country / dropdown ') }}';
    $('#country_id').change(function () {
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
    var urlLike = '{{ url('
            country / gcity ') }}';
    $('#upload_id').change(function () {
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
    })(jQuery);</script>


<script>
    (function($) {
    "use strict";
    $(function(){
    $('#myCheck').change(function(){
    if ($('#myCheck').is(':checked')){
    $('#update-password').show('fast');
    } else{
    $('#update-password').hide('fast');
    }
    });
    });
    })(jQuery);
</script>

@endsection