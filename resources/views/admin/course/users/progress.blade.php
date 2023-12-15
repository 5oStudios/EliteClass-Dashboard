@extends('admin.layouts.master')
@section('title', __('adminstaticword.UserProgress'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('adminstaticword.UserProgress') . __(' in') . ' [ ' . $progress->courses->title . ' ]' }}
        @endslot

        @slot('menu1')
            {{ __('Lessons') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('course.users', $progress->course_id) }}" class="btn btn-primary-rgba mx-2"><i
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
                        <h5 class="box-title">{{ __('adminstaticword.ReadLessons') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="readclasses-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>{{ __('adminstaticword.ID') }}</th>
                                        <th>{{ __('adminstaticword.Title') }}</th>
                                        <th>{{ __('adminstaticword.LessonType') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
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
            $('#readclasses-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search lessons here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,
                ordering: false,

                ajax: "{{ route('course.user.progress', ['course_id' => $progress->course_id, 'user_id' => $progress->user_id]) }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'type',
                        name: 'type',
                    }
                ],
            });
        });
    </script>
@endsection
