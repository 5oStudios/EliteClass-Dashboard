@if ($image !== null && $image !== '')
    <img src="{{ asset('images/majorcategory/' . $image) }}" class="img-responsive img-circle">
@else
    <img src="{{ Avatar::create($title)->toBase64() }}" class="img-responsive img-circle">
@endif
