@extends('admin.layouts.master')
@section('title', 'All Coupons')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Coupon') }}
        @endslot

        @slot('menu1')
            {{ __('Coupon') }}
        @endslot

        @slot('button')
            <div class="col-md-12 col-lg-12">
                @can('coupons.delete')
                    <button type="button" class="float-right btn btn-danger-rgba mr-2 " data-toggle="modal" data-target="#bulk_delete"><i
                            class="feather icon-trash mr-2"></i> {{ __('Delete Selected') }}</button>

                    <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                        <div class="modal-dialog modal-sm">

                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <div class="delete-icon"></div>
                                </div>
                                <div class="modal-body text-center">
                                    <h4 class="modal-heading">{{ __('Are You Sure') }} ?</h4>
                                    <p>{{ __('Do you really want to delete selected coupon? This process cannot be undo') }}.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <form id="bulk_delete_form" method="post" action="{{ route('coupon.bulk_delete') }}">
                                        @csrf
                                        @method('POST')
                                        <button type="reset" class="btn btn-primary" data-dismiss="modal">{{ __('No') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ABPP')  
                    <a href="{{ route('coupon.bulk.create') }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-plus mr-2"></i>{{ __('Add Bulk Coupon') }}</a>
                    <a href="{{ route('coupon.create') }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-plus mr-2"></i>{{ __('Add Coupon') }}</a>
                @endif
            </div>
        @endslot
    @endcomponent

    <style>
        .coupon-datatable {
            width: 100% !important;
            vertical-align: middle;
        }
        table.dataTable {
            border-collapse: collapse !important;
        }
        .active-coupon-color {
            color: #8A98AC;
             !important;
        }
    </style>

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-box">{{ __('All Coupons') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="coupon-datatable" class="coupon-datatable table-striped table-bordered">
                                <thead>
                                    <th> <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                            value="all" />
                                        <label for="checkboxAll" class="material-checkbox"></label>
                                    </th>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.CODE') }}</th>
                                    <th>{{ __('adminstaticword.Amount') }}</th>
                                    <th>{{ __('adminstaticword.MaxUsage') }}</th>
                                    <th>{{ __('adminstaticword.Detail') }}</th>
                                    <th>{{ __('adminstaticword.Action') }}</th>
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
            var table = $('#coupon-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search coupon here"
                },

                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 1000,
                stateSave: true,

                ajax: "{{ route('coupon.index') }}",

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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'maxusage',
                        name: 'maxusage'
                    },
                    {
                        data: 'detail',
                        name: 'coupon_type',
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false,
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
