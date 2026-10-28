@extends('layouts.app')

@section('title', 'Login')
@section('content')
    <div class="card" style="max-width:420px; margin: 0; auto">
        <div class="header">
            <h1>Login</h1>
        </div>
        <form method="POST" action="/login">
            @csrf

            <x-input name="email" label="Email" type="email"/>
            <x-input name="password" label="Password" type="password"/>

            <button type="submit" class="btn">Login here</button>
            <a href="/register" style="margin-left:12px">Create an account</a>
        </form>
    </div>

@endsection