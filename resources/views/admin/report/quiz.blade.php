@extends('admin.layouts.master')
@section('title', 'Report')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Report') }}
        @endslot

        @slot('menu1')
            {{ __(' Quiz Report') }}
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('All Quiz Report') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="quiz-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
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
        $(function() {
            $('#quiz-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search quiz report here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,
                ordering: false,

                ajax: "{{ route('quizreport') }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
            });
        });
    </script>
@endsection
