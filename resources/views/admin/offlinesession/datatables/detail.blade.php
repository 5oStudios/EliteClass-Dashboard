<div @if ($expire_date < date('Y-m-d')) style="color:red;" @endif>
    <p><b>{{ __('In-Person Session') }} {{ __('adminstaticword.Name') }} :</b>
        {{ $title }}
    </p>
    <p><b>{{ __('In-Person Session') }} {{ __('Participant') }}:</b>
        {{ $setMaxParticipants == -1 ? __('Unlimited') : $setMaxParticipants }}
    </p>
    <p><b>{{ __('adminstaticword.Duration') }}:</b> {{ $duration }}
        {{ __('min') }}
    </p>
    <p><b>{{ __('Location') }}:</b> {{ $location }}</p>
    <p><b>{{ __('Google Map Link') }}:</b> {{ $google_map_link }}</p>

    @if ($link_by == 'course')
        <p><b>{{ __('Link on course') }}:</b>
            {{ $course['title'] ?? '-' }}</p>
    @endif

    @php
        $startTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $start_time, 'UTC');
        $startTime->setTimezone(auth()->user()->timezone);
    @endphp
    <p><b>{{ __('Presentation Time') }}:</b>
        {{ date('d-m-Y | h:i:s A', strtotime($startTime)) }}
    </p>
    <p><b>{{ __('Expire Date') }}:</b> {{ $expire_date }}</p>
</div>