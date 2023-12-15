@extends('admin.layouts.master')
@section('title', __('Edit Courseclass'))
@section('maincontent')

    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __('Edit Course Class') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('chapterclasses', $cate->coursechapter_id) }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>

                    {{-- <a href="http://www.webdesign-flash.ro/vs/" target="_blank" class="float-right btn btn-primary-rgba mr-2" ><i class="feather icon-navigation mr-2"></i>{{ __('Encrypt Link') }}</a> --}}
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-8">
                @if (Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert alert-danger') }}">{{ Session::get('message') }}
                    </p>
                @endif

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
                        <h5 class="box-title">{{ __('adminstaticword.Edit') }} {{ __('Course Class') }}</h5>
                    </div>
                    <div class="card-body ml-2">
                        <form autocomplete="off" enctype="multipart/form-data" id="demo-form" method="post"
                            action="{{ url('courseclass/' . $cate->id) }}" data-parsley-validate
                            class="form-horizontal form-label-left">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="type">{{ __('adminstaticword.CourseChapter') }}: <small
                                            class="text-muted"><i class="fa fa-question-circle"></i> {{ __('readonly') }}
                                        </small></label>
                                    <input type="text" readonly value="{{ $coursechapt->chapter_name }}"
                                        class="form-control" />
                                </div>
                            </div>
                            <br>

                            {{-- <div class="row" style="{{($cate->type == 'meeting' || $cate->type == 'quiz')? 'display:none': ''}}"> --}}
                            <div class="row" style="{{ $cate->type == 'quiz' ? 'display:none' : '' }}">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Title') }}:<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control " name="title" id="exampleInputTitle"
                                        value="{{ $cate->title }}">
                                </div>
                            </div>
                            <br>

                            {{-- <div class="row" id="description" style="{{($cate->type == 'meeting' || $cate->type == 'quiz')? 'display:none': ''}}">
                            <div class="col-md-12">
                                <label for="exampleInputDetails">{{ __('adminstaticword.Detail') }}:</label>
                                <textarea id="details" name="detail" rows="5"  class="form-control" placeholder="Enter Your Details">{{ $cate->detail }}</textarea>
                            </div>
                        </div>
                        <br> --}}

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="type">{{ __('adminstaticword.Type') }}:<span
                                            class="text-danger">*</span></label>
                                    <select name="type" id="filetype" class="form-control" required>
                                        {{-- @if ($cate->type == 'meeting')
                                    <option value="{{ $cate->type }}">{{ __('live streaming') }}</option>
                                @elseif($cate->type == 'offline_session')
                                    <option value="{{ $cate->type }}">{{ __('offline session') }}</option>
                                @else --}}
                                        <option value="{{ $cate->type }}">{{ $cate->type }}</option>
                                        {{-- @endif --}}
                                    </select>
                                </div>
                            </div>
                            <br>

                            {{-- @if ($cate->type == 'meeting')
                        <div class="row">
                            <div class="col-md-12" id="meeting">
                                <div class="form-group">
                                    <label>{{__('Select Live Streaming')}}: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="meeting_id" required>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($bbl_meetings as $bbl)
                                        <option {{$cate->meeting_id == $bbl->id? 'selected': ''}} value="{{ $bbl->id }}">{{ $bbl->meetingname }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        @endif --}}

                            {{-- @if ($cate->type == 'offline_session')
                        <div class="row">
                            <div class="col-md-12" id="offline_session">
                                <div class="form-group">
                                    <label>{{__('Select Offline Session')}}: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="offline_session" required>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($offline_sessions as $session)
                                        <option {{$cate->offline_session_id == $session->id? 'selected': ''}} value="{{ $session->id }}">{{ $session->title }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        @endif --}}

                            @if ($cate->type == 'video')
                                <div class="row">
                                    <div id="change-video" class="col-md-12 mb-4">
                                        <button onclick="$('#myvimeoModal').modal('show')" type="button"
                                            class="btn btn-default form-control">Change Video <i
                                                class="fa fa-refresh"></i></button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div id="bunnycdn-videos" class="col-md-12 mb-4">
                                        <button onclick="$('#bunnycdnModal').modal('show')" type="button"
                                            class="btn btn-warning form-control">TESTING BunnyCDN Videos <i
                                                class="fa fa-refresh"></i></button>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="iframeURLBox">
                                            <input class="video_url" type="hidden" name="video_url"
                                                value="{{ $cate->video_url }}" />

                                            <label for="">{{ __('adminstaticword.Content') }}: <span
                                                    class="text-danger">*</span></label>
                                            <textarea id="detail" name="iframe_url" rows="3" class="form-control">{{ $cate->iframe_url }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-12" id="duration">
                                        <label for="">{{ __('adminstaticword.Duration') }}: <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="duration" min="0" required
                                            value="{{ $cate->duration }}" class="form-control"
                                            placeholder="{{ __('Enter class duration in (mins) Eg:160') }}">
                                    </div>
                                </div>
                                <br>
                            @endif

                            @if ($cate->type == 'text')
                                <!--text-->
                                <div class="row">
                                    <div class="col-md-12 form-group" id="long_text">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Text') }}: <span
                                                class="text-danger">*</span></label>
                                        <textarea id="detail" name="long_text" rows="3" class="form-control">{{ $cate->long_text }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12" id="duration">
                                            <label for="">{{ __('adminstaticword.Duration') }}: <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="duration" min="0" required
                                                value="{{ $cate->duration }}" class="form-control"
                                                placeholder="{{ __('Enter class duration in (mins) Eg:160') }}">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($cate->type == 'quiz')
                                <!--text-->
                                <div class="form-group">
                                    <label>Select Quiz: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="url" required
                                        placeholder="{{ __('adminstaticword.SelectQuiz') }}">
                                        @foreach ($topics as $q)
                                            <option @if ($q->id == $cate->url) selected @endif
                                                value="{{ $q->id }}">{{ $q->title }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            @endif

                            {{-- @if ($cate->type == 'image')
                            <div class="col-md-7" id="imagetype">
                                <input type="radio" name="checkImage" id="ch3" value="url"
                                    {{ $cate->url != '' ? 'checked' : '' }}> {{ __('adminstaticword.URL') }}
                                <input type="radio" name="checkImage" id="ch4"
                                    {{ $cate->image != '' ? 'checked' : '' }} value="uploadimage">
                                {{ __('adminstaticword.UploadImage') }}
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div id="imageURL" @if ($cate->image != '') class="display-none" @endif>
                                        <label for="">{{ __('adminstaticword.URL') }}: </label>
                                        <input type="text" value="{{ $cate->url }}" name="imgurl"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div id="imageUpload"
                                        @if ($cate->url != '') class="display-none" @endif>
                                        <label for="">{{ __('adminstaticword.UploadImage') }}:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                    name="image" aria-describedby="inputGroupFileAddon01">
                                                <label class="custom-file-label"
                                                    for="inputGroupFile01">{{ __('Choose file') }}</label>
                                            </div>
                                        </div>

                                        <br>
                                        @if ($cate->image != '')
                                            <img src="{{ asset('images/class/' . $cate->image) }}" width="200"
                                                height="150" autoplay="no"/>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endif

                        @if ($cate->type == 'zip')
                            <div class="form-group">
                                <div class="col-md-12" id="ziptype">
                                    <input type="radio" name="checkZip" id="ch5" value="url"
                                        {{ $cate->url != '' ? 'checked' : '' }}> {{ __('adminstaticword.URL') }}
                                    <input type="radio" name="checkZip" id="ch6"
                                        {{ $cate->zip != '' ? 'checked' : '' }} value="uploadzip">
                                    {{ __('adminstaticword.UploadZip') }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div id="zipURL" @if ($cate->zip != '') class="display-none" @endif>
                                        <label for=""> {{ __('adminstaticword.URL') }}: </label>
                                        <input type="text" value="{{ $cate->url }}" name="zipurl"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div id="zipUpload" @if ($cate->url != '') class="d-none" @endif>
                                        <label for="">{{ __('adminstaticword.UploadZip') }}:</label>
                                        <!-- =========== -->
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="zip"
                                                    id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                                                <label class="custom-file-label"
                                                    for="inputGroupFile01">{{ __('Choose file') }}</label>
                                            </div>
                                        </div>
                                        <!-- =========== -->
                                        <!-- <input type="file" name="zip" class="form-control"> -->
                                        <br>
                                        @if ($cate->zip != '')
                                            <label for="">{{ __('adminstaticword.ZipFileName') }}:</label>
                                            <input disabled value="{{ $cate->zip }}" class="form-control">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endif --}}

                            @if ($cate->type == 'pdf' ||
                                    $cate->type == 'zip' ||
                                    $cate->type == 'rar' ||
                                    $cate->type == 'word' ||
                                    $cate->type == 'excel' ||
                                    $cate->type == 'powerpoint')
                                <div class="row">
                                    <div class="col-md-12" id="fileUpload">
                                        <label for="fileUpload"> {{ __('Upload File') }}:<span
                                                class="text-danger">*</span></label>
                                        <!-- =========== -->
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="file"
                                                    value="{{ $cate->file }}" id="file_input"
                                                    aria-describedby="inputGroupFileAddon01">
                                                <label class="custom-file-label"
                                                    for="inputGroupFile01">{{ $cate->file }}</label>
                                            </div>
                                        </div>
                                        <!-- =========== -->
                                        <br />
                                        <div class="row">
                                            <div class="col-md-6" id="duration">
                                                <label for="">{{ __('adminstaticword.Duration') }}:<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="duration" min="0" required
                                                    value="{{ $cate->duration }}" class="form-control"
                                                    placeholder="{{ __('Enter class duration in (mins) Eg:160') }}">
                                            </div>
                                            <div class="col-md-3" id="downloadable">
                                                <label for="downloadable">{{ __('Downloadable') }}:</label><br>
                                                <input class="slider" type="checkbox" name="downloadable"
                                                    {{ $cate->downloadable == '1' ? 'checked' : '' }} />
                                            </div>
                                            <div class="col-md-3" id="printable">
                                                <label for="printable">{{ __('Printable') }}:</label><br>
                                                <input class="slider" type="checkbox" name="printable"
                                                    {{ $cate->printable == '1' ? 'checked' : '' }} />
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <br>
                            @endif

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
                                    <!-- ============== -->
                                    <label class="switch">
                                        <input class="slider" type="checkbox" data-id="{{ $cate->id }}"
                                            name="status" {{ $cate->status == '1' ? 'checked' : '' }} />
                                        <span class="knob"></span>
                                    </label>
                                    <!-- ============== -->
                                    <br>
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

            @if ($cate->type == 'video')
                <div class="col-lg-5" style="display:none">
                    <div class="card m-b-30">
                        <div class="card-header">
                            <h5 class="box-title">{{ __('adminstaticword.Subtitle') }}</h5>
                        </div>
                        <div class="card-body ml-2">
                            <a data-toggle="modal" data-target="#myModalSubtitle" href="#"
                                class="btn btn-info btn-sm">+ {{ __('adminstaticword.Add') }}
                                {{ __('adminstaticword.Subtitle') }}</a>

                            <!--Model start-->
                            <div class="modal fade" id="myModalSubtitle" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel"> </h4>
                                        </div>
                                        <div class="box box-primary">
                                            <div class="panel panel-sum">
                                                <div class="modal-body">
                                                    <form enctype="multipart/form-data" id="demo-form2" method="post"
                                                        action="{{ route('add.subtitle', $cate->id) }}"
                                                        data-parsley-validate class="form-horizontal form-label-left">
                                                        {{ csrf_field() }}

                                                        <div id="subtitle">

                                                            <label>{{ __('adminstaticword.Subtitle') }}:</label>
                                                            <table class="table table-bordered" id="dynamic_field">
                                                                <tr>
                                                                    <td>
                                                                        <div
                                                                            class="{{ $errors->has('sub_t') ? ' has-error' : '' }} input-file-block">
                                                                            <input type="file" name="sub_t[]" />
                                                                            <p class="info">
                                                                                {{ __('Choose subtitle file ex. subtitle.srt, or. txt') }}
                                                                            </p>
                                                                            <small
                                                                                class="text-danger">{{ $errors->first('sub_t') }}</small>
                                                                        </div>
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" name="sub_lang[]"
                                                                            placeholder="Subtitle Language"
                                                                            class="form-control name_list" />
                                                                    </td>
                                                                    <td><button type="button" name="add"
                                                                            id="add" class="btn btn-xs btn-success">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button></td>
                                                                </tr>
                                                            </table>

                                                        </div>
                                                        <div class="box-footer">
                                                            <button type="submit"
                                                                class="btn btn-lg col-md-3 btn-primary">{{ __('adminstaticword.Submit') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <table class="displaytable table table-striped table-bordered w-100">
                                    <thead>
                                        <br>
                                        <br>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('adminstaticword.SubtitleLanguage') }} </th>
                                            <th>{{ __('adminstaticword.Delete') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        @foreach ($subtitles as $subtitle)
                                            <?php $i++; ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td>{{ $subtitle->sub_lang }}</td>
                                                <td>
                                                    <form method="post"
                                                        action="{{ route('del.subtitle', $subtitle->id) }}"
                                                        data-parsley-validate class="form-horizontal form-label-left">
                                                        {{ csrf_field() }}

                                                        <button type="submit" class="btn btn-danger display-inline">
                                                            <i class="fa fa-fw fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            @endif

        </div>
    </div>

    <!--vimeo API Modal -->
    <div id="myvimeoModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!--vimeo API Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title pull-left">{{ __('Choose Video From Vimeo') }}</h1>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @if (is_null(env('VIMEO_ACCESS')))
                        <p>Make Sure You Have Added Vimeo API Key in <a href="{{ url('admin/api-settings') }}">API
                                Settings</a></p>
                    @endif

                    <div id="vimeo-page-container" style="clear:both;">
                        <div class="vimeo-content-alignment">
                            <div id="vimeo-page-content" class="" style="overflow:hidden;">
                                <div class="container-4">
                                    <form action="" method="post" name="vimeo-yt-search" id="vimeo-yt-search">
                                        <div class="row">
                                            <!-- comment -->
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="search" name="vimeo-search" id="vimeo-search"
                                                            placeholder="Search..."
                                                            class="ui-autocomplete-input form-control" autocomplete="off">
                                                    </div>
                                                    <div class="input-group-prepend">
                                                        <button class="input-group-text icon btn btn-sm btn-primary"
                                                            id="vimeo-searchBtn"><i class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <!-- comment -->
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"
                                                            id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            onchange="uploadFile(this)" name="file"
                                                            id="inputGroupFile01"
                                                            aria-describedby="inputGroupFileAddon01">
                                                        <label class="custom-file-label"
                                                            for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="vimeo-watch-content"
                                    class="vimeo-watch-main-col vimeo-card vimeo-card-has-padding mt-2">
                                    <ul id="vimeo-watch-related" class="vimeo-video-list"
                                        style="overflow-y:scroll; max-height: 400px;">
                                    </ul>
                                </div>
                                <div>
                                    <input type="hidden" id="vpageToken" value="">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" id="vpageTokenPrev" value=""
                                            class="btn btn-default">Prev</button>
                                        <button type="button" id="vpageTokenNext" value=""
                                            class="btn btn-default">Next</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!--TESTING BunnyCDN Videos -->
    <div id="bunnycdnModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!--Bunnycdn API Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title pull-left">{{ __('Choose Video From Bunnycdn') }}</h1>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @if (is_null(env('BUNNYCDN_API_KEY')))
                        <p>Make Sure You Have Added BUNNYCDN API Key in <a href="{{ url('admin/api') }}">API Settings</a>
                        </p>
                    @endif

                    <div id="vimeo-page-container" style="clear:both;">
                        <div class="vimeo-content-alignment">
                            <div id="vimeo-page-content" class="" style="overflow:hidden;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="search" name="bunnycdn_search" id="bunnycdn_search"
                                                    placeholder="Search..." class="ui-autocomplete-input form-control"
                                                    autocomplete="off">
                                            </div>
                                            <div class="input-group-prepend">
                                                <button class="input-group-text icon btn btn-sm btn-primary"
                                                    id="bunnycdn_search_btn"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="vimeo-watch-content"
                                    class="vimeo-watch-main-col vimeo-card vimeo-card-has-padding mt-2 mb-3">
                                    <div id="bunnycdn-video-list" class="row"
                                        style="overflow-y:scroll; max-height: 400px;">
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="button" id="bunnyCDN_previous_page" value=""
                                        class="btn btn-default">Prev</button>
                                    <button type="button" id="bunnyCDN_next_page" value=""
                                        class="btn btn-default ml-2">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <!-- import bunnycdn js file here -->
    <script src="{{ url('js/bunnycdn.js') }}"></script>
    
    @if ($gsetting->vimeo_enable == 1)
        <script src="{{ url('js/vimeo.js') }}"></script>
    @endif

    {{-- <script>
    $('#youtubeurl').click(function() {

        $('#myyoutubeModal').modal("show"); //Open Modal
        $('#videoURL').show();
        $('#videoUpload').hide();
        $('#iframeURLBox').hide();
        $('#duration_video').show();
        $('#liveclassBox').hide();
        $('#awsBox').hide();
    });
</script>

<script>
    function setVideoURl(videourls) {
        console.log(videourls);
        $('#apiUrl').val(videourls);
        $('#myyoutubeModal').modal("hide"); //add youtube URL
    }
</script> --}}

    <script>
        $('#vimeourl').click(function() {

            $('#myvimeoModal').modal("show"); //Open Modal
            $('#videoURL').show();
            $('#videoUpload').hide();
            $('#iframeURLBox').hide();
            $('#duration_video').show();
            $('#liveclassBox').hide();
            $('#awsBox').hide();
        });
    </script>

    <script>
        function setVideovimeoURl(link, name) {
            $('#myvimeoModal').modal("hide");
            var ifrm = '<iframe src="' + link + '&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"\
                                    frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" \
                                    title="' + name + '">\
                            </iframe>';
            tinymce.activeEditor.setContent(ifrm);
        }

        function setBunnyCDNiframeURl(libraryid, videoid) {
            $('#bunnycdnModal').modal("hide"); // add vimeo URL
            console.log('SetBunnyCDN iframeURL: ', 'https://iframe.mediadelivery.net/embed/' + libraryid + '/' + videoid);


            var ifrm = '<iframe src="' + 'https://iframe.mediadelivery.net/embed/' + libraryid + '/' + videoid + '?autoplay=false" loading="lazy" style="position:absolute;top:0;left:0;width:100%;height:100%;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true">\n\
                            </iframe>';

            var directplayURL = 'https://video.bunnycdn.com/play/' + libraryid + '/' + videoid;

            tinymce.activeEditor.setContent(ifrm); //, {format: 'raw'}

            $(".iframe_url").val(ifrm); // iframe_url class name of CKEditor
            $(".video_url").val(directplayURL);
        }
    </script>

    {{-- <script>
    $('#previewvide').on('change', function() {

        if ($('#previewvide').is(':checked')) {
            $('#document11').show('fast');
            $('#document22').hide('fast');
        } else {
            $('#document22').show('fast');
            $('#document11').hide('fast');
        }
    });
</script> --}}

    {{-- <script>
    $('#drip_type').change(function() {

        if ($(this).val() == 'date') {
            $('#dripdate').show();
            $("input[name='drip_date']").attr('required', 'required');
        } else {
            $('#dripdate').hide();
        }

        if ($(this).val() == 'days') {
            $('#dripdays').show();
            $("input[name='drip_days']").attr('required', 'required');
        } else {
            $('#dripdays').hide();
        }
    });
</script> --}}

    <script>
        (function($) {
            "use strict";

            $('#filetype').change(function() {
                if ($(this).val() == 'pdf') {
                    $('#file_input').attr('accept', '.pdf');
                } else if ($(this).val() == 'zip') {
                    $('#file_input').attr('accept', '.zip');
                } else if ($(this).val() == 'rar') {
                    $('#file_input').attr('accept', '.rar');
                } else if ($(this).val() == 'word') {
                    $('#file_input').attr('accept', '.doc,.docx');
                } else if ($(this).val() == 'excel') {
                    $('#file_input').attr('accept', '.xls,.xlsx');
                } else if ($(this).val() == 'powerpoint') {
                    $('#file_input').attr('accept', '.ppt,.pptx');
                }
            });
        })(jQuery);
    </script>

    @if ($cate->type == 'video')
        <script>
            (function($) {
                "use strict";

                $('#ch1').click(function() {
                    $('#videoURL').show();
                    $('#videoUpload').hide();
                    $('#iframeURLBox').hide();
                    $('#liveURLBox').hide();
                    $('#awsUpload').hide();
                });

                $('#ch2').click(function() {
                    $('#videoURL').hide();
                    $('#videoUpload').show();
                    $('#iframeURLBox').hide();
                    $('#liveURLBox').hide();
                    $('#awsUpload').hide();
                });

                $('#ch9').click(function() {
                    $('#iframeURLBox').show();
                    $('#videoURL').hide();
                    $('#videoUpload').hide();
                    $('#liveURLBox').hide();
                    $('#awsUpload').hide();
                });

                $('#ch10').click(function() {
                    $('#iframeURLBox').hide();
                    $('#videoURL').show();
                    $('#videoUpload').hide();
                    $('#liveURLBox').show();
                    $('#awsUpload').hide();
                });

                //aws checkbox
                $('#ch13').click(function() {
                    $('#awsUpload').show();
                    $('#iframeURLBox').hide();
                    $('#videoURL').hide();
                    $('#videoUpload').hide();
                    $('#liveURLBox').hide();
                });

            })(jQuery);
        </script>
    @endif

    {{-- @if ($cate->type == 'audio')
    <script>
        (function($) {
            "use strict";

            $('#ch11').click(function() {
                $('#audioURL').show();
                $('#audioUpload').hide();
            });

            $('#ch12').click(function() {
                $('#audioURL').hide();
                $('#audioUpload').show();

            });

        })(jQuery);
    </script>
@endif

@if ($cate->type == 'image')
    <script>
        (function($) {
            "use strict";

            $('#ch3').click(function() {
                $('#imageURL').show();
                $('#imageUpload').hide();
            });

            $('#ch4').click(function() {
                $('#imageURL').hide();
                $('#imageUpload').show();

            });

        })(jQuery);
    </script>
@endif

@if ($cate->type == 'zip')
    <script>
        (function($) {
            "use strict";

            $('#ch5').click(function() {
                $('#zipURL').show();
                $('#zipUpload').hide();
            });

            $('#ch6').click(function() {
                $('#zipURL').hide();
                $('#zipUpload').show();
            });

        })(jQuery);
    </script>
@endif --}}
@endsection


@section('stylesheets')
    <style type="text/css">
        .modal {
            overflow-y: auto;
        }

        body {
            background-color: #efefef;
        }

        .container-4 input#hyv-search {
            width: 500px;
            height: 30px;
            border: 1px solid #c6c6c6;
            font-size: 10pt;
            float: left;
            padding-left: 15px;
            -webkit-border-top-left-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            -moz-border-top-left-radius: 5px;
            -moz-border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .container-4 input#vimeo-search {
            width: 500px;
            height: 30px;
            border: 1px solid #c6c6c6;
            font-size: 10pt;
            float: left;
            padding-left: 15px;
            -webkit-border-top-left-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            -moz-border-top-left-radius: 5px;
            -moz-border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .container-4 button.icon {
            height: 34px;
            background: #F0F0EF url(../../images/icons/searchicon.png) 10px 1px no-repeat;
            background-size: 24px;
            -webkit-border-top-right-radius: 5px;
            -webkit-border-bottom-right-radius: 5px;
            -moz-border-radius-topright: 5px;
            -moz-border-radius-bottomright: 5px;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            border: 1px solid #c6c6c6;
            width: 50px;
            margin-left: -44px;
            color: #4f5b66;
            font-size: 10pt;
        }

        button#pageTokenNext {
            margin-left: 5px;
            border-radius: 3px;
            margin-bottom: 20px;
        }

        button#vpageTokenNext {
            margin-left: 5px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
    </style>
@endsection
