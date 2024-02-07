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
                <a data-toggle="modal" data-target="#questionnaire_topic" href="#" class="btn btn-primary-rgba"><i
                        class="feather icon-plus "></i>{{ __('Add Quesionnaire') }} </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="" class="displaytable table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                
                                <th>{{ __('adminstaticword.userName') }}</th>
                                <th>{{ __('adminstaticword.rate') }}</th>
                                <th>{{ __('adminstaticword.hasCommented') }}</th>
                                <th>{{ __('adminstaticword.Action') }}</th>
                        </thead>
                        <?php
/* <tbody>
                            <?php $i = 0; ?>
                            @foreach($topics as $topic)
                            <tr>
                                <?php $i++; ?>
                               
                                <td>{{$topic->title}}</td>
                                
                                <td>{{($topic->appointment )}}</td>
                                
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                            @can('Quesionnaire.edit')
                                                <a class="dropdown-item" href="{{url('admin/Quesionnairetopic/'.$topic->id)}}"><i
                                                        class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                            @endcan
                                            @can('Quesionnaire.view')
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
                                            @can('report.Quesionnaire-report.manage')
                                                <a class="dropdown-item" href="{{route('show.Quesionnairereport', $topic->id)}}"><i
                                                    class="feather icon-file mr-2"></i>{{ __('Show Report') }}</a>
                                            @endcan
                                            @can('Quesionnaire.delete')
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
                                                    <form method="post" action="{{url('admin/Quesionnairetopic/'.$topic->id)}}" class="pull-right">
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

                        </tbody>*/
?>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="questionnaire_topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>Add Questionnaire</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>

            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" id="demo-form2" method="post" action="{{url('admin/Quesionnairetopic/')}}" data-parsley-validate
                              class="form-horizontal form-label-left">
                            {{ csrf_field() }}

                            {{--<input type="hidden" name="course_id" value="{{ $cor->id }}" />--}}

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.QuesionnaireTopic') }}:<span class="redstar">*</span>
                                    </label>
                                    <input type="text" placeholder="{{__('Enter Quesionnaire Topic')}}" class="form-control " name="title"
                                           id="exampleInputTitle" required>
                                </div>
                            </div>
                            <br>


                            
                            <!-- Dynamic Question Fields Container -->
                            <div id="questionFieldsContainer">
                            <label for="question1">Question 1:</label>
                            <input type="text" class="form-control" name="questions[0]" id="question1" required>
                            
                            <label for="question1">Question 2:</label>
                            <input type="text" class="form-control" name="questions[1]" id="question2" required>
                            </div>
                                <!-- Add Question Button -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success ms-auto" onclick="addQuestionField()">Add Question</button>
                                    </div>
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

<script>
    // Counter for dynamic question fields
    let questionCounter = 3;

    function addQuestionField() {
        // Create a new text field for the question
        const questionField = document.createElement('div');
        questionField.innerHTML = `
            <label for="question${questionCounter}">Question ${questionCounter}:</label>
            <input type="text" class="form-control" name="questions[`${questionCounter-1}`]" id="question${questionCounter}" required>
        `;

        // Append the new question field to the container
        document.getElementById('questionFieldsContainer').appendChild(questionField);

        // Increment the question counter for the next question
        questionCounter++;
    }
</script>


<!-- script to change status start -->

<!-- script to change status end -->