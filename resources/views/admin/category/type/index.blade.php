@extends('admin.layouts.master')
@section('title', 'All Type of Institutes')
@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Type of Institutes') }}
        @endslot

        @slot('menu1')
            {{ __('Type of Institute') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5 p-0">
                <div class="widgetbar">
                    @can('subcategories.delete')
                        <button type="button" class="btn btn-danger-rgba mx-2 " data-toggle="modal"
                            data-target="#bulk_delete"><i class="feather icon-trash mx-2"></i> {{ __('Delete Selected') }}</button>
                    @endcan
                    @can('subcategories.create')
                        <button type="button" class="btn btn-primary-rgba mx-2" data-toggle="modal" data-target="#create">
                            <i class="feather icon-plus mx-2"></i>{{ __('Add Type of Institute') }}</button>
                    @endcan
                </div>

                <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <div class="delete-icon"></div>
                            </div>
                            <div class="modal-body">
                                <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                                <p>{{ __('Do you really want to delete selected ') }}
                                    <b>{{ __('typecategories') }}</b>{{ __('? This process cannot be undone') }}
                                </p>
                            </div>
                            <div class="modal-footer">
                                <form id="bulk_delete_form" method="post" action="{{ route('typecategories.bulk_delete') }}">
                                    @csrf
                                    @method('POST')
                                    <button type="reset" class="btn btn-secondary"
                                        data-dismiss="modal">{{ __('No') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-sm" id="create" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleSmallModalLabel">
                                    {{ __('Add Type of Institute') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ route('typecategory.store') }}" data-parsley-validate
                                    class="form-horizontal form-label-left" autocomplete="off" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="exampleInputTit1e">{{ __('adminstaticword.Category') }}:<span
                                                        class="redstar">*</span></label>
                                                <select name="category_id" class="form-control select2" required>
                                                    <option value="" selected disabled> {{ __('Please Choose') }} </option>
                                                    @foreach ($categories as $cate)
                                                        <option value="{{ $cate->id }}">
                                                            {{ $cate->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="exampleInputTit1e">{{ __('adminstaticword.TypeCategory') }}:<span
                                                        class="redstar">*</span></label>
                                                <input type="title" class="form-control" name="title" id="exampleInputTitle"
                                                    placeholder="Enter type of institute" value="" required>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="text-dark" for="exampleInputSlug">{{ __('Image') }}: <sup
                                                        class="redstar">*</sup></label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="file">{{ __('Upload') }}</span>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" name="image" class="custom-file-input"
                                                            id="file" aria-describedby="inputGroupFileAddon01" required>
                                                        <label class="custom-file-label"
                                                            for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                                <input id="status_toggle" type="checkbox" class="custom_toggle" name="status"
                                                    checked />
                                                <input type="hidden" name="free" value="0" for="status" id="status">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary-rgba"><i
                                                    class="fa fa-check-circle"></i>
                                                {{ __('Create') }}</button>
                                        </div>
                                        <div class="clear-both"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
                        <h5 class="card-box">{{ __('All Type of Institutes') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="typecategory-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th> <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                value="all" />
                                            <label for="checkboxAll" class="material-checkbox"></label>
                                        </th>
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('adminstaticword.TypeCategory') }}</th>
                                        <th>{{ __('adminstaticword.Slug') }}</th>
                                        <th>{{ __('adminstaticword.Status') }}</th>
                                        <th>{{ __('adminstaticword.Action') }}</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(function() {
            $('#typecategory-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search type of institute"
                },

                processing: true,
                serverSide: true,
                searchDelay: 1000,

                ajax: "{{ route('typecategory.index') }}",

                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        name: 'image',
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'title',
                        data: 'title'
                    },
                    {
                        name: 'slug',
                        data: 'slug'
                    },
                    {
                        name: 'status',
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'action',
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    <script>
        $("#checkboxAll").on('click', function() {
            $('input.check').not(this).prop('checked', this.checked);
        });
    </script>
@endsection
