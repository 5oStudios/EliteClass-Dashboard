<div class="dropdown">
    <button class="btn btn-round btn-primary-rgba" type="button" id="CustomdropdownMenuButton3" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton3">


        @if (!empty($chapters))
            <a class="dropdown-item" href="{{ route('session.enrolled', $id) }}"><i
                    class="feather icon-users mr-2"></i>{{ __('Users enrolled') }}</a>
            <a class="dropdown-item" href="{{ route('offline.sessions.edit', $id) }}"><i
                    class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
            <a href="page-product-detail.html" class="dropdown-item" data-toggle="modal"
                data-target=".bd-coursechapter-modal-sm"><i class="feather icon-delete mr-2"></i>{{ __('Delete') }}</a>
        @else
            <a class="dropdown-item" href="{{ route('session.enrolled', $id) }}"><i
                    class="feather icon-users mr-2"></i>{{ __('Users enrolled') }}</a>
            <a class="dropdown-item" href="{{ route('offline.sessions.edit', $id) }}"><i
                    class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
            <a href="page-product-detail.html" class="dropdown-item" data-toggle="modal"
                data-target=".bd-example-modal-sm"><i class="feather icon-delete mr-2"></i>{{ __('Delete') }}</a>
        @endif
    </div>
</div>

<!--Delete Model start-->
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleSmallModalLabel">
                    {{ __('Delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    {{ __('Do you really want to delete this ') }} <b>{{ __('in-person session') }}</b>
                    {{ __('? This process cannot be undone') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('offline.sessions.destroy', $id) }}" class="float-right">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Delete Model end-->

<!--Cannot Delete In-person session Model start-->
<div class="modal fade bd-coursechapter-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleSmallModalLabel">
                    {{ __('Delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    {{ __("You can't delete this In-Person Session because it is linked with a Course") }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
<!--Cannot Delete In-person session Model end-->