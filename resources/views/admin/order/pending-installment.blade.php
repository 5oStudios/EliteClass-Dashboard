@extends('admin.layouts.master')
@section('title','Pending Installments')
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Pending Installments') }}
@endslot

@slot('menu1')
{{ __('Pending Installments') }}
@endslot

@slot('button')

@if(Auth::User()->role == "admin")
<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{url('paid-installments')}}" class="float-right btn btn-primary-rgba mr-2"><i
        class="feather icon-arrow-left mr-2"></i>{{__('Back')}}</a> </div>
</div>
@endif

@if(Auth::User()->role == "instructor")
<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{url('userenroll')}}" class="float-right btn btn-primary-rgba mr-2"><i
        class="feather icon-arrow-left mr-2"></i>{{__('Back')}}</a> </div>
</div>
@endif

@endslot

@endcomponent
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Pending Installments') }}</h5>
                </div>
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Student') }}</th>

                                    <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($pending_installments as $invoice)
                                <?php $i++; ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>
                                        @if(isset($invoice->user))
                                        <p><b>{{ __('adminstaticword.ID') }}</b>:
                                            {{ $invoice->order->user->id }}
                                        <p><b>{{ __('adminstaticword.Name') }}</b>:
                                            {{ $invoice->order->user->fname }} {{ $invoice->order->user->lname }}
                                        <p><b>{{ __('adminstaticword.MobileNumber') }}</b>:
                                            {{ $invoice->order->user->mobile }}
                                        <p><b>{{ __('adminstaticword.Email') }}</b>:
                                            {{ $invoice->order->user->email }}
                                        @else
                                            {{ __('Hidden') }}
                                        </p>
                                        @endif

                                        <p><b>{{ __('adminstaticword.Course') }}</b>:
                                            {{$invoice->order->title?? __('N/A')}}
                                        </p>
                                        
                                        <p><b>{{ __('adminstaticword.Instructor') }}</b>:
                                            {{$invoice->order->instructor->fname}} {{$invoice->order->instructor->lname}} 
                                        </p>
                                    </td>

                                    <td>
                                        <p><b>{{ __('Due Date') }}</b>:
                                            {{ $invoice->due_date }}</p>

                                        <p><b>{{ __('Amount') }}</b>:
                                            {{ $invoice->amount}}</p>
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
</script>
@endsection
