<div @if ($expire_date < date('Y-m-d')) style="color:red;" @endif>
    <p><b>{{ __('adminstaticword.Meeting') }} {{ __('adminstaticword.Name') }}:</b>
        {{ $meetingname }}
    </p>
    <p><b>{{ __('adminstaticword.Meeting') }} {{ __('Participant') }}:</b>
        {{ $setMaxParticipants == -1 ? __('Unlimited') : $setMaxParticipants }}
    </p>
    <p><b>{{ __('adminstaticword.Duration') }}:</b>
        {{ $duration }} {{ __('min') }}
    </p>
    <p><b>{{ __('Welcome Message') }}:</b>
        {{ $welcomemsg == '' ? __('Not set') : $welcomemsg }}
    </p>
    <p><b>{{ __('Mute on start') }}:</b>
        {{ $setMuteOnStart == 1 ? __('Yes') : __('No') }}
    </p>
    @if ($link_by == 'course')
        <p><b>{{ __('Link on course') }}:</b>
            {{ $course['title'] ?? '-' }}
        </p>
    @endif
    @php
        $startTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $start_time, 'UTC');
        $startTime->setTimezone(auth()->user()->timezone);
    @endphp
    <p><b>{{ __('Start Time') }}:</b>
        {{ date('d-m-Y | h:i:s A', strtotime($startTime)) }}
    </p>
</div>
