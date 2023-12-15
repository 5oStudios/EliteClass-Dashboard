@php
    $contains = Illuminate\Support\Str::contains($currency_icon, 'fa');
@endphp

@foreach ($payment_plan as $key => $insta)
    <p><b>{{ __('adminstaticword.Installment') . ($key + 1) }}</b>:
        @if ($contains)
            <i
                class="fa {{ $currency_icon }}"></i>{{ $insta['status'] ? $insta['amount'] . ' | ' . __('Paid') : $insta['amount'] . ' | ' . __('Not Paid') }}
        @else
            {{ $currency_icon }}
            {{ $insta['status'] ? $insta['amount'] . ' | ' . __('Paid') : $insta['amount'] . ' | ' . __('Not Paid') }}
    </p>
@endif
@endforeach
