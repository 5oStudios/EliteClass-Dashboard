<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        <a class="dropdown-item" href="{{ route('view.invoice', $id) }}"><i
                class="feather icon-eye mr-2"></i>{{ __('View') }}</a>
    </div>
</div>
