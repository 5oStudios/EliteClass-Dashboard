
@php
    $contains = Illuminate\Support\Str::contains($currency_icon, 'fa');
@endphp

@if($installments == 0)
    <p><b>{{ __('adminstaticword.TransactionId') }}:</b>
        {{ $transaction['transaction_id']?? __('N/A') }}</p>
    <p><b>{{ __('adminstaticword.PaymentMethod') }}:</b>
        {{ $transaction['payment_method']?? __('N/A') }}</p>
    <p><b>{{ __('adminstaticword.PaymentDate') }}:</b>
        {{ $transaction['created_at'] ? getUserTimeZoneDateTime(date('Y-m-d H:i:s', strtotime($transaction['created_at']))) : __('N/A') }}</p>
        
    <p><b>{{ __('adminstaticword.TotalAmount') }}:</b>
            {{ $currency_icon }} {{ $total_amount }}</p>
    
    @if ($coupon_id == null)
        <p><b>{{ __('adminstaticword.Status') }}:</b>

        {{ $paid_amount == $total_amount ? __('Paid'): __('Not Paid') }}</p>

    @else
        <p><b>{{ __('adminstaticword.CouponDiscount') }}</b>:
        {{ $currency_icon }} {{ $coupon_discount }}<br/>
        <b>{{ __('adminstaticword.Status') }}:</b>
        {{ $paid_amount+$coupon_discount == $total_amount ? __('Paid'): __('Not Paid') }}</p>

    @endif
@endif

@if($installments == 1)
    @foreach($payment_plan as $key => $insta)
        <p><b>{{ __('adminstaticword.Installment').($key + 1) }}:</b>

        @if($contains)
            @if($insta['status'])
                <i class="fa {{ $currency_icon }}"></i>{{ $insta['amount'].' | '.__('Paid')}}<br>
                <b>{{ __('adminstaticword.DueDate') }}:</b> {{ $insta['due_date']}}<br>
                <b>{{ __('adminstaticword.PaymentDate') }}:</b> {{ $insta['payment_date'] }}</p>
            @else
                <i class="fa {{ $currency_icon }}"></i>{{ $insta['amount'].' | '.__('Not Paid') }}<br>
                <b>{{ __('adminstaticword.DueDate') }}:</b> {{ $insta['due_date']}}</p>

            @endif    
        @else
            @if($insta['status'])
                {{ $currency_icon }} {{ $insta['amount'].' | '.__('Paid') }}<br>
                <b>{{ __('adminstaticword.DueDate') }}:</b> {{ $insta['due_date']}}<br>
                <b>{{ __('adminstaticword.PaymentDate') }}:</b> {{ $insta['payment_date'] }}</p>
            @else
                {{ $currency_icon }} {{ $insta['amount'].' | '.__('Not Paid') }}<br>
                <b>{{ __('adminstaticword.DueDate') }}:</b> {{ $insta['due_date'] }}</p>
            @endif

        @endif
    @endforeach
@endif