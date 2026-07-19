@extends('layouts.app')
@section('title', 'Password Reset Email')
@section('content')
    <div class="auth-page">
        <div class="auth-panel">

            @if (session('status'))
                <div class="auth-alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="auth-card">

                <div class="auth-card-header">
                    {{ __('Reset Password') }}
                </div>

                <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf

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

                    <div class="auth-actions">
                        <button type="submit" class="auth-button">
                            {{ __('Send Password Reset Link') }}
                        </button>

                        <p class="auth-secondary-text">
                            <a class="auth-link" href="{{ route('login') }}">
                                Back to login
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
