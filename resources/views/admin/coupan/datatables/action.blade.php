<div class="dropdown">
    <button class="btn btn-round btn-primary-rgba" type="button" id="CustomdropdownMenuButton3" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton3">
        @can('coupons.edit')
            <a class="dropdown-item" href="{{ route('coupon.edit', $id) }}" class="btn btn-xs btn-success-rgba"><i
                    class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
        @endcan
        @can('coupons.delete')
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $id }}">
                <i class="feather icon-delete mr-2"></i>{{ __('Delete') }}</a>
            </a>
        @endcan
    </div>
</div>

<div id="delete{{ $id }}" class="delete-modal modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="delete-icon"></div>
            </div>
            <div class="modal-body text-center">
                <h4 class="modal-heading">{{ __('Are You Sure') }} ?</h4>
                <p>{{ __('Do you really want to delete this Coupon? This process cannot be undo') }}.
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('coupon.destroy', $id) }}" class="pull-right">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <button type="reset" class="btn btn-primary"
                        data-dismiss="modal">{{ __('adminstaticword.No') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('adminstaticword.Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
