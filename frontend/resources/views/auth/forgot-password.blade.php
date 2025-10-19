@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="forgot-wrapper">
    <div class="container-forgot">
        <div class="card-forgot">
            <h2>Forgot Password</h2>

            @if(session('success'))
                <div class="message success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="message error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email"><i class="fas fa-envelope"></i> Enter your email:</label>
                <input type="email" name="email" id="email" placeholder="you@example.com" required>
                <button type="submit" name="submit_forgot">
                    <i class="fas fa-paper-plane button-icon"></i> Send OTP
                </button>
            </form>
        </div>
    </div>
</div>

<style>
body {
    background: #f0f2f5;
    font-family: 'Roboto', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}
.container-forgot {
    width: 400px;
    max-width: 90%;
}
.card-forgot {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    padding: 40px 30px;
    text-align: center;
}
h2 { margin-bottom: 20px; color: #333; }
.message { margin-bottom: 20px; padding: 15px; border-radius: 5px; font-size: 14px; }
.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
form { display: flex; flex-direction: column; align-items: stretch; }
label { text-align: left; margin-bottom: 8px; font-weight: 500; color: #555; }
input[type="email"] {
    padding: 12px 15px; border: 1px solid #ccc; border-radius: 5px;
    font-size: 16px; margin-bottom: 25px; transition: border-color 0.3s;
}
input[type="email"]:focus { border-color: #007bff; outline: none; }
button {
    padding: 12px; background: #007bff; color: #fff;
    font-size: 16px; border: none; border-radius: 5px;
    cursor: pointer; transition: background 0.3s;
    display: flex; align-items: center; justify-content: center;
}
button:hover { background: #0056b3; }
.button-icon { margin-right: 8px; }
</style>
@endsection
