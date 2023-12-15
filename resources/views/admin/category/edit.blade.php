@extends('admin.layouts.master')
@section('title', 'Edit Country')

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __('Edit Country') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('category.index') }}" class="btn btn-primary-rgba mx-2"><i
                            class="feather icon-arrow-left mx-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <form method="post" action="{{ route('category.update', $cate->id) }}" data-parsley-validate
                    class="form-horizontal form-label-left" autocomplete="off" enctype="multipart/form-data">
                    <div class="card m-b-30">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-box">{{ __('Edit Country') }}</h5>
                                </div>
                                <!-- language start -->
                                @php
                                    $languages = App\Language::all();
                                @endphp
                                <div class="col-md-6">
                                    <div class="widget">
                                        <li class="list-inline-item">
                                            <div class="languagebar">
                                                <div class="dropdown">
                                                    <a class="dropdown-toggle" href="#" role="button"
                                                        id="languagelink" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false"><span class="live-icon">
                                                            {{ __('Selected Language') }}
                                                            ({{ Session::has('changed_language') ? Session::get('changed_language') : '' }})</span></a>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="languagelink">
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
                                </div>
                                <!-- language end -->
                            </div>
                        </div>
                        <div class="card-body ml-2">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputTit1e">{{ __('adminstaticword.Category') }}:<sup
                                                class="redstar">*</sup></label>
                                        <input type="text" class="form-control" name="title" id="exampleInputTitle"
                                            value="{{ $cate->title }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('Image') }}:<sup class="redstar">*</sup> size: 270x200</label>
                                    <br>
                                    <!-- ====================== -->
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                name="cat_image" value="{{ $cate->cat_image }}">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ $cate->cat_image ?? __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    @if ($cate['cat_image'] !== null && $cate['cat_image'] !== '')
                                        <img src="{{ url('/images/category/' . $cate->cat_image) }}" height="70px;"
                                            width="70px;" />
                                    @else
                                        <img src="{{ Avatar::create($cate->title)->toBase64() }}" alt="course"
                                            class="img-fluid">
                                    @endif
                                    <!-- ====================== -->
                                    <br>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <input id="status" type="checkbox" class="custom_toggle"
                                        {{ $cate->status == '1' ? 'checked' : '' }} name="status" />
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary-rgba"><i
                                                class="fa fa-check-circle"></i>
                                            {{ __('Update') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear-both"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script>
        function formatList(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "{{ asset('flags/48x48') }}/";
            var $state = $(
                '<span><img src="' + baseUrl + state.element.value + '" class="img-flag" /> - ' + state.element.value
                .slice(0, -4) + '</span>'
            );
            return $state;
        }

        function formatSelected(state) {
            if (!state.id) {
                return state.text;
            }

            var baseUrl = "{{ asset('flags/48x48') }}/";
            var $state = $(
                '<span><img class="img-flag" /> - ' + state.element.value.slice(0, -4) + '</span>'
            );

            $state.find("img").attr("src", baseUrl + state.element.value);

            return $state;
        }

        var Country_list = "{{ GuzzleHttp\json_encode($flags) }}";
        $(".edit-contry").select2({
            data: Country_list,
            templateResult: formatList,
            templateSelection: formatSelected
        });

        $(".edit-contry").val("{{ $cate->icon }}").trigger('change');
    </script> --}}
@endsection
