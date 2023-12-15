<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">

        @can('courses.edit')
            <a class="dropdown-item" href="{{ route('course.view', $id) }}"><i
                    class="feather icon-edit mx-2"></i>{{ __('Edit') }}</a>

            @if ($installment == 1)
                <a class="dropdown-item btn btn-link" data-toggle="modal"
                    data-target="#course_installment{{ $id }}">
                    <i class="feather icon-list mx-2"></i>{{ __('Installments') }}</a>
                </a>
            @endif

            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#duplicate{{ $id }}">
                <i class="feather icon-copy mx-2"></i>{{ __('Duplicate') }}
            </a>
        @endcan

        @can('courses.delete')
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $id }}">
                <i class="feather icon-delete mx-2"></i>{{ __('Delete') }}</a>
            </a>
        @endcan
    </div>
</div>

<style>
    #submitYes{{ $id }}:disabled:hover {
        cursor: not-allowed;
    }
</style>

<!--Installment Modal start-->
<div class="modal fade bd-example-modal-md" id="course_installment{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ __('Installments For') . $installment_price }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" method="post" action="{{ route('courseinstallment.store') }}"
                    data-parsley-validate class="form-horizontal form-label-left">
                    {{ csrf_field() }}

                    <input type="hidden" name="course_id" value="{{ $id }}">
                    @for ($i = 0; $i < $total_installments; $i++)
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">{{ __('Installment') . ' ' . ($i + 1) }}:<sup
                                        class="redstar">*</sup></label>
                                <div class="form-group">
                                    <input type="number" min="0" step="0.001" class="form-control"
                                        name="amount[]" value="{{ $installments[$i]['amount'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date">{{ __('Due Date') }}:<sup class="redstar">*</sup></label>
                                <div class="form-group">
                                    <input type="text" class="form-control datepicker" name="due_date[]"
                                        value="{{ $installments[$i]['due_date'] ?? '' }}" placeholder="yyyy-mm-dd"
                                        required>
                                </div>
                            </div>
                        </div>
                    @endfor
                    <div class="form-group">
                        <button type="submit" onClick="this.form.submit(); this.disabled=true;"
                            class="btn btn-primary-rgba">
                            <i class="fa fa-check-circle"></i>
                            {{ __('Create') }}
                        </button>
                    </div>

                    <div class="clear-both"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Installment Modal end-->

<!--Duplicate Modal start-->
<div class="modal fade bd-example-modal-md" id="duplicate{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ __('Duplicate') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    {{ __('Do you want to reset course ') }} <b>{{ __('installments ?') }}</b>
                </p>
                <p>
                    {{ __('It will removed installments from all chapters associated with the installment number and turn off course installments.') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('course.duplicate', $id) }}"
                    onsubmit="document.getElementById('submitYes{{ $id }}').disabled = true;" class="float-right">
                    {{ csrf_field() }}

                    <button type="submit" id="submitNo" class="btn btn-secondary" data-toggle="tooltip"
                        data-placement="top" title="{{ __('Keep installments') }}" name="installment"
                        value="1">{{ __('adminstaticword.No') }}</button>
                    <button type="submit" id="submitYes{{ $id }}" class="btn btn-primary" data-toggle="tooltip"
                        data-placement="top" title="{{ __('Reset installments') }}" name="installment"
                        value="0">{{ __('adminstaticword.Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Duplicate Modal end-->

<!--Delete Modal start-->
<div class="modal fade bd-example-modal-sm" id="delete{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
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
                    {{ __('Do you really want to delete this ') }} <b>{{ __('course') }}</b>
                    {{ __('? This process cannot be undone') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ url('course/' . $id) }}" class="float-right">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <button type="reset" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('adminstaticword.No') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('adminstaticword.Yes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Delete Modal end-->

<script>
    
    $(function() {
        $(".datepicker").datepicker({
            language: 'ar',
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",

            zIndexOffset: 10000,
        });
    });
</script>
