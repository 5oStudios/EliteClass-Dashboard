<b>{{ __('adminstaticword.ID') }}:</b>
    {{ $user['id'] }}<br>
<b>{{ __('adminstaticword.Name') }}:</b>
    {{ $user['fname'] }} {{ $user['lname'] }}<br>
<b>{{ __('adminstaticword.MobileNumber') }}:</b>
    {{ $user['mobile'] }}<br>
<b>{{ __('adminstaticword.Email') }}:</b>
    {{ $user['email'] }}<br><br>

<b>{{ __('adminstaticword.Enrolled') }}:</b> 
    {{ date('jS F Y', strtotime($created_at)) }}

<p><b>{{ __('adminstaticword.Instructor') }}:</b>
    {{$instructor['fname']}} {{$instructor['lname']}} 
    {{-- {{ $instructor['fullname'] }}  --}}
</p>