@extends('admin.layouts.master')
@section('title', __('adminstaticword.UsersEnrolled'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('adminstaticword.UsersEnrolled') . __(' in') . ' [ ' . $course->title . ' ]' }}
        @endslot

        @slot('menu1')
            {{ __('Courses') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ route('course.index') }}" class="btn btn-primary-rgba mx-2"><i
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
                        <h5 class="box-title">{{ __('All Users') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="defaultTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="course-tab" data-toggle="tab" href="#course" role="tab"
                                    aria-controls="course" aria-selected="true">{{ __('adminstaticword.Course') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="chapters-tab" data-toggle="tab" href="#chapters" role="tab"
                                    aria-controls="chapters"
                                    aria-selected="false">{{ __('adminstaticword.CourseChapters') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="defaultTabContent">

                            <!-- === course start ======== -->
                            <div class="tab-pane fade show active" id="course" role="tabpanel"
                                aria-labelledby="course-tab">
                                <!-- === User enrolled table start ======== -->
                                <div class="table-responsive">
                                    <table id="userEnroll-datatable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('adminstaticword.ID') }}</th>
                                                <th>{{ __('adminstaticword.Name') }}</th>
                                                <th>{{ __('adminstaticword.MobileNumber') }}</th>
                                                <th>{{ __('adminstaticword.Email') }}</th>
                                                <th>{{ __('adminstaticword.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- === User enrolled table end ===========-->
                            </div>
                            <!-- === course end ======== -->

                            <!-- === chapters start ======== -->
                            <div class="tab-pane fade" id="chapters" role="tabpanel" aria-labelledby="chapters-tab">
                                <div class="row">
                                    <div class="col-lg-5 col-xl-3">
                                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                            aria-orientation="vertical">
                                            @foreach ($course->chapter as $key => $chapter)
                                                <a class="nav-link mb-2 @if ($loop->first) show active @endif"
                                                    data-toggle="pill" href="#v-pills-chapter{{ $key + 1 }}"
                                                    role="tab" aria-selected="true">{{ $chapter->chapter_name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Start col -->
                                    <div class="col-lg-7 col-xl-9">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            <!-- User enrolled table start -->
                                            @foreach ($course->chapter as $key => $chapter)
                                                <div class="tab-pane fade @if ($loop->first) show active @endif"
                                                    id="v-pills-chapter{{ $key + 1 }}" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="userEnroll{{ $key }}-datatable"
                                                            class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>{{ __('adminstaticword.ID') }}</th>
                                                                    <th>{{ __('adminstaticword.Name') }}</th>
                                                                    <th>{{ __('adminstaticword.MobileNumber') }}</th>
                                                                    <th>{{ __('adminstaticword.Email') }}</th>
                                                                    <th>{{ __('adminstaticword.Action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($chapter->enrolled as $key => $enrolled)
                                                                    <tr>
                                                                        <td> {{ $key + 1 }} </td>
                                                                        <td> {{ $enrolled->user->id }} </td>
                                                                        <td> {{ $enrolled->user->fname }}
                                                                            {{ $enrolled->user->lname }} </td>
                                                                        <td> {{ $enrolled->user->mobile }} </td>
                                                                        <td> {{ $enrolled->user->email }} </td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <button
                                                                                    class="btn btn-round btn-outline-primary"
                                                                                    type="button"
                                                                                    id="CustomdropdownMenuButton1"
                                                                                    data-toggle="dropdown"
                                                                                    aria-haspopup="true"
                                                                                    aria-expanded="false"><i
                                                                                        class="feather icon-more-vertical"></i></button>
                                                                                <div class="dropdown-menu"
                                                                                    aria-labelledby="CustomdropdownMenuButton1">

                                                                                    <a class="dropdown-item"
                                                                                        href="{{ route('course.user.progress', ['course_id' => $course->id, 'user_id' => $enrolled->user->id, 'chapter_id' => $chapter->id]) }}"><i
                                                                                            class="feather icon-bar-chart mx-2"></i>{{ __('View Progress') }}</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <!-- User enrolled table end -->
                                        </div>
                                    </div>
                                    <!-- End col -->
                                </div>
                            </div>
                            <!-- === chapters end ======== -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var chapters = @json($course->chapter);

        $(function() {
            $('#userEnroll-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search users here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 2000,
                ordering: false,

                ajax: "{{ route('course.users', $course->id) }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'user.id',
                        name: 'user.id',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'user.fname'
                    },
                    {
                        data: 'user.mobile',
                        name: 'user.mobile',
                    },
                    {
                        data: 'user.email',
                        name: 'user.email',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
            });

            for (let index = 0; index < chapters.length; index++) {
                $(`#userEnroll${index}-datatable`).DataTable({
                    language: {
                        searchPlaceholder: "Search users here"
                    },
                    columnDefs: [{
                        targets: [5],
                        orderable: false,
                        searchable: false
                    }, ]

                });
            }
        });
    </script>
@endsection
