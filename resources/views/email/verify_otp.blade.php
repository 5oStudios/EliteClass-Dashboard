@component('mail::message')
# Hi, {{$user['fname']}} !!

<p style="font-size:18px;font-family: 'Open Sans', sans-serif;text-align: justify;color: #000;">
	Your Email Verification Code is 
	<br>
	<h1>{{$code}}</h1>
	
</p>
<p style="font-size:18px;font-family: 'Open Sans', sans-serif;text-align: justify;color: #000;">{{ __('Use this code to verify your email')}}.</p>


Regards,<br>
{{ config('app.name') }}
@endcomponent