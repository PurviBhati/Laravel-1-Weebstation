@extends('layouts.storefront')

@section('title', 'Checkout | WEEB STATION')

@section('content')

@include('partials.ws-ui')

<div class="ui-page">

    <h1>Checkout</h1>
    <p class="ui-sub">Confirm where we should deliver your order, then choose how you'd like to pay.</p>

    @if ($errors->any())
        <div class="ui-errors">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf

        <div class="ui-layout">

            {{-- Left: details and payment --}}
            <div>

                <div class="ui-card">
                    <h2>Delivery details</h2>

                    <div class="ui-grid">
                        <div class="ui-field full">
                            <label class="ui-label" for="customer_name">Full name</label>
                            <input class="ui-input" type="text" name="customer_name" id="customer_name"
                                   value="{{ old('customer_name', $customer->name) }}" maxlength="255" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label" for="customer_phone">Mobile number</label>
                            <input class="ui-input" type="text" name="customer_phone" id="customer_phone"
                                   value="{{ old('customer_phone', $customer->phone) }}"
                                   inputmode="numeric" maxlength="10" placeholder="10-digit number" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label" for="customer_email">Email (optional)</label>
                            <input class="ui-input" type="email" name="customer_email" id="customer_email"
                                   value="{{ old('customer_email', $customer->email) }}" maxlength="255">
                        </div>

                        <div class="ui-field full">
                            <label class="ui-label" for="address">Address</label>
                            <textarea class="ui-input" name="address" id="address" maxlength="500"
                                      placeholder="House number, street, area" required>{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label" for="city">City</label>
                            <input class="ui-input" type="text" name="city" id="city"
                                   value="{{ old('city', $customer->city) }}" maxlength="100" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label" for="pincode">Pincode</label>
                            <input class="ui-input" type="text" name="pincode" id="pincode"
                                   value="{{ old('pincode', $customer->pincode) }}"
                                   inputmode="numeric" maxlength="6" required>
                        </div>

                        <div class="ui-field full">
                            <label class="ui-label" for="notes">Note for the shop (optional)</label>
                            <textarea class="ui-input" name="notes" id="notes" maxlength="500"
                                      placeholder="Anything we should know?">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="ui-card">
                    <h2>Payment</h2>

                    <label class="ui-choice">
                        <input type="radio" name="payment_method" value="cod"
                               {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                        <div>
                            <strong>Cash on delivery</strong>
                            <span>Pay when your order arrives.</span>
                        </div>
                    </label>

                    <label class="ui-choice">
                        <input type="radio" name="payment_method" value="online_demo"
                               {{ old('payment_method') === 'online_demo' ? 'checked' : '' }}>
                        <div>
                            <strong>Pay online (demo)</strong>
                            <span>Practice payment page. No real money is taken.</span>
                        </div>
                    </label>

                    @error('payment_method')
                        <p class="ui-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Right: summary --}}
            <aside class="ui-card ui-sticky">
                <h2>Your order</h2>

                @foreach ($lines as $line)
                    <div class="ui-line">
                        <div class="ui-thumb">
                            @if ($line['image'])
                                <img src="{{ asset('storage/' . $line['image']) }}" alt="">
                            @else
                                WS.
                            @endif
                        </div>

                        <div>
                            <div class="ui-line-name">{{ $line['product']->name }}</div>
                            <div class="ui-line-meta">
                                {{ $line['size'] ? 'Size ' . $line['size'] . ' · ' : '' }}Qty {{ $line['quantity'] }}
                            </div>
                        </div>

                        <strong>₹{{ number_format($line['total'], 2) }}</strong>
                    </div>
                @endforeach

                <div class="ui-row" style="margin-top: 10px;">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($totals['subtotal'], 2) }}</span>
                </div>

                <div class="ui-row">
                    <span>Shipping</span>
                    <span>
                        @if ($totals['shipping'] > 0)
                            ₹{{ number_format($totals['shipping'], 2) }}
                        @else
                            Free
                        @endif
                    </span>
                </div>

                <div class="ui-row">
                    <span>GST ({{ rtrim(rtrim(number_format($totals['gst_rate'], 2), '0'), '.') }}%)</span>
                    <span>₹{{ number_format($totals['gst'], 2) }}</span>
                </div>

                <div class="ui-row total">
                    <span>Total</span>
                    <span>₹{{ number_format($totals['total'], 2) }}</span>
                </div>

                @if ($totals['to_free'] > 0)
                    <p class="ui-sub" style="margin: 10px 0 0; font-size: 0.8rem;">
                        Add ₹{{ $totals['to_free'] }} more to get free shipping.
                    </p>
                @endif

                <button type="submit" class="ui-btn" style="margin-top: 18px;">Place order</button>

                <p class="ui-sub" style="margin: 14px 0 0; font-size: 0.8rem; text-align: center;">
                    <a class="ui-link" href="{{ route('user.cart.index') }}">&larr; Back to cart</a>
                </p>
            </aside>

        </div>
    </form>

</div>

@endsection