@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
    <div class="auth-page">
        <div class="auth-panel">
            <div class="auth-card">

                <div class="auth-card-header">
                    {{ __('Reset Password') }}
                </div>

                <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

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

                    <div class="auth-field">
                        <label for="password-confirm" class="auth-label">
                            {{ __('Confirm Password') }}:
                        </label>

                        <input id="password-confirm" type="password" class="auth-input" name="password_confirmation" required>
                    </div>

                    <div class="auth-actions">
                        <button type="submit" class="auth-button">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
