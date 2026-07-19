@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <div class="auth-page">
        <div class="auth-panel">
            <div class="auth-card">

                <div class="auth-card-header">
                    {{ __('Register') }}
                </div>

                <form class="auth-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="auth-field">
                        <label for="name" class="auth-label">
                            {{ __('Name') }}:
                        </label>

                        <input id="name" type="text" class="auth-input{{ $errors->has('name') ? ' auth-input-error' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                        @if ($errors->has('name'))
                            <p class="auth-error">
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>

                    <div class="auth-field">
                        <label for="email" class="auth-label">
                            {{ __('E-Mail Address') }}:
                        </label>

                        <input id="email" type="email" class="auth-input{{ $errors->has('email') ? ' auth-input-error' : '' }}" name="email" value="{{ old('email') }}" required>

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

                    <div class="auth-field">
                        <label for="password-confirm" class="auth-label">
                            {{ __('Confirm Password') }}:
                        </label>

                        <input id="password-confirm" type="password" class="auth-input" name="password_confirmation" required>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="auth-button">
                            {{ __('Register') }}
                        </button>

                        <p class="auth-secondary-text">
                            Already have an account?
                            <a class="auth-link" href="{{ route('login') }}">
                                Login
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
