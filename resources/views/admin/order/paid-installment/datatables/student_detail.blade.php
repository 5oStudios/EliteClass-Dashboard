@if ($user)
    <p>
        <b>{{ __('adminstaticword.ID') }}</b>:
        {{ $user['id'] }}
    <p>
        <b>{{ __('adminstaticword.Name') }}</b>:
        {{ $user['fname'] }} {{ $user['lname'] }}
    <p>
        <b>{{ __('adminstaticword.MobileNumber') }}</b>:
        {{ $user['mobile'] }}
    <p>
        <b>{{ __('adminstaticword.Email') }}</b>:
        {{ $user['email'] }}
    @else
        {{ __('Hidden') }}
    </p>
@endif

@if ($course_id)
    <p>
        <b>{{ __('adminstaticword.Course') }}</b>:
        {{ $title ?? __('N/A') }}
    </p>
@elseif ($bundle_id)
    <p>
        <b>{{ __('adminstaticword.Bundle') }}</b>:
        {{ $title ?? __('N/A') }}
    </p>
@elseif ($meeting_id)
    <p>
        <b>{{ __('adminstaticword.Meeting') }}</b>:
        {{ $title ?? __('N/A') }}
    </p>
@elseif ($offline_session_id)
    <p>
        <b>{{ __('adminstaticword.Meeting') }}</b>:
        {{ $title ?? __('N/A') }}
    </p>
@elseif ($chapter_id)
    <p>
        <b>{{ __('adminstaticword.Meeting') }}</b>:
        {{ $title ?? __('N/A') }}
    </p>
@endif

<p>
    <b>{{ __('adminstaticword.Instructor') }}</b>:
    {{ $instructor['fname'] }} {{ $instructor['lname'] }}
</p>
