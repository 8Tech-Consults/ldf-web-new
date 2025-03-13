<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>
    <style>
        /* GLOBAL RESETS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: #f3f3f3;
        }

        /* LAYOUT */
        .login-container {
            display: flex;
            flex-wrap: nowrap;
            width: 100%;
            height: 100vh;
            /* fill viewport */
        }

        /* IMAGE COLUMN */
        .img-col {
            flex: 1;
            /* Fill remaining space */
            background: url("https://cdn.pixabay.com/photo/2020/09/01/17/19/goat-5535783_640.jpg") center center no-repeat;
            background-size: cover;
            display: none;
            /* hidden by default (for mobile) */
        }

        @media (min-width: 768px) {
            .img-col {
                display: block;
                /* show image on md+ screens */
            }
        }

        /* FORM COLUMN */
        .form-col {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            padding: 20px;
        }

        /* LOGIN FORM WRAPPER */
        .login-form {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-form h3 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.6rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* ERROR MESSAGE */
        .invalid-feedback {
            color: #d00;
            font-size: 0.9rem;
            margin-top: 0.3rem;
            display: block;
        }

        /* REMEMBER CHECKBOX */
        .form-check {
            margin-bottom: 1rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }

        .form-check-label {
            font-weight: 500;
        }

        /* BUTTON */
        .btn-submit {
            width: 100%;
            padding: 0.6rem;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #0275d8;
            /* A "Bootstrap-like" blue */
            color: #fff;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #025aa5;
        }

        /* FORGOT PASSWORD LINK */
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 0.75rem;
            color: #0275d8;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* SUCCESS / STATUS ALERT */
        .alert-success {
            background-color: #dff0d8;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            color: #3c763d;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- IMAGE COLUMN (hidden on small screens) -->
        <div class="img-col"></div>

        <!-- FORM COLUMN -->
        <div class="form-col">
            <div class="login-form">
                <h3>{{ __('Login') }}</h3>

                <!-- Display session status if it exists (e.g., after password reset) -->
                @if (session('status'))
                    <div class="alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form method="POST" action="{{ admin_url('auth/login') }}">
                    @csrf

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label for="email">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ 'mubs0x@gmail.com' }}"required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            value="{{ '4321' }}" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- REMEMBER ME -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div>
                        <button type="submit" class="btn-submit">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <!-- FORGOT PASSWORD -->
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

</body>

</html>
