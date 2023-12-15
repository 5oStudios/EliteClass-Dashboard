@extends('admin.layouts.master')
@section('title',__('Installments'))
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Installments') }}
@endslot

@slot('menu1')
{{ __('Installments') }}
@endslot

@endcomponent
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Installments') }}</h5>
                </div>
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table id="installment-datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Student') }}</th>

                                    <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>
                                    <th>{{ __('adminstaticword.Action') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payInInstallments as $key => $invoice)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>

                                        @if(isset($invoice->user))
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

                                        @if(isset($invoice->course_id))
                                        <p><b>{{ __('adminstaticword.Course') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif(isset($invoice->bundle_id))
                                        <p><b>{{ __('adminstaticword.Bundle') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif(isset($invoice->meeting_id))
                                        <p><b>{{ __('adminstaticword.Meeting') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif(isset($invoice->offline_session_id))
                                        <p><b>{{ __('adminstaticword.Meeting') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @elseif(isset($invoice->chapter_id))
                                        <p><b>{{ __('adminstaticword.Meeting') }}</b>:
                                            {{$invoice->title?? __('N/A')}}
                                        </p>
                                        @endif
                                        
                                        <p><b>{{ __('adminstaticword.Instructor') }}</b>:
                                            {{$invoice->instructor->fname}} {{$invoice->instructor->lname}} 
                                        </p>
                                    </td>

                                    <td>  
                                        @php
                                            $contains = Illuminate\Support\Str::contains($invoice->currency_icon, 'fa');
                                        @endphp
                                        
                                        @foreach($invoice->payment_plan as $key => $insta)
                                     
                                            <p><b>{{ __('adminstaticword.Installment').($key+1) }}</b>:
                                            @if($contains)
                                                <i class="fa {{ $invoice['currency_icon'] }}"></i>{{ $insta->status? $insta->amount.' | '.__('Paid') : $insta->amount.' | '.__('Not Paid') }}
                                            @else
                                                {{ $invoice['currency_icon'] }} {{ $insta->status? $insta->amount.' | '.__('Paid') : $insta->amount.' | '.__('Not Paid')}}</p>
                                            @endif
                                        @endforeach

                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-round btn-outline-primary" type="button"
                                                id="CustomdropdownMenuButton1" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"><i
                                                    class="feather icon-more-vertical-"></i></button>
                                            <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                                <a class="dropdown-item" href="{{ route('user.enroll.installment.view', $invoice->id) }}"><i
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
        $('#installment-datatable').dataTable({
            language: {
                searchPlaceholder: "Search installment here"
            },
			columnDefs: [
              {"targets": [3],  orderable: false, searchable: false},
          ]
        
        });
    });
</script>
@endsection
