@if ($coupon_type != 'general')
    @php
        $arr1 = ['course', 'bundle', 'meeting', 'session'];
        $arr2 = ['Course', 'Package', 'Live-streaming', 'In-person-session'];
        
        $key1 = array_search($link_by, $arr1);
    @endphp
    <p>{{ __('adminstaticword.Linkedto') }}:
        <b>
            {{ $arr2[$key1] . ' - ' }}
            @if ($link_by == 'course')
                {{ $course['title'] }}
            @elseif($link_by == 'bundle')
                {{ $bundle['title'] }}
            @elseif($link_by == 'meeting')
                {{ $meeting['meetingname'] }}
            @elseif($link_by == 'session')
                {{ $session['title'] }}
            @endif
        </b>
    </p>
@else
    <p>{{ __('adminstaticword.CouponType') }}:
        <b>{{ ucfirst($coupon_type) }}</b>
    </p>
@endif
<p>{{ __('adminstaticword.ExpiryDate') }}:
    <b>{{ $expirydate }}</b>
</p>
<p>{{ __('adminstaticword.DiscountType') }}:
    <b>{{ $distype == 'per' ? 'Percentage' : 'Fixed Amount' }}</b>
</p>
