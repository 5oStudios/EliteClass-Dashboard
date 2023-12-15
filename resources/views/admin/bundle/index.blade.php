@extends('admin.layouts.master')
@section('title', 'All Packages')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Packages') }}
        @endslot

        @slot('menu1')
            {{ __('Packages') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    @can('bundle-courses.create')
                        <a href="{{ url('bundle/create') }}" class="btn btn-primary-rgba mr-2"><i
                                class="feather icon-plus mr-2"></i>{{ __('Add Package') }}</a>
                    @endcan
                    @can('bundle-courses.delete')
                        <button type="button" class="btn btn-danger-rgba mr-2 " data-toggle="modal" data-target="#bulk_delete"><i
                                class="feather icon-trash mr-2"></i> {{ __('Delete Selected') }}</button>
                    @endcan
                </div>
                <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <div class="delete-icon"></div>
                            </div>
                            <div class="modal-body">
                                <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                                <p>{{ __('Do you really want to delete selected ') }}
                                    <b>{{ __('packages') }}</b>{{ __('? This process cannot be undone') }}
                                </p>
                            </div>
                            <div class="modal-footer">
                                <form id="bulk_delete_form" method="post" action="{{ route('bundlecourse.bulk_delete') }}">
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
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true" style="color:red;">&times;</span></button>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-box">{{ __('All Packages') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="bundle-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th> <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                value="all" />
                                            <label for="checkboxAll" class="material-checkbox"></label>
                                        </th>
                                        <th>#</th>
                                        <th>{{ __('adminstaticword.Image') }}</th>
                                        <th>{{ __('adminstaticword.Title') }}</th>
                                        <th>{{ __('adminstaticword.Instructor') }}</th>
                                        <th>{{ __('adminstaticword.Status') }}</th>
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
            <!-- End col -->
        </div>
        <!-- End row -->
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        $(function() {
            $('#bundle-table').DataTable({
                language: {
                    searchPlaceholder: "Search package here"
                },
                processing: true,
                serverSide: true,
                searchDelay: 2000,

                ajax: "{{ route('bundle.index') }}",

                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'instructor',
                        name: 'user.fname',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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
    <script>
        $(document).on("change", ".bundlestatus", function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'bundlecourse/status',
                data: {
                    'status': $(this).is(':checked') ? 1 : 0,
                    'id': $(this).data('id')
                },
                success: function(data) {
                    var warning = new PNotify({
                        title: 'success',
                        text: 'Status Updated Successfully',
                        type: 'success',
                        desktop: {
                            desktop: true,
                            icon: 'feather icon-thumbs-down'
                        }
                    });
                    warning.get().click(function() {

                        warning.remove();
                    });
                }
            });
        });

        // $(document).on("change", ".subscriptionstatus", function() {
        //     $.ajax({
        //         type: "GET",
        //         dataType: "json",
        //         url: 'bundlecourse/subscription/status',
        //         data: {
        //             'is_subscription_enabled': $(this).is(':checked') ? 1 : 0,
        //             'id': $(this).data('id')
        //         },
        //         success: function(data) {
        //             console.log(id)
        //         }
        //     });
        // });

        $(document).on("change", ".featuredstatus", function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'bundlecourse/featured/status',
                data: {
                    'featured': $(this).is(':checked') ? 1 : 0,
                    'id': $(this).data('id')
                },
                success: function(data) {
                    console.log(id)
                }
            });
        });
    </script>
@endsection
