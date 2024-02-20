@extends('admin.layouts.master')
@section('title','Create a new user')
@section('breadcum')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Users') }}
@endslot

@slot('menu1')
{{ __('Users') }}
@endslot

@slot('button')

<div class="col-md-5 col-lg-5">
  <div class="widgetbar">
    <a href="{{route('user.index')}}" class="float-right btn btn-primary-rgba mr-2"><i
        class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a> </div>
</div>
@endslot
@endcomponent

<div class="contentbar">
  <div class="row">
    <div class="col-lg-12">
      @if ($errors->any())  
      <div class="alert alert-danger" role="alert">
          @foreach($errors->all() as $error)     
          <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true" style="color:red;">&times;</span></button></p>
          @endforeach  
      </div>
      @endif
      <div class="card m-b-30">
        <div class="card-header ">
          <h5 class="box-tittle">{{ __('bulkAdd') }} {{ __('adminstaticword.User') }}</h5>
        </div>
        <div class="card-body">
        
        <form autocomplete="off" action="{{ route('user.bulk_store') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group ml-2">
                    <label class="text-dark" for="exampleInputSlug">{{ __('upload') }} {{ __('CSV') }}: </label>
                    <div class="custom-file">
                        <input type="file" required class="custom-file-input" id="exampleInputSlug" name="csvFile" accept=".csv">
                        <label class="custom-file-label" for="exampleInputSlug">{{ __('Choose file') }}</label>
                    </div>
                </div>
            </div>  
            <div class="row">
                <div class="form-group">
                  <a href="{{route('user.bulk_store_sample')}}" class="btn btn-secondary me-4"> {{__('downloadSample')}} </a>
                    <!-- <button type="button" onClick="" class="btn btn-secondary me-4">{{ __('downloadSample') }}</button> -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>

                </div>
            </div>       
          </form>
            <div class="clear-both"></div>
        </div>

      </div>
    </div>
  </div>
</div>
</div>
@endsection



