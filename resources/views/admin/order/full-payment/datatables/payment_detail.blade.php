<p>
    <b>{{ __('adminstaticword.TransactionId') }}</b>:
    {{ $transaction['transaction_id'] ?? __('N/A') }}
</p>
<p>
    <b>{{ __('adminstaticword.PaymentMethod') }}</b>:
    {{ $transaction['payment_method'] ?? __('N/A') }}
</p>

@php
    $contains = Illuminate\Support\Str::contains($currency_icon, 'fa');
@endphp

<p>
    <b>{{ __('adminstaticword.TotalAmount') }}</b>:
    {{ $currency_icon }} {{ $total_amount }}
</p>

@if ($coupon_id == null)
    <p><b>{{ __('adminstaticword.Status') }}</b>:

        {{ $paid_amount == $total_amount ? __('Paid') : __('Not Paid') }}
    @else
        <b>{{ __('adminstaticword.CouponDiscount') }}</b>:
        {{ $currency_icon }} {{ $coupon_discount }}<br />
        <b>{{ __('adminstaticword.Status') }}</b>:
        {{ $paid_amount + $coupon_discount == $total_amount ? __('Paid') : __('Not Paid') }}
    </p>
@endif
