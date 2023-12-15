@extends('admin.layouts.master')
@section('title', __('Add Courseclass'))
@section('maincontent')

    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __('Add Course Class') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('chapterclasses', ['id' => $chap_id]) }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
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
                        <h5 class="box-title">{{ __('adminstaticword.Add') }} {{ __('Course Class') }}</h5>
                    </div>
                    <div class="card-body ml-2">
                        <form autocomplete="off" enctype="multipart/form-data" id="demo-form2" method="post"
                            action="{{ route('courseclass.store') }}" data-parsley-validate
                            class="form-horizontal form-label-left">
                            {{ csrf_field() }}

                            <input type="hidden" name="course_id" value="{{ $cor->id }}" />
                            <input type="hidden" name="course_chapters" value="{{ $coursechapt->id }}" />

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.ChapterName') }}: <small
                                            class="text-muted"><i class="fa fa-question-circle"></i> {{ __('readonly') }}
                                        </small></label>
                                    <input type="text" readonly value="{{ $coursechapt->chapter_name }}"
                                        class="form-control" />
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="type">{{ __('adminstaticword.Type') }}:<sup
                                            class="redstar">*</sup></label>
                                    <select name="type" id="filetype" class="form-control select2" required>
                                        <option value="">{{ __('adminstaticword.ChooseFileType') }}</option>
                                        <option value="text">{{ __('Text') }}</option>
                                        <option value="video">{{ __('Video') }}</option>
                                        <option value="quiz">{{ __('Quiz') }}</option>
                                        <option value="pdf">{{ __('Pdf') }}</option>
                                        <option value="word">{{ __('Word') }}</option>
                                        <option value="excel">{{ __('Excel') }}</option>
                                        <option value="powerpoint">{{ __('PowerPoint') }}</option>
                                        <option value="zip">{{ __('Zip') }}</option>
                                        <option value="rar">{{ __('Rar') }}</option>
                                        {{-- <option value="meeting">{{ __('Live Streaming') }}</option>
                                    <option value="offline_session">{{ __('Offline Session') }}</option> --}}
                                    </select>
                                </div>
                                <br>

                                <div class="col-md-12" id="title">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Title') }}:<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control " name="title" id="exampleInputTitle title"
                                        placeholder="{{ __('Enter Title') }}" value="{{ old('title') }}">
                                    <br>
                                </div>

                                <div id="change-video" class="col-md-12 mb-4" style="display:none">
                                    <button onclick="$('#myvimeoModal').modal('show')" type="button"
                                        class="btn btn-default form-control">Change Video <i
                                            class="fa fa-refresh"></i></button>
                                    <br>
                                </div>

                                <div id="bunnycdn-videos" class="col-md-12 mb-4" style="display:none">
                                    <button onclick="$('#bunnycdnModal').modal('show')" type="button"
                                        class="btn btn-warning form-control">TESTING BunnyCDN Videos <i
                                            class="fa fa-refresh"></i></button>
                                    <br>
                                </div>

                                <div class="col-md-12" id="iframeURLBox" style="display:none">
                                    <input class="video_url" type="hidden" name="video_url"
                                        value="{{ old('video_url') }}" />

                                    <label for="">{{ __('adminstaticword.Content') }}: <span
                                            class="text-danger">*</span></label>
                                    <textarea id="vemio_detail" name="iframe_url" rows="3" class="iframe_url form-control">{{ old('iframe_url') }}</textarea>
                                    <br>
                                </div>

                                <!-- File Uploads i.e. PDF, zip, rar, word (.docx, .doc), excel (.xlsx, .xls) powepoint (.pptx, .ppt) -->
                                <div class="col-md-12" id="fileUpload" style="display:none">
                                    <label for="fileUpload">{{ __('Upload File') }}:<span
                                            class="text-danger">*</span></label>
                                    <!-- =========== -->
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" name="file" class="custom-file-input"
                                                id="file_input" accept=".pdf,.zip,.rar,.ppt,.pptx,.xls,.xlsx,.doc,.docx"
                                                aria-describedby="inputGroupFileAddon01">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    <!-- =========== -->
                                    <br>
                                </div>

                                <!--text-->
                                <div class="col-md-12" id="long_text" style="display:none">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Text') }}:<span
                                            class="text-danger">*</span></label>
                                    <textarea id="detail" name="long_text" rows="3" class="form-control">{{ old('long_text') }}</textarea>
                                    <br>
                                </div>

                                {{-- quiz --}}
                                <div class="col-md-12 mb-4" id="quiz" style="display:none">
                                    <label>Select Quiz: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="url">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($classquizes as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach

                                    </select>
                                    <br>
                                </div>

                                <div class="col-md-6" id="duration" style="display:none">
                                    <label for="duration"> {{ __('adminstaticword.Duration') }}:<span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="duration" min="0"
                                        placeholder="{{ __('Enter class duration in (mins) Eg:160') }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-3" id="downloadable" style="display:none">
                                    <label for="downloadable">{{ __('Downloadable') }}:</label><br>
                                    <input class="slider" type="checkbox" name="downloadable" />
                                </div>
                                <div class="col-md-3" id="printable" style="display:none">
                                    <label for="printable">{{ __('Printable') }}:</label><br>
                                    <input class="slider" type="checkbox" name="printable" />
                                </div>

                                {{-- <div class="col-md-12" id="meeting" style="display:none">

                                <div class="form-group">
                                    <label>Select Live Streaming: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="meeting_id">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($bbl_meetings as $bbl)
                                        <option value="{{$bbl->id}}">{{ $bbl->meetingname }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <br> --}}

                                {{-- <div class="col-md-12" id="offline_session" style="display:none">

                                <div class="form-group">
                                    <label>Select Offline Session: <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="offline_session_id">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($offline_sessions as $session)
                                        <option value="{{$session->id}}">{{ $session->title }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <br> --}}
                            </div>
                            <br>

                            <!-- preview video -->
                            <div class="row" id="previewUrl" style="display:none">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.PreviewVideo') }}:</label>
                                    <li class="tg-list-item">
                                        <input name="preview_type" class="tgl tgl-skewed" id="previewvid"
                                            type="checkbox" />
                                        <label class="tgl-btn" data-tg-off="URL" data-tg-on="Upload"
                                            for="previewvid"></label>
                                    </li>
                                    <input type="hidden" name="free" value="0" id="cxv">

                                    <div id="document11">
                                        <label for="exampleInputSlug">Preview
                                            {{ __('adminstaticword.UploadVideo') }}:</label>
                                        <!-- =========== -->
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="video"
                                                    id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                                                <label class="custom-file-label"
                                                    for="inputGroupFile01">{{ __('Choose file') }}</label>
                                            </div>
                                        </div>
                                        <!-- =========== -->
                                    </div>
                                </div>
                                <br>
                            </div>
                            <!-- end preview video -->

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status" checked />
                                        <span class="knob"></span>
                                    </label>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i
                                        class="fa fa-ban"></i>{{ __('Reset') }}</button>
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
                        <p>Make Sure You Have Added Vimeo API Key in <a href="{{ url('admin/api') }}">API Settings</a></p>
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
                                                        <div class="bn btn-info btn-sm inline" id="zakiVideoUploadVimeo"
                                                            onclick="videoUploadVimeoViaZakiCode(window.location)">Upload
                                                            video to Vimeo</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="vimeo-watch-content"
                                    class="vimeo-watch-main-col vimeo-card vimeo-card-has-padding mt-2 mb-3">
                                    <div id="vimeo-watch-related" class="row"
                                        style="overflow-y:scroll; max-height: 400px;">
                                    </div>
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

                                <div class="container-4">
                                    <form action="" method="post" name="vimeo-yt-search" id="vimeo-yt-search">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="search" name="bunnycdn_search" id="bunnycdn_search"
                                                            placeholder="Search..."
                                                            class="ui-autocomplete-input form-control" autocomplete="off">
                                                    </div>
                                                    <div class="input-group-prepend">
                                                        <button class="input-group-text icon btn btn-sm btn-primary"
                                                            id="bunnycdn_search_btn"><i class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <a class="bn btn-warning btn-sm inline"
                                                            style="cursor: pointer; color:white;"
                                                            onclick="videoUploadtoBunnyCDN(window.location)">{{ __('Upload video to BunnyCDN') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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

    <script>
        $(function() {
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
        });
    </script>
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
