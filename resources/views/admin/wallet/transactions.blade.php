@extends('admin.layouts.master')
@section('title', __('Transactions - Admin'))

@section('maincontent')

    @component('components.breadcumb', ['secondactive' => 'active'])
        @slot('heading')
            {{ __('Transactions') }}
        @endslot
        @slot('menu1')
            {{ __('Transactions') }}
        @endslot
    @endcomponent

    <style>
        .btn {
            padding: 0.275rem 0.5rem !important;
        }

        .btn-export {
            color: #fff !important;
            background-color: #563d7c;
            border-color: #563d7c;
        }
    </style>

    <!-- Content bar start -->
    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Transactions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form autocomplete="off" action="{{ route('transactions.export') }}" method="POST">
                                @csrf

                                <div class="row px-3">
                                    <div class="col-md-4 form-group d-flex align-items-baseline" id="transaction_type">
                                        <label for="transaction_type"
                                            class="text-nowrap mr-4">{{ __('Filter by') }}:</label>
                                        <select name="transaction_type"
                                            class="form-control2 w-75 filter_by_transaction_type">
                                            <option value="">{{ __('Choose option...') }}</option>
                                            <option value="Credit">{{ __('Credit Transactions') }}</option>
                                            <option value="Debit">{{ __('Debit Transactions') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-none" id="credit_type">
                                        <div class="form-group d-flex align-items-baseline">
                                            <label for="credit_type"
                                                class="text-nowrap mr-2">{{ __('Credit Type') }}:</label>
                                            <select name="credit_type" class="form-control2 w-75 filter_by_credit_type">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="TopUp to wallet by Admin">{{ __('TopUp by Admin') }}
                                                </option>
                                                <option value="Topup to wallet">{{ __('TouUp by User') }}</option>
                                                <option value="Referral Credit">{{ __('Referral') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-none" id="debit_type">
                                        <div class="form-group d-flex align-items-baseline">
                                            <label for="debit_type"
                                                class="text-nowrap mr-2">{{ __('Debit Type') }}:</label>
                                            <select name="debit_type" class="form-control2 w-75 filter_by_debit_type">
                                                <option value="">{{ __('All') }}</option>
                                                <option value="Purchased">{{ __('Items Purchased') }}</option>
                                                <option value="Installment Paid">{{ __('Installment Paid') }}</option>
                                                <option value="Removed amount from wallet by Admin">
                                                    {{ __('TopUp Removed') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row px-3">
                                    <div class="col-md-4 form-group d-flex align-items-baseline">
                                        <label for="min" class="text-nowrap mr-2">{{ __('Date From') }}:</label>
                                        <input type="text" class="form-control2 w-75 datepicker" id="from_date"
                                            name="from_date" placeholder="YYYY-MM-DD">
                                    </div>

                                    <div class="col-md-4 form-group d-flex align-items-baseline">
                                        <label for="max" class="pr-5">{{ __('To') }}:</label>
                                        <input type="text" class="form-control2 w-75 datepicker ml-3" id="to_date"
                                            name="to_date" placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" id="exportbtn" class="btn btn-export float-right"><i
                                                class="fa fa-files-o"></i> {{ __('Export') }}</button>
                                    </div>
                                </div>
                            </form>
                            <table id="transaction-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('#') }}</th>
                                        <th>{{ __('adminstaticword.FirstName') }}</th>
                                        <th>{{ __('Mobile') }}</th>
                                        <th>{{ __('adminstaticword.Type') }}</th>
                                        <th>{{ __('adminstaticword.Amount') }}</th>
                                        <th>{{ __('adminstaticword.PaymentMethod') }}</th>
                                        <th>{{ __('adminstaticword.Detail') }}</th>
                                        <th>{{ __('adminstaticword.Date') }}</th>
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
    <!-- Content bar end -->
@endsection


<link href="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css">

@section('script')
    <script src="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script src="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.js') }}"></script>
    <script>
        $(function() {

            var table = $('#transaction-datatable').DataTable({

                language: {
                    searchPlaceholder: "Search transaction here"
                },

                autoWidth: false,
                searching: false,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'DESC']
                ],

                // ajax:  "{{ route('transactions') }}",
                ajax: {
                    url: "{{ route('transactions') }}",
                    type: 'GET',
                    data: function(d) {
                        d.transaction_type = $('.filter_by_transaction_type').val();
                        d.credit_type = $('.filter_by_credit_type').val();
                        d.debit_type = $('.filter_by_debit_type').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        data: 'user.fname',
                        value: 'user.fname'
                    },
                    {
                        data: 'user.mobile',
                        value: 'user.mobile'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'total_amount'
                    },
                    {
                        data: 'payment_method'
                    },
                    {
                        data: 'detail'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('.filter_by_transaction_type').change(function() {

                if ($(this).val() == '') {
                    $('#credit_type').addClass('d-none');
                    $('#debit_type').addClass('d-none');
                }

                if ($(this).val() == 'Credit') {
                    $('#debit_type').addClass('d-none');
                    $('#credit_type').removeClass('d-none');

                } else if ($(this).val() == 'Debit') {
                    $('#credit_type').addClass('d-none');
                    $('#debit_type').removeClass('d-none');
                }

                $('.filter_by_debit_type').val('');
                $('.filter_by_credit_type').val('');

                table.draw();
            });

            $('.filter_by_debit_type').change(function() {
                table.draw();
            });

            $('.filter_by_credit_type').change(function() {
                table.draw();
            });

            $('#to_date').change(function() {
                table.draw();
            });

            $('#from_date').change(function() {
                table.draw();
            })


            $('#exportbtn').click(function() {
                swal({
                    title: "Excel Export!",
                    text: "Please wait while export is being processed.",
                    icon: "success",
                });
            })

            // table.on('draw', function () {
            //   console.log('RESPONSE: ',table.ajax.json());
            // })
        });
    </script>
@endsection
