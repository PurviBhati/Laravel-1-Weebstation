@extends('layouts.storefront')

@section('title', 'Enter your code | WEEB STATION')

@section('content')

<style>
    .ws-auth {
        --a-peach: #ffd9bf;
        --a-pink: #d86e7c;
        --a-wine: #752027;
        --a-toast: #200b05;
        --a-muted: rgba(255, 217, 191, 0.62);
        --a-border: rgba(255, 217, 191, 0.18);

        width: min(460px, calc(100% - 32px));
        margin: 0 auto;
        padding: 56px 0 70px;
        color: var(--a-peach);
    }

    .ws-auth *,
    .ws-auth *::before,
    .ws-auth *::after {
        box-sizing: border-box;
    }

    .ws-auth .auth-card {
        padding: 32px 28px;
        border: 1px solid var(--a-border);
        border-radius: 18px;
        background: rgba(255, 217, 191, 0.05);
    }

    .ws-auth .auth-eyebrow {
        display: block;
        margin-bottom: 12px;
        color: var(--a-pink);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2.5px;
        text-transform: uppercase;
    }

    .ws-auth h1 {
        margin: 0 0 10px;
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.1;
    }

    .ws-auth .auth-sub {
        margin: 0 0 20px;
        color: var(--a-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .ws-auth .auth-sub strong {
        color: var(--a-peach);
    }

    .ws-auth .demo-box {
        margin-bottom: 22px;
        padding: 14px 16px;
        border: 1px dashed rgba(216, 110, 124, 0.7);
        border-radius: 12px;
        background: rgba(216, 110, 124, 0.1);
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .ws-auth .demo-box strong {
        display: block;
        margin-top: 4px;
        font-size: 1.7rem;
        letter-spacing: 0.3em;
    }

    .ws-auth .field {
        margin-bottom: 16px;
    }

    .ws-auth label {
        display: block;
        margin-bottom: 8px;
        color: var(--a-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ws-auth input[type="text"] {
        width: 100%;
        height: 50px;
        padding: 0 14px;
        border: 1px solid rgba(255, 217, 191, 0.3);
        border-radius: 10px;
        background: rgba(32, 11, 5, 0.6);
        color: var(--a-peach);
        font: inherit;
        font-size: 1rem;
    }

    .ws-auth input.code-input {
        font-size: 1.4rem;
        letter-spacing: 0.4em;
        text-align: center;
    }

    .ws-auth input:focus {
        outline: 2px solid var(--a-pink);
        outline-offset: 2px;
    }

    .ws-auth .auth-error {
        margin: 8px 0 0;
        color: #ffb5b5;
        font-size: 0.85rem;
    }

    .ws-auth .btn {
        display: flex;
        width: 100%;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        margin-top: 6px;
        padding: 14px 20px;
        border: 1px solid var(--a-peach);
        border-radius: 10px;
        background: var(--a-peach);
        color: var(--a-toast);
        font: inherit;
        font-size: 0.92rem;
        font-weight: 800;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .ws-auth .btn:hover {
        background: transparent;
        color: var(--a-peach);
    }

    .ws-auth .auth-links {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 18px;
        font-size: 0.85rem;
    }

    .ws-auth .auth-links form {
        margin: 0;
    }

    .ws-auth .link-button,
    .ws-auth .auth-links a {
        padding: 0;
        border: 0;
        background: none;
        color: var(--a-muted);
        font: inherit;
        text-decoration: underline;
        cursor: pointer;
    }

    .ws-auth .link-button:hover,
    .ws-auth .auth-links a:hover {
        color: var(--a-pink);
    }
</style>

<div class="ws-auth">
    <div class="auth-card">

        <span class="auth-eyebrow">WEEB STATION / VERIFY</span>

        <h1>Enter your code</h1>

        <p class="auth-sub">
            We sent a 6-digit code to
            <strong>{{ $type === 'phone' ? '+91 ' . $value : $value }}</strong>.
            It works for 10 minutes.
        </p>

        @if ($demoCode)
            <div class="demo-box">
                Demo mode: no message is sent. Use this code:
                <strong>{{ $demoCode }}</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('account.verify.submit') }}">
            @csrf

            @if ($isNew)
                <div class="field">
                    <label for="name">Your name</label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="What should we call you?"
                        maxlength="255"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="field">
                <label for="code">6-digit code</label>

                <input
                    type="text"
                    name="code"
                    id="code"
                    class="code-input"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="------"
                    autofocus
                    required
                >

                @error('code')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">
                {{ $isNew ? 'Create my account' : 'Log in' }}
            </button>
        </form>

        <div class="auth-links">
            <form method="POST" action="{{ route('account.resend') }}">
                @csrf
                <button type="submit" class="link-button">Send a new code</button>
            </form>

            <a href="{{ route('account.login') }}">Use a different number or email</a>
        </div>

    </div>
</div>

@endsection