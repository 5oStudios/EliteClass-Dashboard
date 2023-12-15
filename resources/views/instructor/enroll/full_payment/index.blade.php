@extends('admin.layouts.master')
@section('title', __('Full Payments'))
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Full Payment') }}
@endslot

@slot('menu1')
{{ __('Full Payment') }}
@endslot

@endcomponent
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('Full Payment Enrollments') }}</h5>
                </div>
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table id="invoice-datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Student') }}</th>

                                    <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>
                                    <th>{{ __('adminstaticword.Action') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fullPayment as $key => $invoice)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>

                                        @if($invoice->user)
                                        <p><b>{{ __('adminstaticword.ID') }}</b>:
                                            {{ $invoice->user->id }}
                                        <p><b>{{ __('adminstaticword.Name') }}</b>:
                                            {{ $invoice->user->fname }} {{ $invoice->user->lname }}
                                        <p><b>{{ __('adminstaticword.MobileNumber') }}</b>:
                                            {{ $invoice->user->mobile }}
                                        <p><b>{{ __('adminstaticword.Email') }}</b>:
                                            {{ $invoice->user->email }}
                                        @else
                                            {{ __('Hidden') }}
                                        </p>
                                        @endif

                                        @if($invoice->course_id)
                                        <p><b>{{ __('adminstaticword.Course') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif($invoice->bundle_id)
                                        <p><b>{{ __('adminstaticword.Bundle') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif($invoice->meeting_id)
                                        <p><b>{{ __('adminstaticword.Meeting') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif($invoice->offline_session_id)
                                        <p><b>{{ __('In-Person Session') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif($invoice->chapter_id)
                                        <p><b>{{ __('Chapter') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @endif
                                        
                                        <p><b>{{ __('adminstaticword.Instructor') }}</b>:
                                            {{$invoice->instructor->fname}} {{$invoice->instructor->lname}} 
                                        </p>
                                    </td>

                                    <td>
                                        <p><b>{{ __('adminstaticword.TransactionId') }}</b>:
                                            {{ $invoice->transaction->transaction_id?? __('N/A') }}</p>
                                        <p><b>{{ __('adminstaticword.PaymentMethod') }}</b>:
                                            {{ $invoice->transaction->payment_method?? __('N/A') }}</p>
                                            
                                        @php
                                            $contains = Illuminate\Support\Str::contains($invoice->currency_icon, 'fa');
                                        @endphp
                                            
                                        <p><b>{{ __('adminstaticword.TotalAmount') }}</b>:
                                                {{ $invoice->currency_icon }} {{ $invoice->total_amount }}</p>


                                        
                                        @if ($invoice->coupon_id == null)
                                            <p><b>{{ __('adminstaticword.Status') }}</b>:

                                            {{ $invoice->paid_amount == $invoice->total_amount ? __('Paid'): __('Not Paid') }}

                                        @else
                                            <b>{{ __('adminstaticword.CouponDiscount') }}</b>:
                                            {{ $invoice->currency_icon }} {{ $invoice->coupon_discount }}<br/>
                                            <b>{{ __('adminstaticword.Status') }}</b>:
                                            {{ $invoice->paid_amount + $invoice->coupon_discount  == $invoice->total_amount ? __('Paid'): __('Not Paid') }}</p>

                                        @endif

                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-round btn-outline-primary" type="button"
                                                id="CustomdropdownMenuButton1" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"><i
                                                    class="feather icon-more-vertical-"></i></button>
                                            <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                                <a class="dropdown-item" href="{{ route('user.enroll.fullpayment.view', $invoice->id) }}"><i
                                                        class="feather icon-eye mr-2"></i>{{ __('View') }}</a>
                                            </div>
                                        </div>
                                    </td>
                                    
                                </tr>
                                @endforeach

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
	$(function () {
        $('#invoice-datatable').dataTable({
            language: {
                searchPlaceholder: "Search invoice here"
            },
			columnDefs: [
              {"targets": [3], orderable: false, searchable: false},
          ]
        
        });
    });
</script>
@endsection
