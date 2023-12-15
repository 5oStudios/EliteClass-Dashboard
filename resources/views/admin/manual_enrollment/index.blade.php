@extends('admin.layouts.master')
@section('title', __('Orders'))

@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
    @slot('heading')
    {{ __('Orders') }}
    @endslot

    @slot('menu1')
    {{ __('Orders') }}
    @endslot

    @slot('button')
        <div class="col-md-5 col-lg-5">
            <div class="widgetbar">
                <a href="{{route('manual.enrollment.create')}}" class="float-right btn btn-primary-rgba mr-2"><i
                    class="feather icon-plus mr-2"></i>{{ __('User Enroll') }} </a>
            </div>
        </div>
    @endslot
@endcomponent

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Orders') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mr-5">
                        <div class="col-md-4 form-group">
                            <label for="filter_by_payment">{{ __('Payment Type') }}:</label>
                            <select name="installments" class="form-control2 filter_by_payment">
                                <option value="">{{ __('All') }}</option>
                                <option value="0">{{ __('Full') }}</option>
                                <option value="1">{{ __('Installments') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="order-datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Student') }}</th>
                                    <th>{{ __('adminstaticword.OrderDetail') }}</th>
                                    <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>
                                    <th>{{ __('Status') }}</th>
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

        var table = $('#order-datatable').DataTable({
            language: {
                searchPlaceholder: "Search orders here",
            },
                
            autoWidth: false,
            processing: true,
            serverSide: true,
            order: [ [0, 'DESC'] ],

            ajax: {
                url: "{{ route('order.enrollments') }}",
                type: 'GET',
            },

            columns: [
                {data: 'DT_RowIndex', name: 'id'},
                {data: 'student_detail', name: 'user.fname'},
                {data: 'order_detail', name: 'title'},
                {data: 'payment_detail', name: 'installments', searchable: false},
                {data: 'user.mobile', name: 'user.mobile', visible: false},
                {data: 'installments', name: 'installments', visible: false},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            
        });

        $('.filter_by_payment').on('change', function () {
            table.column(5).search($(this).val()).draw();
        });

        $(document).on('change', '#enrollment', function () {
        
            let status = $(this).is(':checked') ? '1' : '0';
            let id = $(this).data('id');
        
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: "GET",
                dataType: "json",
                url: "{{ route('enrollment.status') }}",
                data: {
                    'id': id,
                    'status': status,
                },
                success: function(data){
                    
                    table.draw();

                    var success = new PNotify( {
                        title: 'success', text:'Status Updated Successfully', type: 'success', desktop: {
                        desktop: true, icon: 'feather icon-thumbs-down'
                        }
                    });
                    success.get().click(function() {
                        success.remove();
                    });
                },
                error: function(resp) {
                    table.draw();
                    
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
    });
</script>
@endsection
