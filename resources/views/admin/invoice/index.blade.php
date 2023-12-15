@extends('admin.layouts.master')
@section('title', __('Invoices'))

@section('maincontent')

@component('components.breadcumb',['secondaryactive' => 'active'])
    @slot('heading')
    {{ __('Invoices') }}
    @endslot

    @slot('menu1')
    {{ __('Invoices') }}
    @endslot
@endcomponent

<style>
    .btn{
        padding: 0.275rem 0.5rem !important;
    }
    .w-60 {
        width: 60% !important;
    }
    .select2{
        border: 1px solid #ced4da !important;
        border-radius: .25rem;
    }
    .btn-export{
        color: #fff !important;
        background-color: #563d7c;
        border-color: #563d7c;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple
    {
        border: none !important;
    }
</style>

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Invoices') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form autocomplete="off" action="{{ route('invoices.export') }}" method="POST">
                            @csrf

                            <div class="row px-3">
                                <div class="col-md-4 form-group">
                                    <label for="filter_by_instructor" class="mr-2">{{ __('Instructor') }}:</label>
                                    <select name="instructor_id" class="form-control2 w-60 filter_by_instructor">
                                        <option value="">{{ __('Choose option...') }}</option>
                                        @foreach ($data['instructors'] as $instructor)
                                            <option value="{{ $instructor->instructor_id }}">{{ $instructor->instructor->fname }} {{ $instructor->instructor->lname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group d-none" id="type">
                                    <label for="filter_by_type" class="mr-1">{{ __('Order Type') }}:</label>
                                    <select name="type" class="form-control2 w-60 filter_by_type">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="course_id">{{ __('Course') }} </option>
                                        <option value="chapter_id">{{ __('Chapter') }} </option>
                                        <option value="bundle_id">{{ __('Package') }} </option>
                                        <option value="meeting_id">{{ __('Live Streaming') }} </option>
                                        <option value="offline_session_id">{{ __('In-Person Session') }} </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row px-3 d-none" id="type_orders">
                                <div class="col-md-12 form-group d-flex">
                                    <label for="filter_by_type_orders" class="align-self-end pr-5">{{ __('Title') }}:</label>
                                    <select name="type_ids[]" class="form-control2 select2 filter_by_type_orders" multiple="multiple">
                                        <option value="">{{ __('Choose option...') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row px-3">
                                <div class="col-md-4 form-group">
                                    <label for="filter_by_payment" class="mr-3">{{ __('Payment') }}:</label>
                                    <select name="installments" data-column="3" class="form-control2 w-60 filter_by_payment">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="0">{{ __('Full') }}</option>
                                        <option value="1">{{ __('Installments') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group p-0 d-none" id="installment_no">
                                    <label for="filter_by_installment" class="mr-2">{{ __('Installments') }}:</label>
                                    <select name="installment_no" class="form-control2 filter_by_installment" style="width: 54%">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="1">{{ __('Installment 1') }}</option>
                                        <option value="2">{{ __('Installment 2') }}</option>
                                        <option value="3">{{ __('Installment 3') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group" id="payment_status">
                                    <label for="filter_by_status" class="pr-4 mr-2">{{ __('Status') }}:</label>
                                    <select name="payment_status" class="form-control2 w-60 filter_by_status">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="paid">{{ __('Paid') }}</option>
                                        <option value="unpaid">{{ __('Not Paid') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-end px-3">
                                <div class="col-md-4 form-group">
                                    <label for="min" class="mr-1">{{ __('From Date') }}:<sup class="redstar">*</sup></label>
                                    <input type="text" name="from_date" class="form-control2 w-60 datepicker" id="min" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime($data['from_date'])) }}" required readonly>
                                </div>
        
                                <div class="col-md-4 form-group">
                                    <label for="max" class="pr-4">{{ __('To Date') }}:<sup class="redstar">*</sup></label>
                                    <input type="text" name="to_date" class="form-control2 w-60 datepicker" id="max" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime($data['to_date'])) }}" required readonly>
                                </div>

                                <div class="col-md-4 form-group">
                                    <button type="submit" id="exportbtn" class="btn btn-export"><i class="fa fa-files-o"></i> {{ __('Export') }}</button>
                                </div>
                            </div>
                        </form>

                        <table id="invoice-datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Student') }}</th>
                                    <th>{{ __('adminstaticword.OrderDetail') }}</th>
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

<link href="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">

@section('script')
<script src="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.js') }}"></script>
<script>
$(function() {

    var table = $('#invoice-datatable').DataTable({
        language: {
            searchPlaceholder: "Search invoice here",
        },
            
        autoWidth: false,
        searching: false,
        processing: true,
        serverSide: true,
        order: [ [0, 'DESC'] ],
        // ajax:  "{{ route('invoices') }}",

        ajax: {
            url: "{{ route('invoices') }}",
            type: 'GET',
            data: function(d) {
                d.instructor_id = $('.filter_by_instructor').val();
                d.type = $('.filter_by_type').val();
                d.type_ids = $('.filter_by_type_orders').val();
                d.installments = $('.filter_by_payment').val();
                d.status = $('.filter_by_status').val();
                d.installment_no = $('.filter_by_installment').val();
                d.minDate = $('#min').val();
                d.maxDate = $('#max').val();
            }
        },

        columns: [
            {data: 'DT_RowIndex', name: 'id'},
            {data: 'student_detail', name: 'instructor_id'},
            {data: 'order_detail', name: 'title'},
            {data: 'payment_detail', name: 'installments'},
            {data: 'action', name: 'action', orderable: false, searchable: false},

            // {data: 'payment_detail', name: 'course_id', visible: false},
            // {data: 'payment_plan', name: 'payment_plan.payment_date', visible: false},
        ],

    });

    $('.filter_by_instructor').change(function(){

        if($(this).val() == ''){
            $('#type').addClass('d-none'); 
            $('#type_orders').addClass('d-none');
        }else{
            $('#type').removeClass('d-none'); 
        }
        
        $('.filter_by_type').val('');
        $('.filter_by_type_orders').empty();
        
        table.draw();
    });
        
    $('.filter_by_type').change(function(){
        $('#type_orders').removeClass('d-none');
        
        var ttype = $(this).val();
        if(ttype == ''){
            $('#type_orders').addClass('d-none');
        }
        
        $('.filter_by_type_orders').empty();
        
        table.draw();

        var up = $('.filter_by_type_orders').empty();
        var instructor = $('.filter_by_instructor').val();

        if (instructor && ttype){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url: "{{ route('instructor.order.type') }}",
                data: {
                    instructor_id: instructor,
                    type: ttype,
                },
                success:function(data){
                    up.select2({
                        placeholder: "{{ __('Please Choose') }}",
                    });
                    $.each(data, function(key, row) {
                        up.append($('<option>', {value:row.type_id, text:row.title}));
                    });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log('ERROR: ',XMLHttpRequest);
                }
            });
        }

    });

    $('.filter_by_type_orders').change(function(){
        table.draw();
    });

    $('.filter_by_payment').change(function(){

        if($(this).val() == '1'){
            $('#installment_no').removeClass('d-none');
            $('#payment_status').removeClass('d-none');
        }else{
            $('#installment_no').addClass('d-none');
        }

        $('.filter_by_installment').val('');
        
        table.column( $(this).data('column'))
        .search($(this).val())
        .draw();
    });

    $('.filter_by_installment').change(function(){
        table.draw();
    });

    $('.filter_by_status').change(function(){
        table.draw();
    });

    $('#min').change( function(){
        table.draw();
    });

    $('#max').change( function(){
        table.draw();
    })

    // $("#min").datepicker({
    //     onSelect: function(dateText) {
    //         alert(dateText);
    //         table.draw();
    //     }
    // });
    // $("#min").val("{{ date('Y-m-d', strtotime($data['from_date'])) }}");

    // $("#max").datepicker({
    //     onSelect: function(dateText) {
    //         table.draw();
    //     }
    // });
    // $("#max").val("{{ date('Y-m-d', strtotime($data['to_date'])) }}");

    
    // table.on('draw', function () {
    //     console.log('RESPONSE: ',table.ajax.json());
    // })  
    
    if($('#min, #max').val() != ''){

        $('#exportbtn').click( function(){
            swal({
                title: "Excel Export!",
                text: "Please wait while export is being processed.",
                icon: "success",
            });
        })
    }
    
});
</script>
@endsection
