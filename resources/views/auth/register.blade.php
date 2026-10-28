@extends('layouts.app')

@section('title', 'Register')
@section('content')
    <div class="header">
        <h1>Register</h1>
    </div>

    <form method="POST" action="/register">
        @csrf

        <x-input name="name" label="Name"/>
        <x-input name="email" label="Email" type="email"/>
        <x-input name="password" label="Password (min 8 characters)" type="password"/>
        <x-input name="password_confirmation" label="Confirm Password" type="password"/>

        <button type="summit" class="btn">Register</button>
        <a href="/login" style="margin-left:!2px">I already have an account</a>
    </form>
@endsection