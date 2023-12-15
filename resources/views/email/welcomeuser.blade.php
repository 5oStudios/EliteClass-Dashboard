@component('mail::message')
# Welcome, {{$user['fname']}} !!

Thank You for Signing Up


Regards,<br>
{{ config('app.name') }}
@endcomponent
