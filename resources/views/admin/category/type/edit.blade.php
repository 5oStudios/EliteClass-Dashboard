@extends('admin.layouts.master')
@section('title', 'Edit Subcategories')

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            {{ __('Admin') }}
        @endslot

        @slot('menu2')
            {{ __(' Edit Type of Institute') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('typecategory.index') }}" class="btn btn-primary-rgba mx-2"><i
                            class="feather icon-arrow-left mx-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('Type of Institute') }}</h5>
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
                        <form method="post" action="{{ route('typecategory.update', $cate->id) }}" data-parsley-validate
                            class="form-horizontal form-label-left" autocomplete="off" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputSlug">{{ __('adminstaticword.Category') }}</label>
                                    <select name="category_id" class="form-control select2">
                                        <option value="" selected disabled> {{ __('Please Choose') }} </option>
                                        @foreach ($categories as $cou)
                                            <option value="{{ $cou->id }}"
                                                {{ $cate->category_id == $cou->id ? 'selected' : '' }}>
                                                {{ $cou->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.SubCategory') }}:<span
                                            class="redstar">*</span></label>
                                    <input type="title" class="form-control" name="title" id="exampleInputTitle"
                                        value="{{ $cate->title }}">
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
                                        <img src="{{ url('/images/typecategory/' . $cate->image) }}" height="70px;"
                                            width="70px;" />
                                    @else
                                        <img src="{{ Avatar::create($cate->title)->toBase64() }}"
                                            alt="Type of institute image" class="img-fluid">
                                    @endif
                                    <!-- ====================== -->
                                    <br>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:<sup
                                            class="redstar text-danger">*</sup></label><br>
                                    <input id="status" type="checkbox" class="custom_toggle"
                                        {{ $cate->status == '1' ? 'checked' : '' }} name="status" />
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                    {{ __(' Update') }}</button>
                            </div>
                            <div class="clear-both"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
