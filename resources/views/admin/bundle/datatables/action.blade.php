<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        @can('bundle-courses.view')
            <button type="button" class="dropdown-item" data-toggle="modal"
                data-target="#exampleStandardModal{{ $id }}">
                <i class="feather icon-eye mx-2"></i> {{ __('View') }}
            </button>
        @endcan
        @if ($installment == 1)
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#inst{{ $id }}">
                <i class="feather icon-list mx-2"></i>{{ __('Installments') }}</a>
            </a>
        @endif
        @can('bundle-courses.edit')
            <a class="dropdown-item" href="{{ route('bundle.show', $id) }}"><i
                    class="feather icon-edit mx-2"></i>{{ __('Edit') }}</a>
        @endcan
        @can('bundle-courses.delete')
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $id }}">
                <i class="feather icon-delete mx-2"></i>{{ __('Delete') }}</a>
            </a>
        @endcan
    </div>
</div>

<!--Installment Modal start-->
<div class="modal fade bd-example-modal-md" id="inst{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleSmallModalLabel">
                    {{ __('Installments For') . $installment_price }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form autocomplete="off" id="demo-form2" method="post" action="{{ route('bundleinstallment.store') }}"
                    data-parsley-validate class="form-horizontal form-label-left">
                    {{ csrf_field() }}
                    <input type="hidden" name="bundle_id" value="{{ $id }}">

                    @for ($i = 0; $i < $total_installments; $i++)
                        <div class="row">
                            <div class="col-md-6">
                                <label>{{ __('Installment') . ' ' . ($i + 1) }}:<sup
                                        class="redstar">*</sup></label>
                                <div class="form-group">
                                    <input type="number" step="0.001" min="1" class="form-control"
                                        name="amount[]" value="{{ $installments[$i]['amount'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>{{ __('Due Date') }}:<sup class="redstar">*</sup></label>
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

<!--Delete Modal start-->
<div class="modal fade bd-example-modal-sm" id="delete{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
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
                    {{ __('Do you really want to delete this ') }} <b>{{ __('package') }}</b>
                    {{ __('? This process cannot be undone') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ url('bundle/' . $id) }}" class="pull-right">
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

<!--View Modal start-->
<div class="modal fade" id="exampleStandardModal{{ $id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleStandardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleStandardModalLabel">
                    {{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Courses</h5>
                @foreach ($course_id as $crew)
                    @php
                        $name = App\Course::where('id', $crew)->value('title');
                    @endphp
                    <span class="badge badge-success">{{ ucfirst($name) }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!--View Modal end-->

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
