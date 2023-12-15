@if ($image = @file_get_contents('../public/images/user_img/' . $user_img))
    <img @error('photo') is-invalid @enderror src="{{ url('images/user_img/' . $user_img) }}" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal" data-target="#exampleStandardModal{{ $id }}">
@else
    <img @error('photo') is-invalid @enderror src="{{ Avatar::create($fname)->toBase64() }}" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal" data-target="#exampleStandardModal{{ $id }}">
@endif
