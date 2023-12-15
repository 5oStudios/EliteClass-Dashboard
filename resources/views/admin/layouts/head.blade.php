<!-- Fevicon -->
<link rel="shortcut icon" href="">
<!-- Start css -->

<link rel="icon" type="image/icon" href="{{ asset('images/favicon/' . $gsetting->favicon) }}"> <!-- favicon-icon -->

<?php
$language = Session::get('changed_language'); //or 'english' //set the system language
$rtl = ['ar', 'he', 'ur', 'arc', 'az', 'dv', 'ku', 'fa']; //make a list of rtl languages
?>

@if (in_array($language, $rtl))
    <link href="{{ url('admin_assets_rtl/assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet">
    <!-- Select2 css -->
    <link href="{{ url('admin_assets_rtl/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Slick css -->
    <link href="{{ url('admin_assets_rtl/assets/plugins/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets_rtl/assets/plugins/slick/slick-theme.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets_rtl/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Fontawesome 4 css -->
    <link rel="stylesheet" href="{{ url('admin_assets/assets/icons/font-awesome/css/font-awesome.min.css') }}">
    <link href="{{ url('admin_assets_rtl/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}"
        rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets_rtl/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput-typeahead.css') }}"
        rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets_rtl/assets/plugins/pnotify/css/pnotify.custom.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ url('admin_assets_rtl/assets/css/flag-icon.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets_rtl/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('admin_assets_rtl/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Responsive Datatable css -->
    <link href="{{ url('admin_assets_rtl/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('admin_assets_rtl/assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <!-- jQuery ui css -->
    <link href="{{ url('admin_assets_rtl/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet"
        type="text/css">

    <link rel="stylesheet" href="{{ url('admin_assets_rtl/assets/css/theme.css') }}" type="text/css">
@else
    <link href="{{ url('admin_assets/assets/icons/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets/assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet">
    <!-- Select2 css -->
    <link href="{{ url('admin_assets/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Slick css -->
    <link href="{{ url('admin_assets/assets/plugins/apexcharts/apexcharts.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets/assets/plugins/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets/assets/plugins/slick/slick-theme.css') }}" rel="stylesheet">
    <link href="{{ url('admin_assets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Fontawesome 4 css -->
    <link rel="stylesheet" href="{{ url('admin_assets/assets/icons/font-awesome/css/font-awesome.min.css') }}">
    <link href="{{ url('admin_assets/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ url('admin_assets/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput-typeahead.css') }}"
        rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets/assets/plugins/pnotify/css/pnotify.custom.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ url('admin_assets/assets/css/flag-icon.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets/assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('admin_assets/assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Responsive Datatable css -->

    <link href="{{ url('admin_assets/assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('admin_assets/assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <link rel="stylesheet" href="{{ url('admin_assets/css/imagestyle.css') }}" type="text/css"> --}}
    @if ($gsetting->sidebar_enable == 1 || $gsetting->instructor_sidebar == 1)
        <link rel="stylesheet" href="{{ url('admin_assets/css/theme_sidebar.css') }}" type="text/css">
    @else
        <link rel="stylesheet" href="{{ url('admin_assets/css/theme.css') }}" type="text/css">
    @endif
@endif

<link href="{{ url('admin_assets/assets/plugins/sweet-alert2/sweetalert2.css') }}" rel="stylesheet" type="text/css">
<style>
    .swal2-modal .swal2-title {
        font-size: 20px !important;
    }
</style>

{{-- <link href="{{ url('admin_assets/assets/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css"> --}}
<link href="{{ url('admin_assets/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}"
    rel="stylesheet" type="text/css">
<link href="{{ url('admin_assets/assets/plugins/bootstrap-datepicker/css/bootstrap-datetimepicker.min.css') }}"
    rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="{{ url('admin_assets/css/bootstrap-iconpicker.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/custom-style.css') }}" />
<link href="{{ url('admin_assets/assets/plugins/colorpicker/bootstrap-colorpicker.css') }}" rel="stylesheet"
    type="text/css">
{!! midia_css() !!}

@yield('stylesheet')
