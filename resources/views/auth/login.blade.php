@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div class="auth-page">
        <div class="auth-panel">
            <div class="auth-card">

                <div class="auth-card-header">
                    {{ __('Login') }}
                </div>

                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="auth-field">
                        <label for="email" class="auth-label">
                            {{ __('E-Mail Address') }}:
                        </label>

                        <input id="email" type="email" class="auth-input{{ $errors->has('email') ? ' auth-input-error' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                            <p class="auth-error">
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    <div class="auth-field">
                        <label for="password" class="auth-label">
                            {{ __('Password') }}:
                        </label>

                        <input id="password" type="password" class="auth-input{{ $errors->has('password') ? ' auth-input-error' : '' }}" name="password" required>

                        @if ($errors->has('password'))
                            <p class="auth-error">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>

                    <div class="auth-checkbox-row">
                        <input type="checkbox" class="auth-checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="ml-3 text-sm text-slate-700" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="auth-button">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="auth-link sm:ml-auto" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <p class="auth-secondary-text">
                                Don't have an account?
                                <a class="auth-link" href="{{ route('register') }}">
                                    Register
                                </a>
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
