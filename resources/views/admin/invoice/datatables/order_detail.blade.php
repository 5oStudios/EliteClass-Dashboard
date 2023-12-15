
@if($course_id)
    <p><b>{{ __('adminstaticword.Course') }}:</b>
        {{$title?? __('N/A')}}
    </p>

@elseif($bundle_id)
    <p><b>{{ __('adminstaticword.Bundle') }}:</b>
        {{$title?? __('N/A')}}
    </p>
    
@elseif($meeting_id)
    <p><b>{{ __('adminstaticword.Meeting') }}:</b>
        {{$title?? __('N/A')}}
    </p>

@elseif($offline_session_id)
    <p><b>{{ __('In-Person Session') }}:</b>
        {{$title?? __('N/A')}}
    </p>
    
@elseif($chapter_id)
    <p><b>{{ __('Chapter') }}:</b>
        {{$title?? __('N/A')}}
    </p>
@endif

<b>{{ __('adminstaticword.StartDate') }}:</b> {{ date('jS F Y', strtotime($enroll_start)) }}<br>
<b>{{ __('adminstaticword.EndDate') }}:</b> {{ date('jS F Y', strtotime($enroll_expire)) }}<br>
