@extends('admin.layouts.master')
@section('title','All Quiz')
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Quiz') }}
@endslot

@slot('menu1')
{{ __('Quiz') }}
@endslot
@slot('button')

<div class="col-md-5 col-lg-5">
    <div class="widgetbar">
        {{-- @if($topic->type == NULL)
        <a href="{{ route('import.quiz') }}"  class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-plus mr-2"></i>{{ __('Import Quiz') }}</a>
        <a data-toggle="modal" data-target="#myModalquiz" href="#"  class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-plus mr-2"></i>{{ __('Add Question') }}</a>
        @endif --}}

        @can('question.create')
            <a data-toggle="modal" data-target="#myModalquiz" href="#"  class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-plus mr-2"></i>{{ __('Add Question') }}</a>
        @endcan

        {{-- @if($topic->type == '1')
        <a data-toggle="modal" data-target="#myModalquizsubject"  class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-plus mr-2"></i>{{ __('Add Question') }}</a>
        @endif --}}

        <a href="{{ url('course/create/'. $topic->courses->id) }}" class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>

    </div>                        
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
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Quiz') }}</h5>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                                <tr>

                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Course') }}</th>
                                    <th>{{ __('adminstaticword.Topic') }}</th>
                                    <th>{{ __('adminstaticword.Question') }}</th>
                                    @if($topic->type == NULL)
                                        <th>{{ __('adminstaticword.A') }}</th>
                                        <th>{{ __('adminstaticword.B') }}</th>
                                        <th>{{ __('adminstaticword.C') }}</th>
                                        <th>{{ __('adminstaticword.D') }}</th>
                                        <th>{{ __('adminstaticword.Answer') }}</th>
                                    @endif
                                    @can(['question.edit','question.delete'])
                                        <th>{{ __('adminstaticword.Action') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach($quizes as $quiz)
                                <?php $i++; ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>{{$quiz->courses->title}}</td>
                                    <td>{{$quiz->topic->title}}</td>
                                    <td>{{$quiz->question}}</td>
                                    @if($topic->type == NULL)
                                    <td>{{$quiz->a}}</td>
                                    <td>{{$quiz->b}}</td>
                                    <td>{{$quiz->c}}</td>
                                    <td>{{$quiz->d}}</td>
                                    <td>{{$quiz->answer}}</td>
                                    @endif
                                    @can(['question.edit','question.delete'])
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
                                            <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                                @if($topic->type == NULL)
                                                <a class="dropdown-item"  data-toggle="modal" data-target="#myModaledit{{$quiz->id}}" ><i class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                                @endif
                                                @if($topic->type == '1')
                                                <a class="dropdown-item" data-toggle="modal" data-target="#myModaleditsub{{$quiz->id}}" href="#"><i class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                                @endif

                                                <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $quiz->id }}" >
                                                    <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
                                                </a>
                                            </div>
                                        </div>
                                            
                                        <!-- delete Modal start -->
                                        <div class="modal fade bd-example-modal-sm" id="delete{{$quiz->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                        <form method="post" action="{{url('admin/questions/'.$quiz->id)}}" class="pull-right">
                                                            {{csrf_field()}}
                                                            {{method_field("DELETE")}}
                                                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('Yes') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- delete Model ended -->
                                    </td>
                                    @endcan


                                </tr>

                                <!--Model for edit question-->
                            <div class="modal fade" id="myModaledit{{$quiz->id}}" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="my-modal-title">
                                                <b>{{ __('Edit Question') }}</b>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>

                                        </div>
                                        <div class="box box-primary">
                                            <div class="panel panel-sum">
                                                <div class="modal-body">
                                                    <form autocomplete="off" id="demo-form2" method="POST" action="{{route('questions.update', $quiz->id)}}"
                                                          data-parsley-validate class="form-horizontal form-label-left"
                                                          enctype="multipart/form-data">
                                                        {{ csrf_field() }}
                                                        {{ method_field('PUT') }}

                                                        <input type="hidden" name="course_id" value="{{ $topic->course_id }}" />

                                                        <input type="hidden" name="topic_id" value="{{ $topic->id }}" />
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="col-md-12">
                                                                    <label for="exampleInputTit1e">{{ __('adminstaticword.Question') }}:<sup
                                                                            class="redstar">*</sup></label>
                                                                    <textarea name="question" rows="6" class="form-control"
                                                                              placeholder="{{__('Enter Your Question')}}" required>{{ $quiz->question }}</textarea>
                                                                    <br>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="exampleInputDetails">{{ __('adminstaticword.Answer') }}:<sup
                                                                            class="redstar">*</sup></label>
                                                                    <select style="width: 100%" name="answer"
                                                                            class="form-control select2" required>
                                                                        <option {{ $quiz->answer == 'A' ? 'selected' : ''}} value="A">
                                                                            {{ __('adminstaticword.A') }}</option>
                                                                        <option {{ $quiz->answer == 'B' ? 'selected' : ''}} value="B">
                                                                            {{ __('adminstaticword.B') }}</option>
                                                                        <option {{ $quiz->answer == 'C' ? 'selected' : ''}} value="C">
                                                                            {{ __('adminstaticword.C') }}</option>
                                                                        <option {{ $quiz->answer == 'D' ? 'selected' : ''}} value="D">
                                                                            {{ __('adminstaticword.D') }}</option>
                                                                    </select> 
                                                                </div>
                                                                <!-- <br>
                                                                <h4 class="extras-heading">{{ __('Video And Image For Question') }}</h4>
                                                                <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">


                                                                    <label for="exampleInputDetails">{{ __('Add Video To Question') }} :<sup class="redstar">*</sup></label>
                                                                    <input type="text" name="question_video_link" class="form-control"
                                                                           placeholder="https://myvideolink.com/embed/.." />
                                                                    <small class="text-danger">{{ $errors->first('question_video_link') }}</small>
                                                                    <small class="text-muted text-info"> <i class="text-dark feather icon-help-circle"></i> {{ __('YouTube And Vimeo Video Support (Only Embed Code Link)') }}</small>
                                                                </div> -->
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="col-md-12">

                                                                    <label for="exampleInputDetails">{{ __('adminstaticword.AOption') }} :<sup
                                                                            class="redstar">*</sup></label>
                                                                    <input type="text" name="a" value="{{ $quiz->a }}" class="form-control"
                                                                           placeholder="{{_('Enter Option A')}}" required />
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <label for="exampleInputDetails">{{ __('adminstaticword.BOption') }} :<sup
                                                                            class="redstar">*</sup></label>
                                                                    <input type="text" name="b" value="{{ $quiz->b }}" class="form-control"
                                                                           placeholder="{{__('Enter Option B')}}" required/>
                                                                </div>

                                                                <div class="col-md-12">

                                                                    <label for="exampleInputDetails">{{ __('adminstaticword.COption') }} :<sup
                                                                            class="redstar">*</sup></label>
                                                                    <input type="text" name="c" value="{{ $quiz->c }}" class="form-control"
                                                                           placeholder="{{__('Enter Option C')}}" required/>
                                                                </div>

                                                                <div class="col-md-12">

                                                                    <label for="exampleInputDetails">{{ __('adminstaticword.DOption') }} :<sup
                                                                            class="redstar">*</sup></label>
                                                                    <input type="text" name="d" value="{{ $quiz->d }}" class="form-control"
                                                                           placeholder="{{__('Enter Option D')}}" required/>
                                                                </div>
                                                            </div>
                                                            <!-- <div class="col-md-6">
                                                                <label class="text-dark" for="exampleInputSlug">{{ __('adminstaticword.Image') }}: </label>

                                                                <div class="input-group mb-3">

                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                                                    </div>


                                                                    <div class="custom-file">

                                                                        <input type="file" name="question_img" class="custom-file-input" id="question_img"
                                                                               aria-describedby="inputGroupFileAddon01">
                                                                        <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                                    </div>
                                                                </div>

                                                            </div> -->
                                                        </div>
                                                        <br/>
                                                        <div class="form-group">
                                                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                                                {{ __('Reset') }}</button>
                                                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
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
                            <!--Model close -->
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModalquiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> {{ __('adminstaticword.Add') }} {{ __('adminstaticword.Question') }}
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="box box-primary">
                    <div class="panel panel-sum">
                        <div class="modal-body">
                            <form autocomplete="off" id="demo-form2" method="post" action="{{route('questions.store')}}" data-parsley-validate
                                  class="form-horizontal form-label-left" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <input type="hidden" name="course_id" value="{{ $topic->course_id }}" />

                                <input type="hidden" name="topic_id" value="{{ $topic->id }}" />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <label for="exampleInputTit1e">{{ __('adminstaticword.Question') }}:<sup
                                                    class="redstar">*</sup></label>
                                            <textarea name="question" rows="6" class="form-control" placeholder="{{__('Enter Your Question')}}" required></textarea>
                                            <br>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="exampleInputDetails">{{ __('adminstaticword.Answer') }}:<sup
                                                    class="redstar">*</sup></label>
                                            <select style="width: 100%" name="answer" class="form-control select2" required>
                                                <option value="none" selected disabled hidden>
                                                    {{ __('adminstaticword.SelectanOption') }}
                                                </option>
                                                <option value="A">{{ __('adminstaticword.A') }}</option>
                                                <option value="B">{{ __('adminstaticword.B') }}</option>
                                                <option value="C">{{ __('adminstaticword.C') }}</option>
                                                <option value="D">{{ __('adminstaticword.D') }}</option>
                                            </select>
                                        </div>
                                        <!-- <br>
                                        <h4 class="extras-heading">{{ __('Video And Image For Question') }}</h4>
                                        <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">


                                            <label for="exampleInputDetails">{{ __('Add Video To Question') }} :<sup class="redstar">*</sup></label>
                                            <input type="text" name="question_video_link" class="form-control"
                                                   placeholder="https://myvideolink.com/embed/.." />
                                            <small class="text-danger">{{ $errors->first('question_video_link') }}</small>
                                            <small class="text-muted text-info"> <i class="text-dark feather icon-help-circle"></i> {{ __('Back') }}YouTube And Vimeo Video Support (Only Embed Code Link)</small>
                                        </div> -->
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <label for="exampleInputDetails">{{ __('adminstaticword.AOption') }} :<sup
                                                    class="redstar">*</sup></label>
                                            <input type="text" name="a" class="form-control" placeholder="{{__('Enter Option A')}}" required/>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="exampleInputDetails">{{ __('adminstaticword.BOption') }} :<sup
                                                    class="redstar">*</sup></label>
                                            <input type="text" name="b" class="form-control" placeholder="{{__('Enter Option B')}}" required/>
                                        </div>

                                        <div class="col-md-6">

                                            <label for="exampleInputDetails">{{ __('adminstaticword.COption') }} :<sup
                                                    class="redstar">*</sup></label>
                                            <input type="text" name="c" class="form-control" placeholder="{{__('Enter Option C')}}" required/>
                                        </div>

                                        <div class="col-md-6">

                                            <label for="exampleInputDetails">{{ __('adminstaticword.DOption') }} :<sup
                                                    class="redstar">*</sup></label>
                                            <input type="text" name="d" class="form-control" placeholder="{{__('Enter Option D')}}" required/>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <label class="text-dark" for="exampleInputSlug">{{ __('adminstaticword.Image') }}: </label>

                                        <div class="input-group mb-3">

                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                            </div>


                                            <div class="custom-file">

                                                <input type="file" name="question_img" class="custom-file-input" id="question_img"
                                                       aria-describedby="inputGroupFileAddon01">
                                                <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                                            </div>
                                        </div>

                                    </div> -->
                                </div>
                                <br/>

                                <div class="form-group">
                                    <button type="reset" class="btn btn-danger"><i class="fa fa-ban"></i> {{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i>
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
    <!--Model close -->
    @endsection

@section('scripts')


@endsection