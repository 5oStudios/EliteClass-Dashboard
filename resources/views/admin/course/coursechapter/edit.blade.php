@extends('admin.layouts.master')
@section('title', __('Edit Coursechapter'))

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __('Edit Course Chapter') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <a href="{{ url('course/create/' . $cate->courses->id) }}" class="float-right btn btn-primary-rgba"><i
                        class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
            </div>
        @endslot
    @endcomponent

    <style>
        .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: transparent !important;
        }
    </style>

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
                        <h5 class="card-title">{{ __('adminstaticword.Edit') }} {{ __('Course Chapter') }}</h5>
                    </div>
                    <div class="card-body ml-2">
                        <form autocomplete="off" id="demo-form" method="post"
                            action="{{ url('coursechapter/' . $cate->id) }}"data-parsley-validate
                            class="form-horizontal form-label-left" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="d-none">
                                <label class="display-none"
                                    for="exampleInputSlug">{{ __('adminstaticword.SelectCourse') }}</label>
                                <select name="course_id" class="form-control select2">
                                    @foreach ($courses as $cou)
                                        <option value="{{ $cou->id }}"
                                            {{ $cate->courses->id == $cou->id ? 'selected' : '' }}>{{ $cou->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-9">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.ChapterName') }}:<span
                                            class="redstar">*</span>
                                        @if ($cate->type == 'live-streaming' || $cate->type == 'in-person-session')
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('readonly') }} </small>
                                        @endif
                                    </label>
                                    <input type="text" id="chapter_name" class="form-control" name="chapter_name"
                                        id="exampleInputTitle" value="{{ $cate->chapter_name }}" required
                                        @if ($cate->type == 'live-streaming' || $cate->type == 'in-person-session') readonly @endif>
                                </div>

                                @if ($cate->courses->installment == 1)
                                    <div class="col-md-3">
                                        <label for="unlock_installment">{{ __('adminstaticword.Installment') }}:<span
                                                class="text-danger">*</span></label>
                                        <select id="unlock_installment" name="unlock_installment"
                                            class="form-control select2" required>
                                            <option value="" selected disabled hidden>
                                                {{ __('Select an option') }}</option>
                                            @foreach ($installments as $i => $c)
                                                <option value="{{ ++$i }}"
                                                    {{ $cate->unlock_installment == $i ? 'selected' : '' }}>
                                                    {{ $i . ' (' . $c->amount . ')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-4 @if (!$cate->type) d-none @endif">
                                <div class="col-md-6">
                                    <label>{{ __('Session Type') }}: <span class="text-danger">*</span> <small
                                            class="text-muted"><i class="fa fa-question-circle"></i> {{ __('readonly') }}
                                        </small></label>
                                    <select class="form-control select2 session_type">
                                        <option value="live-streaming"
                                            {{ $cate->type == 'live-streaming' ? 'selected' : '' }}>
                                            {{ __('Live Streaming') }}</option>
                                        <option value="in-person-session"
                                            {{ $cate->type == 'in-person-session' ? 'selected' : '' }}>
                                            {{ __('In-Person Session') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                @if ($cate->type == 'live-streaming')
                                    <div class="col-md-6 @if ($cate->type != 'live-streaming') d-none @endif">
                                        <label>{{ __('Live Streaming') }}: <span class="text-danger">*</span></label>
                                        <select id="selected_meeting" class="form-control select2" name="type_id">
                                            @foreach ($bbl_meetings as $bbl)
                                                <option value="{{ $bbl }}"
                                                    {{ $cate->type_id == $bbl->id ? 'selected' : '' }}>
                                                    {{ $bbl->meetingname }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                @elseif($cate->type == 'in-person-session')
                                    <div class="col-md-6 @if ($cate->type != 'in-person-session') d-none @endif">
                                        <label>{{ __('In-Person Session') }}: <span class="text-danger">*</span></label>
                                        <select id="selected_session" class="form-control select2" name="type_id">
                                            @foreach ($offline_sessions as $session)
                                                <option value="{{ $session }}"
                                                    {{ $cate->type_id == $session->id ? 'selected' : '' }}>
                                                    {{ $session->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            {{-- <div class="col-md-6">
                                <label for="exampleInputDetails">{{ __('adminstaticword.LearningMaterial') }} :</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="file">{{ __('Upload') }}</span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                            aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label"
                                            for="inputGroupFile01">{{ __('Choose file') }}</label>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('Detail') }}: <span class="redstar">*</span>
                                    </label>
                                    <textarea rows="3" placeholder="{{ __('Write something here...') }}" class="form-control" name="detail"></textarea>
                                </div>
                            </div>
                            <br> --}}

                            <div class="row mb-4">
                                <div class="col-md-6" id="purchasable"
                                    style="{{ $cate->type == 'live-streaming' || $cate->type == 'in-person-session' ? 'display:none;' : '' }}">
                                    <label for="purchasable">{{ __('Price or Not') }}:<span
                                            class="redstar">*</span></label><br>
                                    <select id="is_purchasable" name="is_purchasable" class="form-control select2" required>
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}</option>
                                        <option value="1" {{ $cate->is_purchasable == '1' ? 'selected' : '' }}>
                                            {{ __('Price') }} </option>
                                        <option value="0" {{ $cate->is_purchasable == '0' ? 'selected' : '' }}>
                                            {{ __('Without Price') }} </option>
                                    </select>
                                </div>
                                <div id="pricebox" class="col-md-4"
                                    style="{{ $cate->is_purchasable == '1' ? '' : 'display:none;' }}">
                                    <label for="exampleInputTitle">{{ __('Price') }}: <span class="redstar">*</span>
                                        @if ($cate->type == 'live-streaming' || $cate->type == 'in-person-session')
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('readonly') }} </small>
                                        @endif
                                    </label>
                                    <input type="number" min="0" step="0.001" id="price" class="form-control"
                                        name="price" value="{{ $cate->price }}" required
                                        @if ($cate->type == 'live-streaming' || $cate->type == 'in-person-session') readonly @endif>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status"
                                            {{ $cate->status == '1' ? 'checked' : '' }} />
                                        <span class="knob"></span>
                                    </label>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
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
@endsection


@section('script')
    <!--courseclass.js is included -->

    <script>
        $(function() {
            $(".session_type").select2({
                disabled: 'readonly'
            });
        });
    </script>
@endsection
