@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="reset-wrapper">
    <div class="container-reset">
        <div class="card-reset">
            <h2>Reset Your Password</h2>

            @if(session('success'))
                <div class="message success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="message error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('password.reset') }}">
                @csrf
                <label for="password"><i class="fas fa-lock"></i> New Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required minlength="6">

                <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password" required minlength="6">

                <button type="submit"><i class="fas fa-sync-alt button-icon"></i> Reset Password</button>
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
.container-reset {
    width: 400px;
    max-width: 90%;
}
.card-reset {
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
input[type="password"] {
    padding: 12px 15px; border: 1px solid #ccc; border-radius: 5px;
    font-size: 16px; margin-bottom: 25px; transition: border-color 0.3s;
}
input[type="password"]:focus { border-color: #007bff; outline: none; }
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
