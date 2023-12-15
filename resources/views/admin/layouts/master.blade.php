<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $gsetting->meta_data_desc }}">
    <meta name="keywords" content="{{ $gsetting->meta_data_keyword }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    @if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif

    <title>@yield('title')</title>

    @include('admin.layouts.head')

    <style>
        .datepicker {
            box-shadow: none;
            border: 1px solid #ced4da;
        }

        #loading {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background-color: #fff;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    @yield('css')

</head>


<body class="vertical-layout">

    <div id="containerbar">
        @if (Auth::user()->hasRole('admin'))
            @if ($gsetting->sidebar_enable == 1)
                @include('admin.layouts.new_sidebar')
            @else
                @include('admin.layouts.sidebar')
            @endif
        @elseif (Auth::user()->hasRole('instructor'))
            @if ($gsetting->instructor_sidebar == 1)
                @include('admin.layouts.instructor_sidebar')
            @else
                @include('instructor.layouts.sidebar')
            @endif
        @elseif (Auth::user()->role != 'user')
            @include('layouts.sidebar')
        @endif

        <div class="rightbar">
            @include('admin.layouts.topbar')

            @yield('maincontent')

            <!-- Start Footerbar -->
            <div class="footerbar">
                <footer class="footer">
                    {{ $gsetting->project_title }}
                    <p class="mb-0">© {{ $gsetting->cpy_txt }} {{ get_release() }}</p>
                </footer>
            </div>
            <!-- End Footerbar -->
        </div>
    </div>

    @include('admin.layouts.scripts')

    <script>
        $(window).on('load', function() {
            $('#loading').hide();
        })
    </script>

    @yield('scripts')

</body>

</html>
