@extends('admin.layouts.master')
@section('title', 'All User Wallets')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Wallets') }}
        @endslot

        @slot('menu1')
            {{ __('All User Wallets') }}
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-
                                    label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-title"> {{ __('All User Wallets') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="wallet-datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile') }}</th>
                                        <th>{{ __('Wallet Balance') }}</th>
                                        <th>{{ __('Action') }}</th>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    @foreach ($users as $user)
                                        <?php $i++; ?>
                                        <tr>
                                            <td>
                                                {{ $i }}
                                            </td>
                                            <td>
                                                {{ $user->fname }} {{ $user->lname }}
                                            </td>
                                            <td>
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                {{ $user->mobile }}
                                            </td>
                                            <td>
                                                {{ $user->wallet->balance }}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary-rgba mb-1" data-toggle="modal"
                                                    data-target="#wallet-addtopup{{ $user->id }}"><i
                                                        class="feather icon-plus mr-2"></i>{{ __('Topup') }}</button>
                                                <button class="btn btn-danger-rgba" data-toggle="modal"
                                                    data-target="#wallet-removetopup{{ $user->id }}"><i
                                                        class="feather icon-minus mr-2"></i>{{ __('Topup') }}</button>


                                                <div class="modal fade bd-example-modal-sm"
                                                    id="wallet-addtopup{{ $user->id }}" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleSmallModalLabel">
                                                                    {{ __('Wallet Debit/Credit') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="profile-info-block user-bank-button">

                                                                    <h4 class="">{{ __('Current Balance') }} :


                                                                        <div class="display-inline">
                                                                            @if (isset($user->wallet))
                                                                                <i
                                                                                    class="{{ $currency->icon }}"></i>{{ sprintf('%.2f', strip_tags($user->wallet->balance)) }}
                                                                            @endif
                                                                        </div>
                                                                    </h4>

                                                                    <div class="">{{ __('Add balance to Wallet') }}:
                                                                    </div>

                                                                    <form id="" action="{{ url('wallet/topup') }}"
                                                                        method="POST">
                                                                        @csrf

                                                                        <div class="form-group">



                                                                            <input name="user_id" required type="hidden"
                                                                                value="{{ $user->id }}">
                                                                            <input name="amount" required type="number"
                                                                                class="form-control" value="1.00"
                                                                                placeholder="0.00" min="1"
                                                                                step="0.01"
                                                                                aria-describedby="basic-addon1">
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <label
                                                                                    for="exampleInput">{{ __('Reason') }}:<sup
                                                                                        class="redstar">*</sup></label>
                                                                                <textarea name="reason" onkeyup="countChar(this)" rows="6" class="form-control"
                                                                                    placeholder="{{ _('Please mention reason') }}"></textarea>
                                                                                <div id="count" class="pull-right">
                                                                                    <span id="current_count">0</span>
                                                                                    <span id="maximum_count">/ 300</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <button type="submit" class="btn btn-primary">
                                                                            {{ __('Procceed') }}
                                                                        </button>

                                                                    </form>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="modal fade bd-example-modal-sm"
                                                    id="wallet-removetopup{{ $user->id }}" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger border-danger">
                                                                <h5 class="modal-title" id="exampleSmallModalLabel">
                                                                    {{ __('Wallet Debit/Credit') }}
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="profile-info-block user-bank-button">

                                                                    <h4 class="">{{ __('Current Balance') }} :


                                                                        <div class="display-inline">
                                                                            @if (isset($user->wallet))
                                                                                <i
                                                                                    class="{{ $currency->icon }}"></i>{{ sprintf('%.2f', strip_tags($user->wallet->balance)) }}
                                                                            @endif
                                                                        </div>
                                                                    </h4>

                                                                    <div class="">
                                                                        {{ __('Enter amount to remove from wallet') }}:
                                                                    </div>

                                                                    <form action="{{ url('wallet/removetopup') }}"
                                                                        method="POST">
                                                                        @csrf

                                                                        <div class="form-group">
                                                                            <input name="user_id" required type="hidden"
                                                                                value="{{ $user->id }}">
                                                                            <input name="amount" required type="number"
                                                                                class="form-control" value="1.00"
                                                                                placeholder="0.00" min="1"
                                                                                step="0.01"
                                                                                aria-describedby="basic-addon1">
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <label
                                                                                    for="exampleInput">{{ __('Reason') }}:</label>
                                                                                <textarea name="reason" onkeyup="countChar(this)" rows="6" class="form-control"
                                                                                    placeholder="{{ _('Please mention reason') }}"></textarea>
                                                                                <div id="count" class="pull-right">
                                                                                    <span id="current_count">0</span>
                                                                                    <span id="maximum_count">/ 300</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <button type="submit" class="btn btn-danger">
                                                                            {{ __('Procceed') }}
                                                                        </button>

                                                                    </form>

                                                                </div>
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
    </div>
@endsection


@section('script')
    <script>
        $(function() {
            $('#wallet-datatable').dataTable({
                language: {
                    searchPlaceholder: "Search user here"
                },
                columnDefs: [{
                    "targets": [5],
                    orderable: false,
                    searchable: false
                }, ]

            });
        });

        function countChar(val) {

            var len = val.value.length;
            if (len > 300) {
                val.value = val.value.substring(0, 299);
            } else {
                $('#current_count').text(len);
            }
        };
    </script>
    <!-- script to change status end -->
    <!-- ============================================ -->
@endsection
