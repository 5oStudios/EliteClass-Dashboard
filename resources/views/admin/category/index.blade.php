@extends('admin.layouts.master')
@section('title', 'All Countries')

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Countries') }}
        @endslot

        @slot('menu1')
            {{ __('Countries') }}
        @endslot

        @section('css')
            <style>
                .img-flag {
                    height: 40px;
                }
            </style>
        @endsection

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    @can('categories.delete')
                        <button type="button" class="btn btn-danger-rgba mx-2" data-toggle="modal" data-target="#bulk_delete"><i
                                class="feather icon-trash mx-2"></i> {{ __('Delete Selected') }}</button>
                    @endcan
                    @can('categories.create')
                        <button type="button" class="btn btn-primary-rgba mx-2" data-toggle="modal" data-target="#myModal">
                            <i class="feather icon-plus mx-2"></i>{{ __('Add Country') }}
                        </button>
                    @endcan
                </div>

                <div id="bulk_delete" class="delete-modal modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <div class="delete-icon"></div>
                            </div>
                            <div class="modal-body">
                                <h4 class="modal-heading">{{ __('Are You Sure ?') }}</h4>
                                <p>{{ __('Do you really want to delete selected ') }}
                                    <b>{{ __('countries') }}</b>{{ __('? This process cannot be undone') }}
                                </p>
                            </div>
                            <div class="modal-footer">
                                <form id="bulk_delete_form" method="post" action="{{ route('categories.bulk_delete') }}">
                                    @csrf
                                    @method('POST')
                                    <button type="reset" class="btn btn-secondary"
                                        data-dismiss="modal">{{ __('No') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('Yes') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModal">{{ __('Add Country') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <form id="demo-form2" method="post" action="{{ route('category.store') }}" data-parsley-validate
                                    class="form-horizontal form-label-left" autocomplete="off" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="c_name">{{ __('adminstaticword.Name') }}:<sup
                                                    class="redstar">*</sup></label>
                                            <input placeholder="Enter country name" type="text" class="form-control"
                                                name="title" required>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="text-dark" for="exampleInputSlug">{{ __('Image') }}: <sup
                                                    class="redstar">*</sup></label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="file">{{ __('Upload') }}</span>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" name="cat_image" class="custom-file-input" id="file"
                                                        aria-describedby="inputGroupFileAddon01" required>
                                                    <label class="custom-file-label"
                                                        for="inputGroupFile01">{{ __('Choose file') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                            <input id="status_toggle" type="checkbox" class="custom_toggle" name="status"
                                                checked />
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                            {{ __('Create') }}</button>
                                    </div>
                                    <div class="clear-both"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria- label="Close">
                            <span aria-hidden="true" style="color:red;">&times;</span>
                        </button>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-box">{{ __('All Countries') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="maincategory-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th> <input id="checkboxAll" type="checkbox" class="filled-in" name="checked[]"
                                                value="all" />
                                            <label for="checkboxAll" class="material-checkbox"></label>
                                        </th>
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Country Name') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
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
    {{-- <script>
        function formatList(state) {
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "{{ asset('flags/48x48') }}/";
            var $state = $(
                '<span><img src="' + baseUrl + state.element.value + '" class="img-flag" /> - ' + state.element.value
                .slice(0, -4) + '</span>'
            );
            return $state;
        }

        function formatSelected(state) {
            if (!state.id) {
                return state.text;
            }

            var baseUrl = "{{ asset('flags/48x48') }}/";
            var $state = $(
                '<span><img class="img-flag" /> - ' + state.element.value.slice(0, -4) + '</span>'
            );

            $state.find("img").attr("src", baseUrl + state.element.value);
            return $state;
        }

        var Country_list = '<?= GuzzleHttp\json_encode($flags) ?>';
        $(".js-example-templating").select2({
            data: Country_list,
            templateResult: formatList,
            templateSelection: formatSelected
        });

        $(".edit-country").select2({
            data: Country_list,
            templateResult: formatList,
            templateSelection: formatSelected
        })

        function edit_Cat(d) {
            $("#demo-form").attr("action", "{{ url('category') }}/" + d.id)
            $("#edit-title").val(d.title);
            $("#edit-status").prop('checked', d.status == 1 ? true : false);
            $(".edit-country").val(d.cat - image).trigger('change');
        }
    </script> --}}

    <script>
        $(function() {
            $('#maincategory-datatable').DataTable({
                language: {
                    searchPlaceholder: "Search country here"
                },

                processing: true,
                serverSide: true,
                searchDelay: 1000,

                ajax: "{{ route('category.index') }}",

                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'id'
                    },
                    {
                        name: 'image',
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'title',
                        data: 'title'
                    },
                    {
                        name: 'slug',
                        data: 'slug'
                    },
                    {
                        name: 'status',
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'action',
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        $(function() {
            $("#sortable").sortable();
            $("#sortable").disableSelection();
        });

        // $("#sortable").sortable({
        //     update: function(e, u) {
        //         var data = $(this).sortable('serialize');

        //         $.ajax({
        //             url: "{{ route('category_reposition') }}",
        //             type: 'get',
        //             data: data,
        //             dataType: 'json',
        //             success: function(result) {
        //                 console.log(data);
        //             }
        //         });
        //     }
        // });

        $("#checkboxAll").on('click', function() {
            $('input.check').not(this).prop('checked', this.checked);
        });
    </script>

    {{-- <script>
        $(document).on("change", ".status2", function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: 'cat-status',
                data: {
                    'status': $(this).is(':checked') ? 1 : 0,
                    'id': $(this).data('id')
                },
                success: function(data) {
                    var warning = new PNotify({
                        title: 'success',
                        text: 'Status Update Successfully',
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
        });
    </script> --}}
@endsection
