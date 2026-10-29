@extends('layouts.storefront')

@include('partials.ws-ui')

@section('title', 'Change delivery address | WEEB STATION')

@section('account')

<h1>Change delivery address</h1>
<p class="ui-sub">
    Order {{ $order->order_number }}. You can change the address until the order is shipped.
    The shop will see that you changed it.
</p>

@if ($errors->any())
    <div class="ui-errors">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('orders.address.update', $order) }}">
    @csrf
    @method('PUT')

    <div class="ui-card">
        <div class="ui-grid">
            <div class="ui-field">
                <label class="ui-label" for="customer_name">Full name</label>
                <input class="ui-input" type="text" name="customer_name" id="customer_name"
                       value="{{ old('customer_name', $order->customer_name) }}" maxlength="255" required>
            </div>

            <div class="ui-field">
                <label class="ui-label" for="customer_phone">Mobile number</label>
                <input class="ui-input" type="text" name="customer_phone" id="customer_phone"
                       value="{{ old('customer_phone', $order->customer_phone) }}"
                       inputmode="numeric" maxlength="10" required>
            </div>

            <div class="ui-field full">
                <label class="ui-label" for="address">Address</label>
                <textarea class="ui-input" name="address" id="address" maxlength="500" required>{{ old('address', $order->address) }}</textarea>
            </div>

            <div class="ui-field">
                <label class="ui-label" for="city">City</label>
                <input class="ui-input" type="text" name="city" id="city"
                       value="{{ old('city', $order->city) }}" maxlength="100" required>
            </div>

            <div class="ui-field">
                <label class="ui-label" for="pincode">Pincode</label>
                <input class="ui-input" type="text" name="pincode" id="pincode"
                       value="{{ old('pincode', $order->pincode) }}" inputmode="numeric" maxlength="6" required>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
        <button type="submit" class="ui-btn ui-btn--auto">Save new address</button>
        <a href="{{ route('orders.show', $order) }}" class="ui-link">Cancel</a>
    </div>
</form>

@endsection