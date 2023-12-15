@component('mail::message')
# Hi, {{$user['fname']}} !!

<p style="font-size:18px;font-family: 'Open Sans', sans-serif;text-align: justify;color: #000;">
    Please verify your email with bellow link: 
</p>	

{{-- <a href="{{ url('api/email/verify/'. $token) }}"><button class="btn btn-primary">{{ __('Verify Email')}}</button></a> --}}
@component('mail::button', ['url' => url('api/email/verify/'. $token) ])
Verify Email
@endcomponent
<br>
If you’re having trouble clicking the VERIFY button, copy and paste the URL below into your web browser: {{ url('api/email/verify/'. $token) }}
<br>
<br>

Regards,<br>
{{ config('app.name') }}
@endcomponent
