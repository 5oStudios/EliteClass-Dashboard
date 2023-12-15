@extends('admin.layouts.master')
@section('title',__('Edit QuestionAnswer'))
@section('maincontent')

@component('components.breadcumb',['thirdactive' => 'active'])

@slot('heading')
{{ __('Answers') }}
@endslot

{{-- @slot('menu1')
{{ __('Admin') }}
@endslot --}}

@slot('menu2')
{{ __('Answers') }}
@endslot

@slot('button')
<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    @can('answer.create')
    <a data-toggle="modal" href="#" data-target="#myModalanswer" class="float-right btn btn-primary-rgba">  <i class="feather icon-plus mr-2"></i>{{__('Add Answers')}}</a>
    @endcan
    
    @if(auth()->user()->role == 'admin')
      <a href="{{ url('course/create/'. $que->courses->id) }}" class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
    @elseif(auth()->user()->role == 'instructor')
      <a href="{{ route('instructorquestion.index') }}" class="float-right btn btn-primary-rgba mr-2"><i class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
    @endif
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
          <h5 class="card-box">{{__('All Answers')}}</h5>
        </div>
      <div class="card-body">

      <div class="table-responsive">
        <table id="anss-datatable" class="table table-striped table-bordered">

          <thead>
          
            <th>#</th>
            <th>{{ __('adminstaticword.Question') }}</th>
            <th>{{ __('adminstaticword.Answer') }}</th>
            <th>{{ __('adminstaticword.Status') }}</th>
            <th>{{ __('adminstaticword.Action') }}</th>
          </tr>
          </thead>
          <tbody>
          <?php $i=0;?>
          @foreach($answers as $ans)
          <tr>
          	<?php $i++;?>
          	<td><?php echo $i;?></td>
            	<td>{{strip_tags($ans->question->question)}}</td>
            	<td>{{strip_tags($ans->answer)}}</td> 
            <td>
                @if($ans->status==1)
                  {{ __('adminstaticword.Active') }}
                @else
                  {{ __('adminstaticword.Deactive') }}
                @endif	                    
            </td>
            <td>
               <div class="dropdown">
                <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                    class="feather icon-more-vertical-"></i></button>
                @can(['answer.edit', 'answer.delete'])
                <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                  <a class="dropdown-item" href="{{route('courseanswer.edit',$ans->id)}}"><i class="feather icon-edit mr-2"></i>{{__('Edit')}}</a>
                  
                  <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{$ans->id}}">
                    <i class="feather icon-delete mr-2"></i>{{ __("Delete") }}</a>
                  </a>
                </div>
                @endcan
              </div>
            </td>

            <div class="modal fade bd-example-modal-sm" id="delete{{$ans->id}}" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleSmallModalLabel">{{ __("Delete") }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted">{{__('Do you really want to delete this Bundle ? This process cannot be
                      undone')}}</p>
                  </div>
                  <div class="modal-footer">
                    <form method="post" action="{{url('courseanswer/'.$ans->id)}}" data-parsley-validate
                      class="form-horizontal form-label-left">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}

                      <button type="reset" class="btn btn-gray translate-y-3"
                        data-dismiss="modal">{{ __('adminstaticword.No') }}</button>
                      <button type="submit" class="btn btn-danger">{{ __('adminstaticword.Yes') }}</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            
            
          </tr>
          @endforeach
          
          </tbody>
        </table>
      </div>

    </div>
  </div>
  


  <div class="modal fade" id="myModalanswer" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>

        <div class="box box-primary">
          <div class="panel panel-sum">
            <div class="modal-body">
              <form autocomplete="off" id="demo-form2" method="post" action="{{url('courseanswer/')}}" data-parsley-validate class="form-horizontal form-label-left">
                {{ csrf_field() }}
               
                <input type="hidden" name="instructor_id" class="form-control" value="{{ Auth::User()->id }}"  />
                <input type="hidden" name="ans_user_id" value="{{Auth::user()->id}}" />
           
                <div class="row">
                  <div class="col-md-12">
                    <label  for="exampleInputTit1e">{{ __('adminstaticword.Question') }}:<sup class="redstar">*</sup></label>
                    <br>
                    <textarea rows="3" class="form-control" readOnly>{{$que->question}}</textarea>
                    <input type="hidden" name="question_id" value="{{$que->id}}">
                   
                  </div>
                  <input type="hidden" name="ques_user_id"  value="{{$que->user_id}}" />
                  <input type="hidden" name="course_id"  value="{{$que->course_id}}" />
                </div>
                <br>

                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInput">{{ __('adminstaticword.Answer') }}:<sup class="redstar">*</sup></label>
                    <textarea name="answer" onkeyup="countChar(this)" rows="3" class="form-control" placeholder="{{_('Please Enter Your Answer')}}"></textarea>
                    <div id="count" class="pull-right">
                      <span id="current_count">0</span>
                      <span id="maximum_count">/ 300</span>
                    </div>
                  </div>
                </div>
                <br>

                <div class="col-md-12">
                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                    <label class="switch">
                      <input class="slider" type="checkbox" name="status" checked />
                      <span class="knob"></span>
                    </label>
                </div>
                <br>
        
                <div class="box-footer">
                  <button type="submit" value="Add Answer" class="btn btn-md col-md-3 btn-primary">+  {{ __('adminstaticword.Save') }}</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Model close -->  
  </div>  

  </div>
  <!-- /.row -->

  </div>
    <!-- /.col -->
</div>
@endsection

@section('script')
<script>
  $(function () {
        $('#anss-datatable').dataTable({
            language: {
                searchPlaceholder: "Search answer here"
            },
			columnDefs: [
              {"targets": [4], orderable: false, searchable: false},
          ]
        
        });
  });
  
  function countChar(val){
     
    var len = val.value.length;
     if (len > 300) {
              val.value = val.value.substring(0, 299);
     } else {
              $('#current_count').text(len);
     }     
  };
</script>
@endsection

