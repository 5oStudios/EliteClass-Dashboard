<style>
    .datepickers-container{
        z-index: 10000;
    }
 </style>
 
<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())  
        <div class="alert alert-danger" role="alert">
        @foreach($errors->all() as $error)     
            <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-   label="Close">
            <span aria-hidden="true" style="color:red;">&times;</span></button></p>
        @endforeach  
        </div>
        @endif
        <div class="card m-b-30">
            <div class="card-header">
                <button type="button" class="btn btn-danger-rgba mr-2" data-toggle="modal" data-target="#bulk_delete"><i
                        class="feather icon-trash mr-2"></i>{{ __('Delete Selected') }}</button>
                @if(count($installments) < 3)
                <a data-toggle="modal" data-target="#myModalJ" href="#" class="btn btn-primary-rgba">
                    <i class="feather icon-plus mr-2"></i>{{ __('Add Course Installment') }}</a>
                @endif
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table id="" class="displaytable table table-striped table-bordered w-100" >
                        <thead>
                            <tr>
                                <th>
                                    <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                           value="all" />
                                    <label for="checkboxAll" class="material-checkbox"></label>#</th>
                                <th>{{ __('adminstaticword.Installment_number') }}</th>
                                <th>{{ __('adminstaticword.amount') }}</th>
                                <th>{{ __('adminstaticword.Action') }}</th>

                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($installments as $cat)
                            <?php $i++; ?>
                            <tr>
                                <td>    

                                    <input type="checkbox" form="bulk_delete_form1" class="filled-in material-checkbox-input" name="checked[]" value="{{$cat->id}}" id="checkbox{{$cat->id}}">
                                    <label for="checkbox{{$cat->id}}" class="material-checkbox"></label>

                                    <?php echo $i; ?>

                                    <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                                        <div class="modal-dialog modal-sm">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <div class="delete-icon"></div>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <h4 class="modal-heading">{{ __('Are You Sure') }} ?</h4>
                                                    <p>{{ __('Do you really want to delete selected item ? This process cannot be undone') }}.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form id="bulk_delete_form1" method="post"
                                                          action="{{ route('courseinstallment.delete.bulk') }}">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="reset" class="btn btn-gray translate-y-3"
                                                                data-dismiss="modal">{{ __('No') }}</button>
                                                        <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                    
                                <td>{{$cat->sort}}</td>
                                <td>{{$cat->amount}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                            <!--@ can('course-includes.edit')-->
                                            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#myModalE" onclick="Edit({{json_encode($cat)}},{{($cor->installment_price - (($installments->sum('amount') - $cat->amount)??0))}})"><i
                                                    class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                            <!--                          @ endcan
                                                                      @ can('course-includes.delete')-->
                                            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $cat->id}}">
                                                <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
                                            </a>
                                            <!--@ endcan-->
                                        </div>
                                    </div>

                                    <div class="modal fade bd-example-modal-sm" id="delete{{$cat->id}}" role="dialog"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleSmallModalLabel">{{ __('Delete') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4>{{ __('Are You Sure ?')}}</h4>
                                                    <p>{{ __('Do you really want to delete')}} ? {{ __('This process cannot be undone.')}}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="post" action="{{url('installment-delete/'.$cat->id)}}" class="pull-right">
                                                        {{csrf_field()}}
                                                        {{method_field("DELETE")}}
                                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('Yes') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalJ" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>{{ __('Add Course Installment') }}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" id="demo-form2" method="post" action="{{ route('courseinstallment.store') }}" data-parsley-validate
                              class="form-horizontal form-label-left">
                            {{ csrf_field() }}
                            <input type="hidden" name="course_id" value="{{ $cor->id }}">

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="">{{ __('adminstaticword.amount') }}:<sup class="redstar">*</sup></label>
                                    <div class="input-group">
                                        <input type="number" min="1" class="form-control iconvalue" name="amount" required>
                                    </div>
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-dark">{{ __('Due Date') }}: <sup class="redstar">*</sup></label>

                                        <div class="input-group">                                  
                                            <input type="text" id="default-date" required class="datepicker-here form-control" name="due_date" placeholder="yyyy-mm-dd" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2"><i class="feather icon-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i> {{ __('Reset') }}</button>
                                <button type="submit" onClick="this.form.submit(); this.disabled=true;" class="btn btn-primary"><i class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>

                            <div class="clear-both"></div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalE" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>{{ __('Edit Course Installment') }}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" id="demo-form2" method="post" action="{{ route('courseinstallment.store') }}" data-parsley-validate
                              class="form-horizontal form-label-left">
                            {{ csrf_field() }}
                            <input type="hidden" name="course_id" value="{{ $cor->id }}">
                            <input type="hidden" id="installment_id" name="id" value="">

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">{{ __('adminstaticword.amount') }}:<sup class="redstar">*</sup></label>
                                    <div class="input-group">
                                        <input type="number" id="max-amount" min="1" class="form-control iconvalue" name="amount" required>
                                    </div>
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-dark">{{ __('Due Date') }}: <sup class="redstar">*</sup></label>
                                        <div class="input-group">                                  
                                            <input type="text" id="end-due-date" required class="datepicker-here form-control" name="due_date" placeholder="yyyy-mm-dd"
                                                aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2"><i class="feather icon-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i> {{ __('Reset') }}</button>
                                <button type="submit" onClick="this.form.submit(); this.disabled=true;" class="btn btn-primary"><i class="fa fa-check-circle"></i>
                                    {{ __('Update') }}</button>
                            </div>

                            <div class="clear-both"></div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- script to change status end -->

<script>
    function Edit(e, m){
        // console.log(e,m);
        $("#max-amount").val(e.amount);
        $("#installment_id").val(e.id);
        $("#end-due-date").val(e.due_date);
    }

    function  courceinstallment(id) {
        window.location.href = "{{url('quickupdate-course-installment')}}/" + id;
    }


    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });

</script>
