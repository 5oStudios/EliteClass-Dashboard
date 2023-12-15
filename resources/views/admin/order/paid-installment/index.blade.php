@extends('admin.layouts.master')
@section('title', __('Paid Installments'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Paid Installments') }}
        @endslot

        @slot('menu1')
            {{ __('Paid Installments') }}
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-title">{{ __('All Paid Installments') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="paidInstallment-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('adminstaticword.Student') }}</th>
                                        <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>
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
            $('#paidInstallment-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search installment here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 1000,

                ajax: "{{ route('installment.invoice') }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'student_detail',
                        name: 'user.fname',
                    },
                    {
                        data: 'payment_detail',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'instructor.fname',
                        name: 'instructor.fname',
                        visible: false
                    },
                    {
                        data: 'user.mobile',
                        name: 'user.mobile',
                        visible: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        visible: false
                    },
                ]
            });
        });
    </script>
@endsection
