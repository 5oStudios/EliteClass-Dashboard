<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        @can('subcategories.edit')
            <a href="{{ route('subcategory.edit', $id) }}" class="dropdown-item"><i
                    class="feather icon-edit mx-2"></i>{{ __('Edit') }}</a>
        @endcan
        @can('subcategories.delete')
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $id }}">
                <i class="feather icon-delete mx-2"></i>{{ __('Delete') }}</a>
            </a>
        @endcan
    </div>
</div>

<!-- delete Modal start -->
<div class="modal fade bd-example-modal-sm" id="delete{{ $id }}" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ __('Delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    {{ __('Do you really want to delete this ') }} <b>{{ __('institute') }}</b>
                    {{ __('? This process cannot be undone') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('subcategory.destroy', $id) }}" class="float-right">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="reset" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- delete Model ended -->
