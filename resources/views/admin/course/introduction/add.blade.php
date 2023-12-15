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
                <h1 class="modal-title pull-left">{{ __('Choose Video From BunnyCDN') }}</h1>
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
                <h5 class="card-box">{{ __('Add Course Introduction Video') }}</h5>
            </div>
            <div class="card-body ml-2">
                <form autocomplete="off" class="form-horizontal form-label-left" enctype="multipart/form-data"
                    method="post" action="{{ route('course.introduction', $cor->id) }}">
                    {{ csrf_field() }}
                    {{-- {{ method_field('PUT') }} --}}

                    <input class="video_url" type="hidden" name="video_url" value="{{ $cor->video_url }}" />

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <button onclick="$('#myvimeoModal').modal('show')" type="button"
                                class="btn btn-default form-control">Add/Update Video <i
                                    class="fa fa-refresh"></i></button>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <button onclick="$('#bunnycdnModal').modal('show')" type="button"
                                class="btn btn-warning form-control">Add/Update BunnyCDN Video <i
                                    class="fa fa-refresh"></i></button>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="vemio_detail">{{ __('adminstaticword.Content') }}: <span
                                    class="text-danger">*</span></label>
                            <textarea id="vemio_detail" name="iframe_url" rows="3" class="iframe_url form-control">{{ $cor->iframe_url }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for=""> {{ __('adminstaticword.Duration') }}:<span
                                    class="text-danger">*</span></label>
                            <input type="number" name="duration" min="0" value="{{ $cor->duration }}"
                                placeholder="{{ __('Enter class duration in (mins) Eg:160') }}" class="form-control">
                        </div>
                    </div>
                    <br>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                            {{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- import bunnycdn js in parent class i.e. admin.course.show.blade.php -->
