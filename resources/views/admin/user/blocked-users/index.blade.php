@extends('admin.layouts.master')
@section('title', 'Blocked Users')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Blocked Users') }}
        @endslot

        @slot('menu1')
            {{ __('Blocked Users') }}
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-title"> {{ __('Users Blocked due to multiple device attempts') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="blockedUsers-datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile') }}</th>
                                        <th>{{ __('Blocked Attempts') }}</th>
                                        <th>{{ __('Allowed Multiple Devices') }}</th>
                                        <th>{{ __('Blocked') }}</th>
                                        <th>{{ __('Action') }}</th>
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        $(function() {
            $('#blockedUsers-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search user here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,

                ajax: "{{ route('blocked.users') }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'image',
                        name: 'user_img',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'fname'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'blocked_attempt',
                        name: 'blocked_count',
                        searchable: false
                    },
                    {
                        data: 'allow_multiple_device',
                        name: 'is_allow_multiple_device',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'locked',
                        name: 'is_locked',
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
        $(document).on("change", ".userlocked", function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'user/locked',
                data: {
                    'is_locked': $(this).is(':checked') ? 1 : 0,
                    'id': $(this).data('id')
                },
                success: function(data) {
                    var success = new PNotify({
                        title: 'success',
                        text: data.success,
                        type: 'success',
                    });
                    success.get().click(function() {
                        success.remove();
                    });
                },
                error: function(resp) {
                    var error = new PNotify({
                        title: 'error',
                        text: "{{ __('Oops something went wrong, please try again') }}",
                        type: 'error',
                    });
                    error.get().click(function() {
                        error.remove();
                    });
                }
            });
        });
        $(document).on("change", ".allow_multiple_device", function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'user/allow/multi/device',
                data: {
                    'is_allow': $(this).is(':checked') ? 1 : 0,
                    'id': $(this).data('id')
                },
                success: function(data) {
                    var success = new PNotify({
                        title: 'success',
                        text: data.success,
                        type: 'success',
                    });
                    success.get().click(function() {
                        success.remove();
                    });
                },
                error: function(resp) {
                    var error = new PNotify({
                        title: 'error',
                        text: "{{ __('Oops something went wrong, please try again') }}",
                        type: 'error',
                    });
                    error.get().click(function() {
                        error.remove();
                    });
                }
            });
        });
    </script>
@endsection
