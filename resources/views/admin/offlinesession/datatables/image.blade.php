@if ($image !== null && $image !== '')
    <img src="{{ asset('images/offlinesession/' . $image) }}" class="img-responsive img-circle" width="150px"
        height="100px">
@else
    <img src="{{ Avatar::create($title)->toBase64() }}" class="img-responsive img-circle">
@endif