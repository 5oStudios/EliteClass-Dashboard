<?php
$language = Session::get('changed_language'); //or 'english' //set the system language
$rtl = ['ar', 'he', 'ur', 'arc', 'az', 'dv', 'ku', 'fa']; //make a list of rtl languages
?>
@if (in_array($language, $rtl))
    <script>
        var rtl = true;
    </script>
@else
    <script>
        var rtl = false;
    </script>
@endif

<script src="{{ url('admin_assets/assets/js/jquery.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/togglepassword.js') }}"></script>

<!-- Datatable js -->
<script src="{{ url('admin_assets/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/jszip.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/vfs_fonts.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/buttons.print.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/dataTables.dateTime.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/buttons.colVis.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/custom/custom-table-datatable.js') }}"></script>

<!-- Select2 js -->
<script src="{{ url('admin_assets/assets/js/custom/custom-form-select.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/popper.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/bootstrap.min.js') }}"></script>

{{-- <script src="{{ url('admin_assets/assets/plugins/moment/moment.min.js') }}"></script> --}}
<script src="{{ url('admin_assets/assets/plugins/moment/moment.js') }}"></script>

<script src="{{ url('admin_assets/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Tagsinput js -->
<script src="{{ url('admin_assets/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/bootstrap-tagsinput/typeahead.bundle.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/modernizr.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/detect.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/vertical-menu.js') }}"></script>

<!-- Switchery js -->
<script src="{{ url('admin_assets/assets/plugins/switchery/switchery.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/custom/custom-switchery.js') }}"></script>

<!-- Slick js -->
<script src="{{ url('admin_assets/assets/plugins/slick/slick.min.js') }}"></script>
{{-- <script src="{{ url('admin_assets/assets/plugins/datepicker/datepicker.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/datepicker/i18n/datepicker.en.js') }}"></script> --}}

<script src="{{ url('admin_assets/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ url('admin_assets/assets/plugins/bootstrap-datepicker/js/bootstrap-datetimepicker.min.js') }}">
</script>
{{-- <script src="{{ url('admin_assets/assets/js/custom/custom-form-datepicker.js') }}"></script> --}}

<!-- Pnotify js -->
<script src="{{ url('admin_assets/assets/plugins/pnotify/js/pnotify.custom.min.js') }}"></script>

<!-- Select2 js -->
{{-- <script src="{{ url('admin_assets/assets/plugins/select2/select2.min.js') }}"></script> --}}
<script src="{{ url('admin_assets/assets/plugins/select2/select2.full.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/core.js') }}"></script>

<script src="{{ url('admin_assets/js/tinymce.min.js') }}"></script>
<script src="{{ url('admin_assets/js/master.js') }}"></script>
<script src="{{ url('admin_assets/js/togglepassword.js') }}"></script>

<script src="{{ url('admin_assets/js/courseclass.js') }}"></script>
{{-- <script src="{{ url('admin_assets/js/subscription-pricing.js') }}"></script> --}}

<script src="{{ url('admin_assets/assets/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/custom/custom-form-touchspin.js') }}"></script>

<script src="{{ url('admin_assets/assets/plugins/colorpicker/bootstrap-colorpicker.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/custom/custom-form-colorpicker.js') }}"></script>
<script src="{{ url('admin_assets/assets/js/custom/custom-dashboard.js') }}"></script>
<script>
    var baseurl = @json(url('/'));
</script>
{{-- <script src="{{ url('js/updater.js') }}"></script> --}}

<script src={{ url('admin_assets/assets/js/bs-custom-file-input.js') }}></script>
<script src={{ url('admin_assets/assets/js/bs-custom-file-input.min.js') }}></script>

<script src="{{ url('js/custom-toggle.js') }}"></script>
<script src="{{ url('js/custom-file-input.js') }}"></script>
<script src="{{ url('js/custom-js.js') }}"></script>

{{-- <script>
    var youtubekey = @json(env('YOUTUBE_API_KEY'));
</script>
@if ($gsetting->youtube_enable == 1)
    <script src="{{ url('js/youtube.js') }}"></script>
@endif --}}

<script>
    var vimeokey = @json(env('VIMEO_ACCESS'));
</script>

<script>
    var bunnycdnkey = @json(env('BUNNYCDN_API_KEY'));
</script>
<script>
    var bunnycdn_libraryid = @json(env('BUNNYCDN_LIBRARY_ID'));
</script>
<script>
    var bunnycdn_hostname = @json(env('BUNNYCDN_HOSTNAME'));
</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
{{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-5XTDNP6BTL"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-5XTDNP6BTL');
</script> --}}

<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "8f0b3208-bfa4-424d-bf0b-014b09ace4fe",
        });
    });
</script>

{!! midia_js() !!}

<script>
    $(document).ready(function() {
        bsCustomFileInput.init()
    })
</script>

<script src="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.js') }}"></script>
<script>
    @if (session('push-notifications-disabled'))
        swal({
            width: '520px',
            icon: 'error',
            title: 'Kindly review your OneSignal Dashboard Settings',
            text: "{!! session('push-notifications-disabled') !!}"
        });
    @endif
</script>

