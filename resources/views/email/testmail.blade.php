@component('mail::message')
Hi,
<br>
<br>
{{ $order }}
<br>
You can see UPayment Webhook response as below:

@component('mail::table', ['data' => $data])

@endcomponent
<br>
<br>

Regards,<br>
{{ config('app.name') }}
@endcomponent
