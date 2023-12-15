@extends('theme.master')
@section('title', 'Two Factor Authentication')
@section('content')

    @include('admin.message')
    <!-- Signup start-->
    <section id="signup" class="signup-block-main-block">
        <div class="container">
            <div class="login-signup">
                <div class="row no-gutters">
                    <div class="col-lg-6 col-md-6">
                        <div class="signup-side-block">
                            <img src="{{ url('images/login/login.png') }}" class="img-fluid" alt="">
                            <div class="login-img">
                                <img src="{{ url('/images/login/' . $gsetting->img) }}" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="signup-heading">

                            @if (session()->has('message'))
                                <p class="alert alert-info">
                                    {{ session()->get('message') }}
                                </p>
                            @endif

                            <div class="signup-block">
                                <form method="POST" action="{{ route('verify.store') }}"
                                    onsubmit="document.getElementById('submitButton').disabled = true;">
                                    {{ csrf_field() }}
                                    <h1>Two Factor Verification</h1>
                                    <p class="text-muted">
                                        {{ __("You have received an email which contains two factor login code. If you haven't received it, press") }}
                                        <a href="#" onclick="disableLink(this)"><span
                                                class="h5">{{ __('here') }}</span></a>.
                                    </p>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-lock"></i>
                                            </span>
                                        </div>
                                        <input name="two_factor_code" type="text"
                                            class="form-control{{ $errors->has('two_factor_code') ? ' is-invalid' : '' }}"
                                            value="{{ request()->code ?? '' }}"
                                            required autofocus placeholder="Two Factor Code">
                                        @if ($errors->has('two_factor_code'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('two_factor_code') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-6 text-left">
                                            <button type="submit" id="submitButton" class="btn btn-danger">
                                                {{ __('Verify') }}
                                            </button>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a class="btn btn-danger px-4" href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form id="logoutform" action="{{ route('logout') }}" method="POST">
        @csrf
    </form>
@endsection

@section('custom-script')
    <script>
        $(window).on('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const myParam = urlParams.get('code');
    
            if (myParam && {{ !$errors->has('two_factor_code') }}) { 
                document.getElementById('submitButton').click();
            }
        })
        function disableLink(link) {
            // Simulate a mouse click:
            window.location.href = "{{ route('verify.resend') }}";
            // Prevent the default link behavior
            event.preventDefault();
            // Disable the link
            link.style.cursor = 'not-allowed';
        }
    </script>
@endsection
