@extends('admin.layouts.master')
@section('title','Edit Quiztopic')
@section('maincontent')
@component('components.breadcumb',['thirdactive' => 'active'])
@slot('heading')
{{ __('Home') }}
@endslot
@slot('menu1')
{{ __('Admin') }}
@endslot
@slot('menu2')
{{ __(' Edit Questionnaire') }}
@endslot
@slot('button')
<div class="col-md-5 col-lg-5">
<a href="{{ url('course/create/'. $questionnaire['course_id']) }}" class="float-right btn btn-primary-rgba"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
</div>
@endslot
@endcomponent

<div class="contentbar">
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
            @if(count($questionnaire['students']) !== 0)
            <div class="card-header">
                <a data-toggle="modal" data-target="#questionnaire_topic" href="#" class="btn btn-warning-rgba"><i
                        class="feather icon-edit "></i>  {{ __('Edit Quesionnaire') }} </a>
                    </div>
            @endif
            <div class="card-body">
                <div class="table-responsive">
                    <table id="" class="displaytable table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('adminstaticword.Action') }}</th>
                        </thead>
                     
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($questionnaire['students'] as $student)
                            <tr>
                                <?php $i++; ?>
                               
                                <td>{{$student['fname']}} {{$student['lname']}}</td>
                                
                                <td>{{($student['email'] )}}</td>
                                <td>{{($student['answer_date'] )}}</td>
                                
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                                <a class="dropdown-item" href="{{url('admin/questionnaires/'.$student['id'].'/edit')}}"><i
                                                        class="feather icon-eye mr-2"></i>{{ __('view') }}</a>
                                           
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

<div class="modal fade" id="questionnaire_topic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>Edit Questionnaire</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>

            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" id="demo-form2" method="post" action="{{url('admin/questionnaires/'.$questionnaire['id'])}}" data-parsley-validate
                              class="form-horizontal form-label-left">
                            {{ csrf_field() }}
                            {{method_field("PUT")}}

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.QuesionnaireTopic') }}:<span class="redstar">*</span>
                                    </label>
                                    <input type="text" placeholder="{{__('Enter Quesionnaire Topic')}}" class="form-control " name="title"
                                           id="exampleInputTitle" value="{{ old('questionnaire_title', $questionnaire['questionnaire_title']) }}" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTitle1">{{ __('adminstaticword.QuesionnaireDate') }}:<span class="redstar">*</span>
                                    </label>
                                    <input type="date" placeholder="{{__('Enter Quesionnaire date')}}" class="form-control " name="appointment"
                                           id="exampleInputTitle1" value="{{ old('questionnaire_appointment', $questionnaire['questionnaire_appointment']) }}" required>
                                </div>
                            </div>
                            <br>
                            <!-- Dynamic Question Fields Container -->
                            <div id="questionFieldsContainer"  style="max-height:300px ;overflow-y: auto;">
                            <?php $i = 0; ?>
                            @foreach($questionnaire['questions'] as $q)
                            <label for="question{{$i}}">Question {{$i+1}}:</label>
                            <input type="hidden" name="questions[{{$i}}][id]" value="{{ $q['id'] }}" />
                            <input type="text" class="form-control" name="questions[{{ $i }}][title]" id="question{{ $i }}" value="{{ old('questions.'.$i.'.title', $q['title']) }}" required>
                            <?php $i++; ?>
                            @endforeach
                            </div>
                                
                              
                                
                            <!-- Add Question Button -->
                            <div class="row mt-4">
                                <div class="col-md-12 ms-auto">
                                    <button type="button" class="btn btn-success" onclick="addQuestionField({{$i}})">Add Question</button>
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
</div>
<script>
    // Counter for dynamic question fields
    
    function addQuestionField(index) {
        // Create a new text field for the question
        const questionField = document.createElement('div');
        questionField.innerHTML = `
        <input type="hidden" name="questions[${index}][id]" value="" />
            <label for="question${index+1}">Question ${index+1}:</label>
            <input type="text" class="form-control" name="questions[${index}][title]" id="question${index+1}" required>
        `;

        // Append the new question field to the container
        document.getElementById('questionFieldsContainer').appendChild(questionField);

        // Increment the question counter for the next question
        questionCounter++;
    }
</script>
@endsection

