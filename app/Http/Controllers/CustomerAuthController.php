<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\CartStore;
use App\Support\WishlistStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class CustomerAuthController extends Controller
{
    private const DEMO = true;

    public function showLogin()
    {
        return view('account.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
        ]);

        $parsed = $this->parseIdentifier($request->input('identifier'));

        if (! $parsed) {
            return back()->withInput()->withErrors([
                'identifier' => 'Enter a valid 10-digit phone number or an email address.',
            ]);
        }

        [$type, $value] = $parsed;

        if (! $this->issueCode($type, $value)) {
            return back()->withInput()->withErrors([
                'identifier' => 'Too many codes requested. Please try again in a few minutes.',
            ]);
        }

        return redirect()->route('account.verify');
    }

    public function showVerify()
    {
        $type = session('otp_type');
        $value = session('otp_identifier');

        if (! $value) {
            return redirect()->route('account.login');
        }

        return view('account.verify', [
            'type' => $type,
            'value' => $value,
            'isNew' => ! Customer::where($type, $value)->exists(),
            'demoCode' => self::DEMO ? session('otp_demo_code') : null,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $type = session('otp_type');
        $value = session('otp_identifier');

        if (! $value) {
            return redirect()->route('account.login');
        }

        $isNew = ! Customer::where($type, $value)->exists();

        $rules = ['code' => 'required|digits:6'];

        if ($isNew) {
            $rules['name'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $key = $this->cacheKey($value);
        $entry = Cache::get($key);

        if (! $entry) {
            return back()->withErrors([
                'code' => 'This code has expired. Please ask for a new one.',
            ]);
        }

        if ($entry['attempts'] >= 5) {
            Cache::forget($key);

            return back()->withErrors([
                'code' => 'Too many wrong attempts. Please ask for a new code.',
            ]);
        }

        if (! hash_equals($entry['hash'], $this->hashCode($request->input('code')))) {
            $entry['attempts']++;
            Cache::put($key, $entry, now()->addMinutes(10));

            return back()->withErrors(['code' => 'That code is not correct.']);
        }

        Cache::forget($key);

        // Existing customer, or create a new one with the name they gave.
        $customer = Customer::where($type, $value)->first()
            ?? Customer::create([
                'name' => $request->input('name'),
                $type => $value,
            ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        CartStore::mergeGuestCart();
        WishlistStore::mergeGuestWishlist();

        session()->forget(['otp_identifier', 'otp_type', 'otp_demo_code']);

        return redirect()
            ->intended(url('/'))
            ->with('success', 'Welcome, '.$customer->name.'! Your cart and wishlist are saved.');
    }

    public function resendOtp()
    {
        $type = session('otp_type');
        $value = session('otp_identifier');

        if (! $value) {
            return redirect()->route('account.login');
        }

        if (! $this->issueCode($type, $value)) {
            return back()->with('error', 'Too many codes requested. Please try again in a few minutes.');
        }

        return back()->with('success', 'A new code has been sent.');
    }

    public function logout(Request $request)
    {
        // Only the customer is logged out. An admin login in the same browser stays.
        Auth::guard('customer')->logout();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }

    private function issueCode(string $type, string $value): bool
    {
        $limiter = 'otp-send:'.$value;

        if (RateLimiter::tooManyAttempts($limiter, 5)) {
            return false;
        }

        RateLimiter::hit($limiter, 600);

        $code = (string) random_int(100000, 999999);

        Cache::put($this->cacheKey($value), [
            'hash' => $this->hashCode($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        session([
            'otp_identifier' => $value,
            'otp_type' => $type,
            'otp_demo_code' => $code,
        ]);

        $this->deliverCode($type, $value, $code);

        return true;
    }

    private function deliverCode(string $type, string $value, string $code): void
    {
        if (self::DEMO) {
            return;
        }
    }

    private function parseIdentifier(string $input): ?array
    {
        $input = trim($input);

        if (str_contains($input, '@')) {
            $email = strtolower($input);

            return filter_var($email, FILTER_VALIDATE_EMAIL) ? ['email', $email] : null;
        }

        $digits = preg_replace('/\D+/', '', $input);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return preg_match('/^[6-9]\d{9}$/', $digits) ? ['phone', $digits] : null;
    }

    private function cacheKey(string $value): string
    {
        return 'customer-otp:'.$value;
    }

    private function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }
}
