<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button"
        id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i
            class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        @if($installments == '1' && $status == '1')
            <a class="dropdown-item" href="{{ route('manual.enrollment.installment', $id) }}"><i
                class="feather icon-pie-chart mr-2"></i>{{ __('Installments') }}</a>
        @endif
        @if($installments == '0' && $status == '1')
            <a class="dropdown-item" href="{{ route('manual.enrollment.view', $id) }}"><i
                class="feather icon-eye mr-2"></i>{{ __('View') }}</a>
        @endif
        <a class="dropdown-item btn btn-link" data-toggle="modal"
            data-target="#enrollment_delete{{ $id }}">
            <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
        </a>
    </div>
</div>


<div id="enrollment_delete{{ $id }}" class="delete-modal modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                    data-dismiss="modal">&times;</button>
                <div class="delete-icon"></div>
            </div>
            <div class="modal-body text-center">
                <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                <p>{{ __('This process') }} <b>{{__('can not be undo.')}}</b> {{__('Do you really want to delete the enrollment?') }}</p>
            </div>
            <div class="modal-footer">
                <form method="post"
                    action="{{ route('enrollment.delete',$id) }}">
                    @csrf
                    @method('POST')

                    <button type="reset" class="btn btn-primary translate-y-3"
                        data-dismiss="modal">{{ __('No') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>