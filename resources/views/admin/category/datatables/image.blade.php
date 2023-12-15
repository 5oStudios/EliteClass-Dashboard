@if ($cat_image !== null && $cat_image !== '')
    <img src="{{ asset('images/category/' . $cat_image) }}" class="img-responsive img-circle">
@else
    <img src="{{ Avatar::create($title)->toBase64() }}" class="img-responsive img-circle">
@endif
