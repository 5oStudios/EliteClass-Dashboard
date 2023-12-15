@component('mail::message')
Hi {{ $order->user->fname }} !!
<br>
<br>
{{ $x }}
<br>
You can see invoice below:
{{-- {{ $data['PaymentID'] }} --}}

@component('mail::table', ['data' => $data, 'x' => $x])

@endcomponent

{{-- @component('mail::button', ['url' => route('invoice.show', $order_id)])
Invoice
@endcomponent --}}

<br>
<br>

Regards,<br>
{{ config('app.name') }}
@endcomponent
