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
                @can('quiz-topic.delete')
                <button type="button" class="btn btn-danger-rgba mr-2 " data-toggle="modal" data-target="#bulk_delete10"><i
                        class="feather icon-trash mr-2"></i>{{ __('Delete Selected') }} </button>
                @endcan
                @can('quiz-topic.create')
                <a data-toggle="modal" data-target="#myModaltopic" href="#" class="btn btn-primary-rgba"><i
                        class="feather icon-plus "></i>{{ __('Add Quiz') }} </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="" class="displaytable table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>
                                    <input id="checkboxAll10" type="checkbox" class="filled-in" name="checked[]" value="all" />
                                    <label for="checkboxAll" class="material-checkbox"></label> #
                                </th>
                                <th>{{ __('adminstaticword.Topic') }}</th>
                                <th>{{ __('adminstaticword.Marks') }}</th>
                                <th>{{ __('adminstaticword.PassingPercentage') }}</th>
                                <!-- <th>{{ __('adminstaticword.Status') }}</th> -->
                                <th>{{ __('adminstaticword.Reattempt') }}</th>
                                <!-- <th>{{ __('adminstaticword.Type') }}</th> -->
                                <th>{{ __('adminstaticword.Action') }}</th>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($topics as $topic)
                            <tr>
                                <?php $i++; ?>
                                <td>
                                    <input type="checkbox" form="bulk_delete_form10" class="filled-in material-checkbox-input10" name="checked[]" value="{{$topic->id}}" id="checkbox{{$topic->id}}">
                                    <label for="checkbox{{$topic->id}}" class="material-checkbox"></label> <?php echo $i; ?>
                                    <!-- bulk delete modal start -->
                                    <div id="bulk_delete10" class="delete-modal modal fade" role="dialog">
                                        <div class="modal-dialog modal-sm">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <div class="delete-icon"></div>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <h4 class="modal-heading">{{ __('Are You Sure') }} ?</h4>
                                                    <p>{{ __('Do you really want to delete selected item ? This process
                                        cannot be undone') }}.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form id="bulk_delete_form10" method="post"
                                                          action="{{ route('quiztopic.bulk_delete') }}">
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
                                    <!-- bulk delete modal start -->
                                </td>
                                <td>{{$topic->title}}</td>
                                <td>{{($topic->per_q_mark * ($topic->quizquestion->count()??1))}}</td>
                                <td>
                                    {{$topic->p_percent}}
                                </td>

                                <!-- <td>
                                    <label class="switch">
                                        <input class="slider" type="checkbox"  data-id="{{$topic->id}}" name="status" {{ $topic->status ==1 ? 'checked' : ''}} onchange="quiztopic('{{$topic->id}}')" />
                                        <span class="knob"></span>
                                    </label>
                                </td> -->

                                <td>
                                    @if(auth()->user()->role == 'admin')
                                        <label class="switch">
                                            <input class="slider" type="checkbox"  data-id="{{$topic->id}}" name="quiz_again" {{ $topic->quiz_again ==1 ? 'checked' : ''}} onchange="quizreattemp('{{$topic->id}}')" />
                                            <span class="knob"></span>
                                        </label>
                                    @elseif(auth()->user()->role == 'instructor')
                                        {{ $topic->quiz_again ==1 ? 'Active' : 'Not active' }}
                                    @endif
                                </td>


                                {{-- <td>
                                    @if($topic->type==1)
                                    {{ __('Subjective') }}
                                    @else
                                    {{ __('Objective') }}
                                    @endif
                                </td> --}}

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                            @can('quiz-topic.edit')
                                                <a class="dropdown-item" href="{{url('admin/quiztopic/'.$topic->id)}}"><i
                                                        class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                            @endcan
                                            @can('quiz-topic.view')
                                                @if(auth()->user()->role == 'admin')
                                                    <a class="dropdown-item" href="{{route('questions.show', $topic->id)}}"><i
                                                        class="feather icon-file-plus mr-2"></i>{{ __('Add Questions') }}</a>
                                                @elseif(auth()->user()->role == 'instructor')
                                                    <a class="dropdown-item" href="{{route('questions.show', $topic->id)}}"><i
                                                        class="feather icon-file-plus mr-2"></i>{{ __('View Questions') }}</a>
                                                @endif
                                            @endcan
                                            {{-- <a class="dropdown-item" href="{{route('answersheet', $topic->id)}}"><i
                                                    class="feather icon-delete mr-2"></i>{{ __('Delete Answer') }}</a> --}}
                                            @can('report.quiz-report.manage')
                                                <a class="dropdown-item" href="{{route('show.quizreport', $topic->id)}}"><i
                                                    class="feather icon-file mr-2"></i>{{ __('Show Report') }}</a>
                                            @endcan
                                            @can('quiz.delete')
                                                <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#deleteq{{ $topic->id}}">
                                                    <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="modal fade bd-example-modal-sm" id="deleteq{{$topic->id}}" tabindex="-1" role="dialog"
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
                                                    <p>{{ __('Do you really want to delete')}} <b>{{$topic->title}}</b>? {{ __('This process cannot be undone.')}}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="post" action="{{url('admin/quiztopic/'.$topic->id)}}" class="pull-right">
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
<script>
    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>

<div class="modal fade" id="myModaltopic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>Add Quiz</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>

            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" id="demo-form2" method="post" action="{{url('admin/quiztopic/')}}" data-parsley-validate
                              class="form-horizontal form-label-left">
                            {{ csrf_field() }}

                            <input type="hidden" name="course_id" value="{{ $cor->id }}" />


                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.QuizTopic') }}:<span class="redstar">*</span>
                                    </label>
                                    <input type="text" placeholder="{{__('Enter Quiz Topic')}}" class="form-control " name="title"
                                           id="exampleInputTitle" required>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.QuizDescription') }}:<sup
                                            class="redstar">*</sup></label>
                                    <textarea name="description" rows="3" class="form-control" placeholder="{{__('Enter Description')}}" required></textarea>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Marks') }}:<span class="redstar">*</span></label>
                                    <input type="number" min="0" placeholder="{{__('Enter Per Question Mark')}}" class="form-control " name="per_q_mark" id="exampleInputTitle" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.PassingPercentage') }}:<span class="redstar">*</span></label>
                                    <input type="number" min="0" placeholder="{{__('Enter Passing Percentage')}}" class="form-control" name="p_percent" id="exampleInputTitle" value="60">
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.QuizTimer') }}:<span class="redstar">*</span></label>
                                    <input type="number" min="0" placeholder="{{__('Enter Quiz Time in (mins) Eg:20')}}" class="form-control" name="timer" id="exampleInputTitle" required>
                                </div>
<!--                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Days') }}:</label>
                                    <input type="text" placeholder="Enter Due Days" class="form-control" name="due_days"
                                           id="exampleInputTitle">
                                    <small>(Days after quiz will start when user enroll in course)</small>

                                </div>-->

                            </div>
                            <br>

                            <div class="row">
                                <!-- <div class="col-md-4">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status" checked />
                                        <span class="knob"></span>
                                    </label>


                                </div> -->

                                <div class="col-md-4">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.QuizReattempt') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="quiz_again" checked />
                                        <span class="knob"></span>
                                    </label>


                                </div>


                                <!-- <div class="col-md-4">
                                    <label for="exampleInputTit1e">{{ __('Quiz Type') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="free" checked />
                                        <span class="knob"></span>
                                    </label>


                                </div> -->
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="reset" class="btn btn-danger"><i class="fa fa-ban"></i> {{__('Reset')}}</button>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i>
                                    {{__('Create')}}</button>
                            </div>

                            <div class="clear-both"></div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- script to change status start -->
<script>
    $("#checkboxAll").on('click', function () {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>
<script>
    function getClasses(id) {

        var status = $(this).prop('checked') == true ? 1 : 0;

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{url('/quiz/topic/status/')}}/" + id,
            data: {'status': status, 'id': id},
            success: function (data) {
                console.log(id)
            }
        });
    };

    function quiztopic(id) {

        var status = $(this).prop('checked') == true ? 1 : 0;

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{url('/quiz/topic/status/')}}/" + id,
            data: {'status': status, 'id': id},
            success: function (data) {
                console.log(id)
            }
        });
    };

</script>

<script>
    function  quizreattemp(id) {

        var status = $(this).prop('checked') == true ? 1 : 0;

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{url('/quiz/topic/again/status/')}}/" + id,
            data: {'status': status, 'id': id},
            success: function (data) {
                var warning = new PNotify({
                    title: 'success', text: "{{__('Status Update Successfully')}}", type: 'success', desktop: {
                        desktop: true, icon: 'feather icon-thumbs-down'
                    }
                });
                warning.get().click(function () {
                    warning.remove();
                });
            }
        });
    }
    ;

</script>
<!-- script to change status end -->