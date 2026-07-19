@extends('layouts.app')
@section('title', 'Email Verification')
@section('content')
    <div class="auth-page">
        <div class="auth-panel">

            @if (session('resent'))
                <div class="auth-alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <div class="auth-card">
                <div class="auth-card-header">
                    {{ __('Verify Your Email Address') }}
                </div>

                <div class="auth-card-body auth-copy">
                    <p>
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                    </p>

                    <p class="mt-6">
                        {{ __('If you did not receive the email') }},
                    </p>

                    <form action="{{ route('verification.resend') }}" method="POST" class="mt-2">
                        @csrf

                        <button type="submit" class="auth-inline-link">
                            {{ __('click here to resend another') }}
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
