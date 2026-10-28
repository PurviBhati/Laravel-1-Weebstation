@extends('layouts.storefront')

@section('title', 'Log in or sign up | WEEB STATION')

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
        margin: 0 0 24px;
        color: var(--a-muted);
        font-size: 0.95rem;
        line-height: 1.7;
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

    .ws-auth input {
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

    .ws-auth input::placeholder {
        color: rgba(255, 217, 191, 0.4);
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
        margin-top: 20px;
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

    .ws-auth .auth-note {
        margin: 18px 0 0;
        color: var(--a-muted);
        font-size: 0.8rem;
        line-height: 1.6;
        text-align: center;
    }
</style>

<div class="ws-auth">
    <div class="auth-card">

        <span class="auth-eyebrow">WEEB STATION / ACCOUNT</span>

        <h1>Log in or sign up</h1>

        <p class="auth-sub">
            Use your phone number or email. We'll send you a one-time code,
            so there's no password to remember. Your cart and wishlist are saved to your account.
        </p>

        <form method="POST" action="{{ route('account.send-otp') }}">
            @csrf

            <label for="identifier">Phone number or email</label>

            <input
                type="text"
                name="identifier"
                id="identifier"
                value="{{ old('identifier') }}"
                placeholder="98765 43210 or you@example.com"
                autocomplete="username"
                autofocus
                required
            >

            @error('identifier')
                <p class="auth-error">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn">Send code</button>
        </form>

        <p class="auth-note">
            New here? Just enter your details. We'll create your account after you verify the code.
        </p>

    </div>
</div>

@endsection