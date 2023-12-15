@extends('admin.layouts.master')
@section('title', __('adminstaticword.UsersEnrolled'))
@section('maincontent')

@component('components.breadcumb',['secondaryactive' => 'active'])
    @slot('heading')
    {{ __('adminstaticword.UsersEnrolled'). __(" in")." [ ".$session->title." ]" }}
    @endslot

    @slot('menu1')
    {{__('In-Person Session') }}
    @endslot

    @slot('button')
        <div class="col-md-5 col-lg-5">
            <div class="widgetbar">
                <a href="{{ url('sessions') }}" class="float-right btn btn-primary-rgba mr-2"><i
                        class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
            </div>
        </div>
    @endslot
@endcomponent

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Users') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="enrolled-session-datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.ID') }}</th>
                                    <th>{{ __('adminstaticword.Name') }}</th>
                                    <th>{{ __('adminstaticword.MobileNumber') }}</th>
                                    <th>{{ __('adminstaticword.Email') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrolled as $key => $enroll)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td> {{ $enroll->user->id }} </td>
                                        <td> {{ $enroll->user->fname }} {{ $enroll->user->lname }} </td>
                                        <td> {{ $enroll->user->mobile }} </td>
                                        <td> {{ $enroll->user->email }} </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
	$(function () {
        $('#enrolled-session-datatable').dataTable({
            language: {
                searchPlaceholder: "Search users here"
            },
        
        });
    });
</script>
@endsection