<script>
    PNotify.desktop.permission();
    @if (session('warning'))
        var warning = new PNotify({
            title: 'Warning',
            text: '{{ session('warning') }}',
            type: 'primary',
            desktop: {
                desktop: true,
                icon: 'feather icon-thumbs-down'
            }
        });
        warning.get().click(function() {
            warning.remove();
        });
    @endif
    @if (session('success'))
        var success = new PNotify({
            title: 'Success',
            text: '{{ session('success') }}',
            type: 'success',
            desktop: {
                desktop: true,
                icon: 'feather icon-thumbs-up'
            }
        });
        success.get().click(function() {
            success.remove();
        });
    @endif
    @if (session('info'))
        var info = new PNotify({
            title: 'Info',
            text: '{{ session('info') }}',
            type: 'info',
            desktop: {
                desktop: true,
                icon: 'feather icon-info'
            }
        });
        info.get().click(function() {
            info.remove();
        });
    @endif
    @if (session('delete'))
        var deleted = new PNotify({
            title: 'Deleted',
            text: '{{ session('delete') }}',
            type: 'error',
            desktop: {
                desktop: true,
                icon: 'feather icon-trash-2'
            }
        });
        deleted.get().click(function() {
            deleted.remove();
        });
    @endif
    $('.select2').select2();

    $(function() {
        $(".datepicker").datepicker({
            language: 'ar',
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",

            zIndexOffset: 10000,
        });
        $('.default-datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,

            startDate: new Date(),
            format: 'yyyy-mm-dd',

            zIndexOffset: 10000,
        });

        $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD hh:mm a',
            minDate: new Date(),
        });
        $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD hh:mm a',
        });
    });
</script>

<script>
    $(".toggle-password").on('click', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>

@if ($gsetting->rightclick == '1')
    <script>
        (function($) {
            "use strict";
            $(function() {
                $(document).on("contextmenu", function(e) {
                    return false;
                });
            });
        })(jQuery);
    </script>
@endif

@if ($gsetting->inspect == '1')
    <script>
        (function($) {
            "use strict";
            document.onkeydown = function(e) {
                if (event.keyCode == 123) {
                    return false;
                }
                if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                    return false;
                }
                if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                    return false;
                }
                if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                    return false;
                }
                if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                    return false;
                }
            }
        })(jQuery);
    </script>
@endif

<script>
    $("#sortable").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            sendOrderToServer();
        }
    });

    function sendOrderToServer() {

        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function(index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('class-sort') }}",
            data: {
                order: order,
                _token: "{{ csrf_token() }}",
            },
            success: function(resp) {
                var success = new PNotify({
                    title: 'success',
                    text: "{{ __('Position Updated Successfully') }}",
                    type: 'success',
                });
                success.get().click(function() {
                    success.remove();
                });
            },
            error: function(resp) {
                var error = new PNotify({
                    title: 'error',
                    text: "{{ __('Oops something went wrong, please try again') }}",
                    type: 'error',
                });
                error.get().click(function() {
                    error.remove();
                });
            }
        });
    }
</script>

<script>
    $("#sortable-chapter").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            sendOrderToServerchapter();
        }
    });

    function sendOrderToServerchapter() {

        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function(index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('chapter-sort') }}",
            data: {
                order: order,
                _token: "{{ csrf_token() }}",
            },
            success: function(resp) {
                var success = new PNotify({
                    title: 'success',
                    text: "{{ __('Position Updated Successfully') }}",
                    type: 'success',
                });
                success.get().click(function() {
                    success.remove();
                });
            },
            error: function(resp) {
                var error = new PNotify({
                    title: 'error',
                    text: "{{ __('Oops something went wrong, please try again') }}",
                    type: 'error',
                });
                error.get().click(function() {
                    error.remove();
                });
            }
        });
    }
</script>

<script>
    $("#checkboxAllcoursechapter").on('click', function() {
        $('input.check').not(this).prop('checked', this.checked);
    });
</script>

<script>
    $(function() {
        $('#checkboxAll').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll4').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input4').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input4').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll5').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input5').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input5').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll6').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input6').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input6').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll7').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input7').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input7').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll8').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input8').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input8').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll9').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input9').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input9').attr('checked', false);
            }
        });

        $('#checkboxAll10').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input10').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input10').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll11').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input11').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input11').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll12').on('change', function() {
            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input12').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input12').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('#checkboxAll100').on('change', function() {

            if ($(this).prop("checked") == true) {
                $('.material-checkbox-input100').attr('checked', true);
            } else if ($(this).prop("checked") == false) {
                $('.material-checkbox-input100').attr('checked', false);
            }
        });
    });
</script>

<script>
    $(function() {
        $('.select2-multi-select').select2({
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });
</script>

<script>
    $('#drip_type1').change(function() {

        if ($(this).val() == 'date') {
            $('#dripdate1').show();
            $("input[name='drip_date']").attr('required', 'required');
        } else {
            $('#dripdate1').hide();
        }

        if ($(this).val() == 'days') {
            $('#dripdays1').show();
            $("input[name='drip_days']").attr('required', 'required');
        } else {
            $('#dripdays1').hide();
        }


    });
</script>

<script>
    (function($) {
        "use strict";
        $(document).ready(function() {
            $('a[data-toggle="pill"]').on('show.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('#v-pills-tab a[href="' + activeTab + '"]').tab('show');
            }
        });
    })(jQuery);
</script>
<!-- End js -->

<!-- To unregister serviceWorker from client browsers -->
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                registration.unregister();
            } 
        });
    }
</script>

@yield('script')
@stack('script')
