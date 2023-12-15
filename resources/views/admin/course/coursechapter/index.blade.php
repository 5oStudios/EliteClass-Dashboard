<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                @can('course-chapter.delete')
                    <button type="button" class=" btn btn-danger-rgba my-2" data-toggle="modal"
                        data-target="#bulk_delete3"><i
                            class="feather icon-trash my-2"></i>{{ __('Delete Selected') }}</button>
                @endcan
                @can('course-chapter.create')
                    <a data-toggle="modal" data-target="#myModalp" href="#" class="btn btn-primary-rgba my-2"><i
                            class="feather icon-plus my-2"></i>{{ __('Add Course Chapter') }}</a>
                    <a data-toggle="modal" data-target="#copyChapterModal" href="#" class="btn btn-primary-rgba my-2"><i
                            class="feather icon-plus my-2"></i>{{ __('Copy Course Chapter') }}</a>
                    <a data-toggle="modal" data-target="#sessionModal" href="#" class="btn btn-primary-rgba my-2"
                        id="addsession"><i class="feather icon-plus my-2"></i>{{ __('Add Session') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <table id="" class="displaytable table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>
                                <input id="checkboxAllcoursechapter" type="checkbox" class="filled-in" name="checked[]"
                                    value="all" />
                                <label for="checkboxAll" class="material-checkbox"></label> #
                            </th>
                            <th>{{ __('adminstaticword.ChapterName') }}</th>
                            <th>{{ __('adminstaticword.Status') }}</th>
                            <th>{{ __('adminstaticword.Action') }}</th>

                        </tr>
                    </thead>
                    <tbody id="sortable-chapter">
                        <?php $i = 0; ?>
                        @foreach ($coursechapter as $key => $cat)
                            <tr class="sortable row1" data-id="{{ $cat->id }}">
                                <td>
                                    <input type="checkbox" form="bulk_delete_form3"
                                        class="filled-in material-checkbox-input check" name="checked[]"
                                        value="{{ $cat->id }}" id="checkbox{{ $cat->id }}">
                                    <label for="checkbox" class="material-checkbox"></label>
                                    {{ $key + 1 }}

                                    <!-- =============== -->
                                    <div id="bulk_delete3" class="delete-modal modal fade" role="dialog">
                                        <div class="modal-dialog modal-sm">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <div class="delete-icon"></div>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <h4 class="modal-heading">{{ __('Are You Sure') }} ?</h4>
                                                    <p>{{ __('Do you really want to delete selected item ? This process cannot be undone') }}.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form id="bulk_delete_form3" method="post"
                                                        action="{{ route('coursechapter.bulk_delete') }}">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="reset" class="btn btn-gray translate-y-3"
                                                            data-dismiss="modal">{{ __('No') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-danger">{{ __('Yes') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- =============== -->
                                </td>
                                <td>{{ $cat->chapter_name }}</td>
                                <td>
                                    @if (auth()->user()->role == 'admin')
                                        <label class="switch">
                                            <input class="slider" type="checkbox" data-id="{{ $cat->id }}"
                                                name="status" {{ $cat->status == '1' ? 'checked' : '' }}
                                                onchange="courcechapter('{{ $cat->id }}')" />
                                            <span class="knob"></span>
                                        </label>
                                    @else
                                        {{ $cat->status == '1' ? 'Active' : 'Not active' }}
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-round btn-outline-primary" type="button"
                                            id="CustomdropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                            @can('course-chapter.edit')
                                                <a class="dropdown-item" href="{{ url('coursechapter/' . $cat->id) }}"><i
                                                        class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
                                            @endcan
                                            @can('course-chapter.edit')
                                                <form action="{{ route('course.chapter.duplicate', $cat->id) }}"
                                                    method="POST">
                                                    {{ csrf_field() }}

                                                    <button type="Submit" class="dropdown-item">
                                                        <i class="feather icon-copy mr-2"></i>{{ __('Duplicate') }}
                                                    </button>
                                                </form>
                                            @endcan
                                            @if ($cat->type_id == null)
                                                <a class="dropdown-item"
                                                    href="{{ route('chapterclasses', ['id' => $cat->id]) }}"><i
                                                        class="feather icon-edit mr-2"></i>{{ __('adminstaticword.CourseClass') }}</a>
                                            @endif
                                            @can('course-chapter.delete')
                                                <a class="dropdown-item btn btn-link" data-toggle="modal"
                                                    data-target="#delete{{ $cat->id }}">
                                                    <i class="feather icon-delete mr-2"></i>{{ __('Delete') }}</a>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="modal fade bd-example-modal-sm" id="delete{{ $cat->id }}"
                                        tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleSmallModalLabel">
                                                        {{ __('Delete') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4>{{ __('Are You Sure ?') }}</h4>
                                                    <p>{{ __('Do you really want to delete') }}
                                                        <b>{{ $cat->courses->title }}</b> ?
                                                        {{ __('This process cannot be undone.') }}
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="post"
                                                        action="{{ url('coursechapter/' . $cat->id) }}"
                                                        class="pull-right">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="reset" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('No') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Yes') }}</button>
                                                    </form>
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

<div class="modal fade" id="myModalp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>{{ __('Add Course Chapter') }}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" method="post" action="{{ route('coursechapter.store') }}"
                            data-parsley-validate class="form-horizontal form-label-left"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <select class="d-none" name="course_id" class="form-control select2">
                                <option value="{{ $cor->id }}">{{ $cor->title }}</option>
                            </select>

                            <div class="row">
                                <div class="col-md-9">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.ChapterName') }}: <span
                                            class="redstar">*</span>
                                    </label>
                                    <input type="text" placeholder="{{ __('Enter chapter name') }}"
                                        class="form-control" name="chapter_name" id="exampleInputTitle"
                                        value="" required>
                                </div>

                                @if ($cor->installment == 1)
                                    <div class="col-md-3">
                                        <label for="unlock_installment">{{ __('adminstaticword.Installment') }}:<span
                                                class="text-danger">*</span></label>
                                        <select id="unlock_installment" name="unlock_installment"
                                            {{ $cor->installment == 1 ? 'required' : '' }}
                                            class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('Select an option') }}</option>
                                            @foreach ($installments as $i => $c)
                                                <option value="{{ ++$i }}">
                                                    {{ $i . ' (' . $c->amount . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <br>

                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputTit1e">{{ __('Detail') }}: <span
                                            class="redstar">*</span>
                                    </label>
                                    <textarea rows="3" placeholder="{{ __('Write something here...') }}" class="form-control" name="detail"></textarea>
                                </div>
                            </div>
                            <br> --}}

                            <div class="row">
                                <div class="col-md-6" id="purchasableBox1">
                                    <label for="purchasable1">{{ __('Price or Not') }}: <span
                                            class="redstar">*</span></label><br>
                                    <select id="is_purchasable1" name="is_purchasable" class="form-control select2"
                                        required>
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}</option>
                                        <option value="1"> {{ __('Price') }} </option>
                                        <option value="0"> {{ __('Without Price') }} </option>
                                    </select>
                                </div>
                                <div id="priceBox1" class="col-md-6" style="display:none">
                                    <label for="exampleInputTit1e">{{ __('Price') }}: <span
                                            class="redstar">*</span>
                                    </label>
                                    <input type="number" min="0" id="price1"
                                        step="0.001"placeholder="{{ __('Enter price') }}" class="form-control"
                                        name="price">
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                {{-- <div class="col-md-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" name="file"
                                                id="file">Upload</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file"
                                                aria-describedby="inputGroupFileAddon01">
                                            <label class="custom-file-label" for="inputGroupFile01">Choose
                                                file</label>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status" checked />
                                        <span class="knob"></span>
                                    </label>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    {{ __('Reset') }}</button>
                                <button type="submit" class="btn btn-primary-rgba"><i
                                        class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>
                            <div class="clear-both"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="copyChapterModal" tabindex="-1" role="dialog"
    aria-labelledby="copyChapterModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>{{ __('Copy Course Chapter Classes') }}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" method="post" action="{{ route('course.classes.copy') }}"
                            data-parsley-validate class="form-horizontal form-label-left"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <select class="d-none" name="course_id" class="form-control select2">
                                <option value="{{ $cor->id }}">{{ $cor->title }}</option>
                            </select>

                            <div class="row">
                                <div class="col-md-9">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.ChapterName') }}: <span
                                            class="redstar">*</span>
                                    </label>
                                    <input type="text" placeholder="{{ __('Enter chapter name') }}"
                                        class="form-control" name="chapter_name" id="exampleInputTitle"
                                        value="" required>
                                </div>

                                @if ($cor->installment == 1)
                                    <div class="col-md-3">
                                        <label for="unlock_installment">{{ __('adminstaticword.Installment') }}:<span
                                                class="text-danger">*</span></label>
                                        <select id="unlock_installment" name="unlock_installment"
                                            {{ $cor->installment == 1 ? 'required' : '' }}
                                            class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('Select an option') }}</option>
                                            @foreach ($installments as $i => $c)
                                                <option value="{{ ++$i }}">
                                                    {{ $i . ' (' . $c->amount . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12" id="coursesBox">
                                    <label for="courses">{{ __('Courses') }}:</label><br>
                                    <select id="courses" name="course_ids[]" class="form-control select2"
                                        multiple="multiple" required>
                                        <option value="" disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ $course->id == $cor->id ? 'selected' : '' }}> {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12" id="courseclassesBox">
                                    <label for="courseclasses">{{ __('Course Classes') }}:</label><br>
                                    <select id="courseclasses" name="class_ids[]" class="form-control select2"
                                        multiple="multiple" required>
                                        <option value="" disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($courseclass as $class)
                                            <option value="{{ $class->id }}"> {{ $class->title }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6" id="purchasableBox2">
                                    <label for="purchasable2">{{ __('Price or Not') }}: <span
                                            class="redstar">*</span></label><br>
                                    <select id="is_purchasable2" name="is_purchasable" class="form-control select2"
                                        required>
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}</option>
                                        <option value="1"> {{ __('Price') }} </option>
                                        <option value="0"> {{ __('Without Price') }} </option>
                                    </select>
                                </div>
                                <div id="priceBox2" class="col-md-6" style="display:none">
                                    <label for="exampleInputTit1e">{{ __('Price') }}: <span
                                            class="redstar">*</span>
                                    </label>
                                    <input type="number" min="0" id="price2"
                                        step="0.001"placeholder="{{ __('Enter price') }}" class="form-control"
                                        name="price">
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status" checked />
                                        <span class="knob"></span>
                                    </label>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary-rgba"><i
                                        class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>
                            <div class="clear-both"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sessionModal" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">
                    <b>{{ __('Add Session') }}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="box box-primary">
                <div class="panel panel-sum">
                    <div class="modal-body">
                        <form autocomplete="off" method="post" action="{{ route('coursechapter.store') }}"
                            data-parsley-validate class="form-horizontal form-label-left"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <select class="d-none" name="course_id" class="form-control select2">
                                <option value="{{ $cor->id }}">{{ $cor->title }}</option>
                            </select>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('Select Session Type') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control select2 session_type" name="type">
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        <option value="live-streaming">{{ __('Live Streaming') }}</option>
                                        <option value="in-person-session">{{ __('In-Person Session') }}</option>
                                    </select>
                                </div>

                                @if ($cor->installment == 1)
                                    <div class="col-md-3">
                                        <label for="unlock_installment">{{ __('adminstaticword.Installment') }}:<span
                                                class="text-danger">*</span></label>
                                        <select id="unlock_installment" name="unlock_installment"
                                            {{ $cor->installment == 1 ? 'required' : '' }}
                                            class="form-control select2">
                                            <option value="" selected disabled hidden>
                                                {{ __('Select an option') }}</option>
                                            @foreach ($installments as $i => $c)
                                                <option value="{{ ++$i }}">
                                                    {{ $i . ' (' . $c->amount . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6 d-none" id="meeting_div">
                                    <label>{{ __('Select Live Streaming') }}: <span
                                            class="text-danger">*</span></label>
                                    <select id="selected_meeting" class="form-control select2" name="type_id"
                                        disabled>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($bbl_meetings as $bbl)
                                            <option value="{{ $bbl }}">{{ $bbl->meetingname }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-6 d-none" id="session_div">
                                    <label>{{ __('Select In-Person Session') }}: <span
                                            class="text-danger">*</span></label>
                                    <select id="selected_session" class="form-control select2" name="type_id"
                                        disabled>
                                        <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                        @foreach ($offline_sessions as $session)
                                            <option value="{{ $session }}">{{ $session->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('adminstaticword.ChapterName') }}: <span
                                            class="redstar">*</span> <small class="text-muted"><i
                                                class="fa fa-question-circle"></i> {{ __('readonly') }} </small>
                                    </label>
                                    <input id="chapter_name" type="text" class="form-control" name="chapter_name"
                                        readonly required>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputTit1e">{{ __('Price') }}: <span
                                            class="redstar">*</span> <small class="text-muted"><i
                                                class="fa fa-question-circle"></i> {{ __('readonly') }} </small>
                                    </label>
                                    <input id="price" type="number" min="0" class="form-control"
                                        name="price" readonly required>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="exampleInputDetails">{{ __('adminstaticword.Status') }}:</label><br>
                                    <label class="switch">
                                        <input class="slider" type="checkbox" name="status" checked />
                                        <span class="knob"></span>
                                    </label>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                {{-- <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                    {{ __('Reset') }}</button> --}}
                                <button type="submit" class="btn btn-primary-rgba"><i
                                        class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>
                            <div class="clear-both"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <!--courseclass.js is included -->

    <script>
        function courcechapter(id) {
            var status = $(this).prop('checked') == true ? 1 : 0;

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ url('/course-chapter/status/') }}/" + id,
                data: {
                    'status': status,
                    'id': id
                },
                success: function(data) {
                    var success = new PNotify({
                        title: 'success',
                        text: 'Status Updated Successfully',
                        type: 'success',
                        desktop: {
                            desktop: true,
                            icon: 'feather icon-thumbs-down'
                        }
                    });
                    success.get().click(function() {
                        success.remove();
                    });
                }
            });
        };

        $('#courses').change(function() {

            let course_ids = $(this).val();
            let up = $('#courseclasses').empty();

            if (course_ids) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ url('course/classes') }}",
                    data: {
                        course_ids: course_ids,
                    },
                    success: function(data) {
                        up.select2({
                            placeholder: "{{ __('Select an Option') }}",
                        });
                        $.each(data, function(key, value) {
                            up.append($('<option>', {
                                value: value.id,
                                text: `${value.title}`
                            }));
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest);
                    }
                });
            }
        });
    </script>
@endpush
