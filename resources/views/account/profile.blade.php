@extends('layouts.storefront')
@include('partials.ws-ui')
@section('title', 'Profile and address | WEEB STATION')

@section('account')

<h1>Profile &amp; address</h1>
<p class="ui-sub">These details are filled in for you at checkout.</p>

@if ($errors->any())
    <div class="ui-errors">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('account.update') }}">
    @csrf
    @method('PUT')

    <div class="ui-card">
        <h2>Your details</h2>

        <div class="ui-grid">
            <div class="ui-field full">
                <label class="ui-label" for="name">Name</label>
                <input class="ui-input" type="text" name="name" id="name"
                       value="{{ old('name', $customer->name) }}" maxlength="255" required>
            </div>

            <div class="ui-field">
                <label class="ui-label">Phone</label>
                <input class="ui-input" type="text" value="{{ $customer->phone ? '+91 ' . $customer->phone : 'Not added' }}" readonly style="opacity: 0.6;">
            </div>

            <div class="ui-field">
                <label class="ui-label">Email</label>
                <input class="ui-input" type="text" value="{{ $customer->email ?: 'Not added' }}" readonly style="opacity: 0.6;">
            </div>
        </div>
    </div>

    <div class="ui-card">
        <h2>Delivery address</h2>

        <div class="ui-field">
            <label class="ui-label" for="address">Address</label>
            <textarea class="ui-input" name="address" id="address" maxlength="500"
                      placeholder="House number, street, area">{{ old('address', $customer->address) }}</textarea>
        </div>

        <div class="ui-grid">
            <div class="ui-field">
                <label class="ui-label" for="city">City</label>
                <input class="ui-input" type="text" name="city" id="city"
                       value="{{ old('city', $customer->city) }}" maxlength="100">
            </div>

            <div class="ui-field">
                <label class="ui-label" for="pincode">Pincode</label>
                <input class="ui-input" type="text" name="pincode" id="pincode"
                       value="{{ old('pincode', $customer->pincode) }}" inputmode="numeric" maxlength="6">
            </div>
        </div>
    </div>

    <button type="submit" class="ui-btn ui-btn--auto">Save details</button>
</form>

@endsection