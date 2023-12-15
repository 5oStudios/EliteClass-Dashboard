@extends('admin.layouts.master')
@section('title','User Fingerprints')
@section('maincontent')
@component('components.breadcumb',['secondaryactive' => 'active'])
@slot('heading')
{{ __('User Fingerprints') }}
@endslot

@slot('menu1')
{{ __('User Fingerprint') }}
@endslot

@endcomponent

<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title"> {{ __('User Fingerprints') }}</h5>
                </div>
                <div style="display:none" id="msg" class="alert alert-success">
                    <span id="res_message"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="userstable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                     <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                     <th>{{ __('Fingerprint ID') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userFingerprints as $fingerprint)
                                <tr>
                                    <td>
                                        {{ $fingerprint->id}}
                                    </td>
                                    <td>
                                        {{ $fingerprint->user->fname}} {{ $fingerprint->user->lname}}
                                    </td>
                                    <td>
                                        {{ $fingerprint->fpjsid}}
                                    </td>
                                    <td>
                                        {{ $fingerprint->created}}
                                    </td>
                                    <td>
                                        {{ $fingerprint->created_at}}
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