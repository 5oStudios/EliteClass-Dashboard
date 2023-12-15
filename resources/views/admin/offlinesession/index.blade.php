@extends('admin.layouts.master')
@section('title', 'List all In-person sessions- Admin')

@section('maincontent')
    @component('components.breadcumb', ['fourthactive' => 'active'])
        @slot('heading')
            {{ __('List all In-Person Sessons') }}
        @endslot
        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('offline.sessions.create') }}" class="btn btn-primary-rgba"><i
                            class="feather icon-plus mr-2"></i>{{ __('Add') }}</a>
                    <a href="page-product-detail.html" class="btn btn-danger-rgba" data-toggle="modal"
                        data-target=".bd-example-modal-sm1"><i class="feather icon-trash mr-2"></i>{{ __('Delete Selected') }}</a>
                </div>
                <div class="modal fade bd-example-modal-sm1" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                                <p>{{ __('Do you really want to delete selected ') }}
                                    <b>{{ __('in-person sessions') }}</b>{{ __('? This process cannot be undone') }}
                                </p>
                            </div>
                            <div class="modal-footer">
                                <form method="post" action="{{ action('BulkdeleteController@sessiondeleteAll') }}"
                                    id="bulk_delete_form" data-parsley-validate class="form-horizontal form-label-left">
                                    {{ csrf_field() }}

                                    <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">{{ __('No') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
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
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-title">{{ __('List all In-person sessions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="session-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                value="all" id="checkboxAll">
                                            <label for="checkboxAll" class="material-checkbox"></label>
                                        </th>
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('In-Person Session') }} {{ __('adminstaticword.Detail') }}</th>
                                        <th>{{ __('adminstaticword.Action') }}</th>
                                    </tr>
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
            $('#session-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search In-person session"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,

                ajax: "{{ route('offline.sessions.index') }}",

                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'detail',
                        name: 'title',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endsection