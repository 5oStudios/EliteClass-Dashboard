@extends('admin.layouts.master')
@section('title', 'Edit Childcategory')

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __(' Edit Major') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('childcategory.index') }}" class="btn btn-primary-rgba mx-2"><i
                            class="feather icon-arrow-left mx-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria- label="Close">
                            <span aria-hidden="true" style="color:red;">&times;</span>
                        </button>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('Major') }}</h5>
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
                                                <a class="dropdown-toggle" href="#" role="button" id="languagelink"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span
                                                        class="live-icon"> {{ __('Selected Language') }}
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
                        <form method="post" action="{{ route('childcategory.update', $cate->id) }}" data-parsley-validate
                            class="form-horizontal form-label-left" autocomplete="off" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.Category') }}: <span
                                            class="redstar">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Please Choose') }}</option>
                                        @foreach ($category as $caat)
                                            <option {{ $cate->category_id == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.TypeCategory') }}:<span
                                            class="redstar">*</span></label>
                                    <select name="scnd_category_id" id="type_id" class="form-control select2" required>
                                        <option value="" selected disabled>{{ __('Please Choose') }}</option>
                                        @foreach ($typecategory as $caat)
                                            <option {{ $cate->scnd_category_id == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.SubCategory') }}:<span
                                            class="redstar">*</span></label>
                                    <select name="subcategory_id" id="upload_id" class="form-control select2" required>
                                        <option value="" selected disabled>{{ __('Please Choose') }}</option>
                                        @foreach ($subcategory as $caat)
                                            <option {{ $cate->subcategory_id == $caat->id ? 'selected' : '' }}
                                                value="{{ $caat->id }}">{{ $caat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="title">{{ __('adminstaticword.ChildCategory') }}:<span
                                            class="redstar">*</span></label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        value="{{ $cate->title }}" required>
                                </div>
                            </div>
                            <br>

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
                                                name="image" value="{{ $cate->image }}">
                                            <label class="custom-file-label"
                                                for="inputGroupFile01">{{ $cate->image ?? __('Choose file') }}</label>
                                        </div>
                                    </div>
                                    @if ($cate['image'] !== null && $cate['image'] !== '')
                                        <img src="{{ url('/images/majorcategory/' . $cate->image) }}" height="70px;"
                                            width="70px;" />
                                    @else
                                        <img src="{{ Avatar::create($cate->title)->toBase64() }}" alt="Major image"
                                            class="img-fluid">
                                    @endif
                                    <!-- ====================== -->
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <input id="status" type="checkbox" class="custom_toggle" name="status"
                                        {{ $cate->status == '1' ? 'checked' : '' }} />
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


@section('scripts')
    <script>
        (function($) {
            "use strict";
            $(function() {
                $('#category_id').change(function() {
                    var up = $('#type_id').empty();
                    var cat_id = $(this).val();
                    if (cat_id) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            url: "{{ url('type/categories') }}",
                            data: {
                                catId: cat_id
                            },
                            success: function(data) {
                                "<option value='' selected disabled>{{ __('Please Choose') }}</option>"
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
                            url: "{{ url('admin/dropdown') }}",
                            data: {
                                catId: cat_id,
                                typeId: type_id
                            },
                            success: function(data) {
                                up.append(
                                    "<option value='' selected disabled>{{ __('Please Choose') }}</option>"
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
        })(jQuery);
    </script>
@endsection
