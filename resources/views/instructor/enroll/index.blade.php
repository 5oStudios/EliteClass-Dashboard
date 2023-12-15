@extends('admin.layouts.master')
@section('title','All User')
@section('maincontent')

@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('Students') }}
@endslot

@slot('menu1')
{{ __('Students') }}
@endslot
@endcomponent

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title"> {{ __('Enrolled Students') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="userstable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.Name') }}</th>
                                    <th>{{ __('adminstaticword.Email') }}</th>
                                    <th>{{ __('adminstaticword.Mobile') }}</th>
                                    <th>{{ __('adminstaticword.Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $i => $order)
                                <tr>
                                    <td>
                                        {{ $i + 1 }}
                                    </td>
                                    <td>
                                        {{ $order->user->fname}} {{ $order->user->lname}}
                                    </td>
                                    <td>
                                        {{ $order->user->email}}
                                    </td>
                                    <td>
                                        {{ $order->user->mobile}}
                                    </td>
                                    <td>
                                       {{ $order->user->status == '1' ? 'Acitve' : 'Not active' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>                                   
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End col -->
    </div>
    <!-- End row -->
</div>
@endsection

@section('script')
<script>
$(function () {
    $('#userstable').dataTable({
        language: {
            searchPlaceholder: "Search student here"
        },
    });
});
</script>
@endsection
