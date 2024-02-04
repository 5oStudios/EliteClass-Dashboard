@extends('admin.layouts.master')
@section('title', __('Create manual enrollment'))

@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Enrollment') }}
        @endslot

        @slot('menu1')
            {{ __('Enrollment') }}
        @endslot

        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ request()->has('test_user') ? route('testuser.enrollments', $testUser->id) : route('order.enrollments') }}"
                        class="float-right btn btn-primary-rgba mr-2"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>
                </div>
            </div>
        @endslot
    @endcomponent

    <style>
        .ml-4,
        .mx-4 {
            margin-left: 1.2rem !important;
        }

        #submitButton:disabled {
            /* disable hover effect */
            /* pointer-events: none; */

            opacity: 0.5;

            background-color: #506fe4;
            border: none;
            color: #ffffff;
        }

        #submitButton:disabled:hover {
            cursor: not-allowed;
        }
    </style>

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-tittle">{{ __('adminstaticword.UserEnrollment') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manual.enrollment.store') }}" method="POST" autocomplete="off">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ __('adminstaticword.Students') }}:<sup class="redstar">*</sup></label>
                                    @if (request()->has('test_user'))
                                        <input type="text" class="form-control"
                                            value="{{ $testUser->fname }} {{ $testUser->lname }} : {{ $testUser->mobile }}"
                                            disabled>
                                        <input type="hidden" name="user_id" value="{{ $testUser->id }}">
                                    @else
                                        <select name="user_id" id="student_name" class="form-control select2" required>
                                            <option value="" selected disabled hidden>
                                                {{ __('adminstaticword.SelectanOption') }}</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->fname }}
                                                    {{ $user->lname }} : {{ $user->mobile }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="filter_by_type" class="mr-1">{{ __('Order Type') }}:<sup
                                            class="redstar">*</sup></label>
                                    <select name="type" id="type" class="form-control select2" required>
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                        <option value="course">{{ __('Course') }} </option>
                                        <option value="package">{{ __('Package') }} </option>
                                        <option value="live-streaming">{{ __('Live Streaming') }} </option>
                                        <option value="in-person-session">{{ __('In-Person Session') }} </option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div id="order_items_div" class="col-md-6 d-none">
                                    <label for="item_label"></label><sup class="redstar">*</sup>
                                    <select name="type_id" id="order_items" class="form-control select2" required>
                                        <option value="" selected disabled hidden>
                                            {{ __('adminstaticword.SelectanOption') }}
                                        </option>
                                    </select>
                                </div>
                                <div id="course_chapters_div" class="col-md-6 d-none">
                                    <label>{{ __('Chapters') }}: <sup class="redstar">*</sup><small class="text-muted">
                                            {{ __('By default, the user will be enrolled in a course unless you select a chapter.') }}</small></label>
                                    <select name="chapter_id" id="course_chapters" class="form-control select2">
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div id="price_div" class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="full_payment"
                                            value="full" checked>
                                        <label class="form-check-label"
                                            for="exampleRadios1">{{ __('Full Payment') }}</label> <small id="price"
                                            class="text-muted"></small>
                                    </div>
                                    <div id="installments_div" class="d-none">
                                        <hr>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_type"
                                                id="installments" value="installments">
                                            <label class="form-check-label"
                                                for="exampleRadios2">{{ __('Pay in installments') }}</label> <small
                                                class="text-muted" id="totalInstallments"></small>
                                        </div>

                                        <div id="installment_list" class="form-group form-check ml-4 d-none">
                                            <div class="installment1 d-none">

                                                <input type="checkbox" name="installment1" id="installment1Check"
                                                    class="form-check-input">
                                                <label class="form-check-label">{{ __('Installment 1') }}</label> <small
                                                    id="installment1" class="text-muted"></small><br>
                                            </div>
                                            <div class="installment2 d-none">
                                                <input type="checkbox" name="installment2" id="installment2Check"
                                                    class="form-check-input">
                                                <label class="form-check-label">{{ __('Installment 2') }}</label> <small
                                                    id="installment2" class="text-muted"></small><br>
                                            </div>
                                            <div class="installment3 d-none">
                                                <input type="checkbox" name="installment3" id="installment3Check"
                                                    class="form-check-input" disabled>
                                                <label class="form-check-label">{{ __('Installment 3') }}</label> <small
                                                    id="installment3" class="text-muted"></small>
                                            </div>
                                            <div class="installment4 d-none">
                                                <input type="checkbox" name="installment4" id="installment4Check"
                                                    class="form-check-input" disabled>
                                                <label class="form-check-label">{{ __('Installment 4') }}</label> <small
                                                    id="installment4" class="text-muted"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <button id="submitButton" class="btn btn-primary-rgba"
                                    onClick="this.form.submit(); this.disabled=true;"><i class="fa fa-check-circle"></i>
                                    {{ __('Create') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        'use strict';
        var chapters = '',
            order_item = '',
            course = '',
            installments = '',
            count = 0;

        const urlParams = new URLSearchParams(window.location.search);
        const myParam = urlParams.get('test_user');

        var currency = @json($currency);

        $('#student_name').change(function() {

            let type = $('#type').val();
            let userId = $(this).val();

            getOrderItems(userId, type);
        });

        $('#type').change(function() {

            let type = $(this).val();
            let userId = $('#student_name').val();

            if (myParam != '' && myParam != null) {
                userId = myParam;
            }

            getOrderItems(userId, type);
        });

        $('#order_items').change(function() {
            $("#full_payment").prop("checked", true);
            $("#installment_list").addClass('d-none');

            if ($('#type').val() === 'course') {
                $(this).attr('name', 'course_id');

                let type = $('#type').val();
                let course_id = $(this).val();

                if (course_id === '') {
                    $('#course_chapters_div').addClass('d-none');
                } else {
                    $('#course_chapters').empty();
                    $('#course_chapters_div').removeClass('d-none');
                }

                $('#price').removeClass('d-none');

                $.each(order_item, function(key, row) {
                    if (row.id == course_id) {
                        let price = row.price
                        if(row.discount_type === 'fixed'){
                            price = row.price - row.discount_price
                        }else if (row.discount_type === 'percentage'){
                            price = row.price*((100-row.discount_price)/100)
                        } else if(row.discount_price){
                            price = row.discount_price
                        }
                        $('#price').text(price + ' ' + 'KWD');course = row; // Assign course here to call its installment on chapter change

                        if (row.installment == '1') {
                            // Call async function to get installment price
                            getInstallment(type, course_id);

                        } else {
                            $('#installments_div').addClass('d-none');
                        }
                    }
                });


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('course.chapters') }}",
                    data: {
                        course_id: course_id,
                    },
                    success: function(data) {
                        chapters = data;
                        $('#course_chapters').append("<option value=''>{{ __('All') }}</option>");
                        $.each(data, function(key, row) {
                            $('#course_chapters').append($('<option>', {
                                value: row.id,
                                text: row.chapter_name
                            }));
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log('ERROR: ', XMLHttpRequest);
                    }
                });

            } else if ($('#type').val() === 'package') {
                $(this).attr('name', 'bundle_id');

                let type = $('#type').val();
                let bundle_id = $(this).val();

                $('#course_chapters_div').addClass('d-none');
                $('#price').removeClass('d-none');

                $.each(order_item, function(key, row) {
                    if (row.id == bundle_id) {
                        let price = row.price
                        if(row.discount_type === 'fixed'){
                            price = row.price - row.discount_price
                        }else if (row.discount_type === 'percentage'){
                            price = row.price*((100-row.discount_price)/100)
                        } else if(row.discount_price){
                            price = row.discount_price
                        }
                        $('#price').text(price + ' ' + 'KWD');

                        if (row.installment === '1') {
                            // Call async function to get installment price
                            getInstallment(type, bundle_id);

                        } else {
                            $('#installments_div').addClass('d-none');
                        }
                    }
                });

            } else if ($('#type').val() === 'live-streaming') {
                $(this).attr('name', 'meeting_id');
                $('#course_chapters_div').addClass('d-none');
                $('#installments_div').addClass('d-none');
                $('#price').removeClass('d-none');

                let meeting_id = $(this).val();

                $.each(order_item, function(key, row) {
                    if (row.id == meeting_id) {
                        let price = row.price
                        if(row.discount_type === 'fixed'){
                            price = row.price - row.discount_price
                        }else if (row.discount_type === 'percentage'){
                            price = row.price*((100-row.discount_price)/100)
                            console.log('percent');
                        } else if(row.discount_price){
                            price = row.discount_price
                        }
                        console.log(row.price,row.discount_price,row.discount_type,price);
                        $('#price').text(price + ' ' + 'KWD');
                    }
                });

            } else if ($('#type').val() === 'in-person-session') {
                $(this).attr('name', 'offline_session_id');
                $('#course_chapters_div').addClass('d-none');
                $('#installments_div').addClass('d-none');
                $('#price').removeClass('d-none');

                let session_id = $(this).val();

                $.each(order_item, function(key, row) {
                    if (row.id == session_id) {
                        $('#price').text(row.discount_price + ' ' + 'KWD');
                    }
                });
            }
        });

        $('#course_chapters').change(function() {
            $('#price').removeClass('d-none');
            if ($(this).val() != '') {
                $('#installments_div').addClass('d-none');
                $("#full_payment").prop("checked", true);

                let chapter_id = $('#course_chapters').val();

                $('#price').removeClass('d-none');

                $.each(chapters, function(key, row) {
                    if (row.id == chapter_id) {
                        $('#price').text(row.discount_price + ' ' + 'KWD');
                    }
                });

            } else {
                $('#installment_list').addClass('d-none');
                $('#price').text(course.discount_price + ' ' + 'KWD');
                if (course.installment == '1') {
                    // Call async function to get installment price
                    getInstallment('course', course.id);

                } else {
                    $('#installments_div').addClass('d-none');
                }
            }
        });

        $('#full_payment').click(function() {
            if ($(this).is(':checked')) {
                $('#installment_list').addClass('d-none');
                $("#installment1Check").prop("checked", false);
                $("#installment2Check").prop("checked", false);
                $("#installment3Check").prop("checked", false);
            }
        });

        $('#installments').click(function() {
            $('#installment_list').removeClass('d-none');
            $('#installment1Check').prop("checked", true);
            $('#installment1Check').click(function() {
                return false;
            });

            $("#installment2Check").prop('disabled', false);
            $("#installment3Check").prop('disabled', true);
            $("#installment4Check").prop('disabled', true);
            $("#installment2Check").prop("checked", false);
            $("#installment3Check").prop("checked", false);

            $(`#installment${count}Check`).prop('disabled', true);

            // installments.map(function(data, key) {

            //     if (data.expire === true) {
            //         $(`#installment${key+1}Check`).prop("checked", true);
            //         $(`#installment${key+1}Check`).click(function() {
            //             return false;
            //         });
            //     }
            // });

        });

        $('#installment2Check').click(function() {
            if ($(this).prop('checked')) {
                $('#installment3Check').prop('disabled', false);
            } else {
                $('#installment3Check').prop('disabled', true);
            }

            $('#installment3Check').prop('checked', false);
            $(`#installment${count}Check`).prop('disabled', true);
        })

        function getOrderItems(userId, type) {
            if (type === '' || type === null || userId === '' || userId === null) {
                $('#order_items_div').addClass('d-none');
            }

            $('#course_chapters_div').addClass('d-none');
            $('#installments_div').addClass('d-none');
            $('#price').addClass('d-none');

            $('#order_items').empty();

            if (type != '' && type != null && userId != '' && userId != null) {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('order.items') }}",
                    data: {
                        user_id: userId,
                        type: type,
                    },
                    success: function(data) {
                        $('#order_items_div').removeClass('d-none');
                        var $label = $('label[for="item_label"]');
                        // get the text of the label
                        $label.text(type.split("-").map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(" ") + "s:");
                        order_item = data;
                        $('#order_items').append(
                            "<option value='' selected disabled hidden>{{ __('adminstaticword.SelectanOption') }}</option>"
                        );
                        $.each(data, function(key, row) {
                            $('#order_items').append($('<option>', {
                                value: row.id,
                                text: type == 'live-streaming' ? row.meetingname : row
                                    .title,
                            }));
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log('ERROR: ', XMLHttpRequest);
                    }
                });
            }
        }

        async function getInstallment(type, type_id) {
            const options = {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: `{"type": "${type}","type_id": "${type_id}"}`
            }
            const baseURL = window.location.host;

            try {
                let response = await fetch(`${window.location.protocol}//${baseURL}/manual/installments`, options);
                installments = await response.json();

                count = installments.length;

                for (let i = 1; i <= 4; i++) {
                    $(`.installment${i}`).addClass('d-none');
                }

                installments.map(function(data, key) {

                    $('#installments_div').removeClass('d-none');
                    $('#totalInstallments').text(
                        `${installments.length} Installments`);
                    $(`.installment${key+1}`).removeClass('d-none');
                    $(`#installment${key+1}`).text(
                        `${data.amount} ${currency.code}.  Due date: ${data.due_date}`);
                    $(`#installment${installments.length}Check`).prop('disabled', true);
                });

                return installments;

            } catch (error) {
                console.error(error);
                return null;
            }
            return null;
        }
    </script>
@endsection
