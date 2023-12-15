@extends('admin.layouts.master')
@section('title', __('Add Courseclass'))

@section('maincontent')
    @component('components.breadcumb', ['thirdactive' => 'active'])
        @slot('heading')
            {{ __('Home') }}
        @endslot

        @slot('menu1')
            <a href="{{ url('course/create/' . $chap->course_id) }}">{{ __('Course') }}</a>
        @endslot

        @slot('menu2')
            {{ __('Course Classes') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ url('course/create/' . $chap->course_id) }}" class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if (Session::has('error'))
                    <div class="offset-md-3 col-md-offset-3 col-md-6 animated fadeInDown alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif
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
                        @can('course-class.delete')
                            <button type="button" class="btn btn-danger-rgba my-2 " data-toggle="modal"
                                data-target="#bulk_delete4"><i
                                    class="feather icon-trash mr-2"></i>{{ __('Delete Selected') }}</button>

                            <div id="bulk_delete4" class="delete-modal modal fade" role="dialog">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                {{ __('Delete') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <div class="delete-icon"></div>
                                        </div>
                                        <div class="modal-body">
                                            <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                                            <p>{{ __('Do you really want to delete selected item ? This process cannot be undone') }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <form id="bulk_delete_form" method="post"
                                                action="{{ route('courseclass.bulk_delete') }}">
                                                @csrf
                                                @method('POST')
                                                <button type="reset" class="btn btn-secondary"
                                                    data-dismiss="modal">No</button>
                                                <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan

                        @can('course-class.create')
                            <a href="{{ route('courseclass.add', ['id' => $chap->course_id, 'chap' => $chap->id]) }}"
                                class="btn btn-primary-rgba"><i
                                    class="feather icon-plus "></i>{{ __('Add Course Classes') }}</a>
                            {{-- <a href="http://www.webdesign-flash.ro/vs/" target="_blank" class="float-right btn btn-primary-rgba mr-2" ><i class="feather icon-navigation mr-2"></i>{{ __('Encrypt Link') }}</a> --}}
                        @endcan

                        <p class="float-right text-primary p-2 mr-2"> {{ __('Classes Duration:') }}
                            {{ $duration }}{{ __(' hours') }}</p>
                    </div>

                    <div class="card-body">
                        <table id="courseclasses-datatable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>
                                        <input id="checkboxAll4" type="checkbox" class="filled-in" name="checked[]"
                                            value="all" />
                                        <label for="checkboxAll" class="material-checkbox"></label>
                                    </th>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.CourseChapter') }}</th>
                                    <th>{{ __('adminstaticword.Type') }}</th>
                                    <th>{{ __('adminstaticword.Title') }}</th>
                                    <th>{{ __('adminstaticword.Status') }}</th>
                                    <th>{{ __('adminstaticword.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!--courseclass.js is included -->
@section('script')
    <script type="text/javascript">
        $(function() {
            $("#courseclasses-datatable").DataTable({
                language: {
                    searchPlaceholder: "Search class here"
                },

                ordering: false,
                processing: true,
                serverSide: true,
                searchDelay: 2000,

                ajax: "{{ route('chapterclasses', $chap->id) }}",

                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'coursechapter',
                        name: 'coursechapters.chapter_name',
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false
                    },
                ]
            });
        });
    </script>

    <!-- script to change status start -->
    <script>
        function courceclassstatus(id) {
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ url('/course-class/status/') }}/" + id,
                data: {
                    'status': status,
                    'id': id
                },
                success: function(data) {
                    var warning = new PNotify({
                        title: 'success',
                        text: "{{ __('Status Update Successfully') }}",
                        type: 'success',
                        desktop: {
                            desktop: true,
                            icon: 'feather icon-thumbs-down'
                        }
                    });
                    warning.get().click(function() {
                        warning.remove();
                    });
                }
            });
        };
        // to change featured status
        function courceclassfeatured(id) {
            var featured = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ url('/course-class/featured/') }}/" + id,
                data: {
                    'featured': featured,
                    'id': id
                },
                success: function(data) {
                    console.log(id)
                }
            });
        };
    </script>
    <!-- script to change status end -->

    <script>
        $(function() {
            $('#previewvid').on('change', function() {
                if ($('#previewvid').is(':checked')) {
                    $('#document11').show('fast');
                    $('#document22').hide('fast');
                } else {
                    $('#document22').show('fast');
                    $('#document11').hide('fast');
                }
            });

            $("#checkboxAll").on('click', function() {
                $('input.check').not(this).prop('checked', this.checked);
            });
        })
    </script>
@endsection


@section('stylesheets')
    <style type="text/css">
        .modal {
            overflow-y: auto;
        }

        body {
            background-color: #efefef;
        }

        .container-4 input#hyv-search {
            width: 500px;
            height: 30px;
            border: 1px solid #c6c6c6;
            font-size: 10pt;
            float: left;
            padding-left: 15px;
            -webkit-border-top-left-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            -moz-border-top-left-radius: 5px;
            -moz-border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .container-4 input#vimeo-search {
            width: 500px;
            height: 30px;
            border: 1px solid #c6c6c6;
            font-size: 10pt;
            float: left;
            padding-left: 15px;
            -webkit-border-top-left-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            -moz-border-top-left-radius: 5px;
            -moz-border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .container-4 button.icon {
            height: 34px;
            background: #F0F0EF url(../../images/icons/searchicon.png) 10px 1px no-repeat;
            background-size: 24px;
            -webkit-border-top-right-radius: 5px;
            -webkit-border-bottom-right-radius: 5px;
            -moz-border-radius-topright: 5px;
            -moz-border-radius-bottomright: 5px;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            border: 1px solid #c6c6c6;
            width: 50px;
            margin-left: -44px;
            color: #4f5b66;
            font-size: 10pt;
        }

        button#pageTokenNext {
            margin-left: 5px;
            border-radius: 3px;
            margin-bottom: 20px;
        }

        button#vpageTokenNext {
            margin-left: 5px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
    </style>
@endsection
