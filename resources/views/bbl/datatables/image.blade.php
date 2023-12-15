@if ($image !== null && $image !== '')
    <img src="{{ asset('images/bg/' . $image) }}" class="img-responsive img-circle" width="150px" height="100px">
@else
    <img src="{{ Avatar::create($meetingname)->toBase64() }}" class="img-responsive img-circle">
@endif