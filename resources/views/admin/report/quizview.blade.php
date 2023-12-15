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

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('quizreport') }}" class="btn btn-primary-rgba mx-2"><i
                            class="feather icon-arrow-left mx-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
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
                            <table id="quizreport-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Course') }}</th>
                                        <th>{{ __('Quiz') }} </th>
                                        <th>{{ __('Attempt') }} </th>
                                        <th>{{ __('Marks Get') }}</th>
                                        <th>{{ __('Total Marks') }}</th>
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
            $('#quizreport-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search quiz report here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,
                ordering: false,

                ajax: "{{ route('quizre', request()->id) }}",

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
                        data: 'user.email',
                        name: 'user.email'
                    },
                    {
                        data: 'courses.title',
                        name: 'courses.title'
                    },
                    {
                        data: 'topic.title',
                        name: 'topic.title'
                    },
                    {
                        data: 'attempt',
                        name: 'attempt'
                    },
                    {
                        data: 'marks_obtained',
                        name: 'marks_obtained'
                    },
                    {
                        data: 'total_marks',
                        name: 'total_marks'
                    }
                ],
            });
        });
    </script>
@endsection
