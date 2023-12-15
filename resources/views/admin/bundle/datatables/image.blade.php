@if ($preview_image !== null && $preview_image !== '')
    <img src="images/bundle/<?php echo $preview_image; ?>" class="img-responsive img-circle" width="150px" height="100px">
@else
    <img src="{{ Avatar::create($title)->toBase64() }}" class="img-responsive img-circle">
@endif
