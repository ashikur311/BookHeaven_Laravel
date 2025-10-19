<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login & Registration</title>

  {{-- Fonts & Icons (same as old page) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    /* ===== SCOPE EVERYTHING UNDER #auth-page TO AVOID GLOBAL COLLISIONS ===== */
    #auth-page * { box-sizing: border-box; font-family: "Montserrat", sans-serif; }
    html, body { height: 100%; }
    body { margin: 0; }

    #auth-page {
      background-color: #f5f5f5;
      display: flex; align-items: center; justify-content: center;
      min-height: 100vh; position: relative; overflow: hidden;
      isolation: isolate; /* keep z-index safe */
    }

    /* Book-themed background animation */
    #auth-page .book {
      position: absolute; width: 60px; height: 80px;
      background: linear-gradient(45deg,rgb(28,216,249),rgb(55,173,206),rgb(63,205,177));
      border-radius: 5px 10px 10px 5px; box-shadow: 2px 2px 5px rgba(0,0,0,.3);
      transform-origin: left center; animation: float 15s infinite linear;
      opacity: .7; z-index: -1;
    }
    #auth-page .book::before {
      content: ""; position: absolute; inset: 0;
      background: linear-gradient(90deg, rgba(255,255,255,.1) 0%, rgba(255,255,255,0) 20%);
      border-radius: 5px 10px 10px 5px;
    }
    #auth-page .book::after {
      content: ""; position: absolute; top: 5px; right: 5px; width: 15px; height: 70px;
      background: linear-gradient(90deg, #8b4513, #a0522d); border-radius: 0 5px 5px 0;
    }
    #auth-page .book-spine { position: absolute; top: 5px; left: 0; width: 5px; height: 70px;
      background: linear-gradient(90deg, #5d2906, #8b4513); }
    #auth-page .book-title { position: absolute; top: 30px; left: 10px; width: 40px; height: 3px; background: #fff; transform: rotate(90deg); }

    @keyframes float {
      0%   { transform: translateY(0) rotate(0deg); left: -100px; }
      100% { transform: translateY(-100vh) rotate(360deg); left: calc(100vw + 100px); }
    }

    /* Shelf */
    #auth-page .shelf {
      position: absolute; bottom: 0; left: 0; width: 100%; height: 20px;
      background: linear-gradient(to right, #8b4513, #a0522d, #8b4513); z-index: -1;
    }
    #auth-page .shelf::before, #auth-page .shelf::after {
      content: ""; position: absolute; width: 100%; height: 10px;
      background: linear-gradient(to right, #5d2906, #8b4513, #5d2906);
    }
    #auth-page .shelf::before { top: -30px; }
    #auth-page .shelf::after  { top: -60px; }

    /* Container */
    #auth-page .container {
      background: #fff; border-radius: 10px;
      box-shadow: 0 14px 28px rgba(0,0,0,.25), 0 10px 10px rgba(0,0,0,.22);
      position: relative; overflow: hidden; width: 768px; max-width: 100%; min-height: 480px;
      margin: 24px;  /* keep away from edges on small screens */
    }
    #auth-page .container h1 { font-size: 24px; margin-bottom: 10px; color: #333; }
    #auth-page .container p { font-size: 14px; line-height: 20px; letter-spacing: .3px; margin: 15px 0; color: #666; }
    #auth-page .container span { font-size: 12px; color: #888; margin-bottom: 15px; display: block; }
    #auth-page .container a { color: rgb(49,198,196); font-size: 13px; text-decoration: none; margin: 10px 0; transition: color .3s ease; }
    #auth-page .container a:hover { color: #311b92; }

    #auth-page .container button {
      background: #512da8; color: #fff; font-size: 12px; padding: 12px 45px; border: none; border-radius: 20px;
      font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin: 10px 0; cursor: pointer; transition: all .3s ease;
    }
    #auth-page .container button:hover { background: #311b92; transform: translateY(-2px); }
    #auth-page .container button:active { transform: scale(.98); }
    #auth-page .container button.hidden { background: transparent; border: 1px solid #fff; }
    #auth-page .container button.hidden:hover { background: rgba(255,255,255,.1); }

    #auth-page form {
      background: #fff; display: flex; align-items: center; justify-content: center; flex-direction: column;
      padding: 0 40px; height: 100%;
    }
    #auth-page input {
      background: #eee; border: none; margin: 8px 0; padding: 12px 15px; font-size: 13px; border-radius: 8px; width: 100%; outline: none;
      transition: background-color .3s ease;
    }
    #auth-page input:focus { background: #ddd; }

    /* Form panels */
    #auth-page .form-container { position: absolute; top: 0; height: 100%; transition: all .6s ease-in-out; }
    #auth-page .sign-in { left: 0; width: 50%; z-index: 2; }
    #auth-page .container.active .sign-in { transform: translateX(100%); }

    #auth-page .sign-up { left: 0; width: 50%; opacity: 0; z-index: 1; }
    #auth-page .container.active .sign-up {
      transform: translateX(100%); opacity: 1; z-index: 5; animation: move .6s;
    }
    @keyframes move {
      0%,49.99% { opacity: 0; z-index: 1; }
      50%,100%  { opacity: 1; z-index: 5; }
    }

    /* Socials (kept in case you add later) */
    #auth-page .social-icons { margin: 15px 0; }
    #auth-page .social-icons a {
      border: 1px solid #ddd; border-radius: 50%; display: inline-flex; justify-content: center; align-items: center;
      margin: 0 5px; width: 40px; height: 40px; transition: all .3s ease;
    }
    #auth-page .social-icons a:hover { transform: scale(1.1); border-color: #512da8; color: #512da8; }

    /* Toggle section */
    #auth-page .toggle-container {
      position: absolute; top: 0; left: 50%; width: 50%; height: 100%; overflow: hidden;
      transition: all .6s ease-in-out; border-radius: 150px 0 0 100px; z-index: 1000;
    }
    #auth-page .container.active .toggle-container { transform: translateX(-100%); border-radius: 0 150px 100px 0; }

    #auth-page .toggle {
      background: linear-gradient(to right, rgb(21,199,175), rgb(183,135,187));
      height: 100%; position: relative; left: -100%; width: 200%; transform: translateX(0); transition: all .6s ease-in-out;
    }
    #auth-page .container.active .toggle { transform: translateX(50%); }

    #auth-page .toggle-panel {
      position: absolute; width: 50%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;
      padding: 0 30px; text-align: center; top: 0; transform: translateX(0); transition: all .6s ease-in-out; color: #000;
    }
    #auth-page .toggle-left { transform: translateX(-200%); }
    #auth-page .container.active .toggle-left { transform: translateX(0); }
    #auth-page .toggle-right { right: 0; transform: translateX(0); }
    #auth-page .container.active .toggle-right { transform: translateX(200%); }

    /* Password eye */
    #auth-page .password-container { position: relative; width: 100%; }
    #auth-page .toggle-password {
      position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #777; transition: color .3s ease;
    }
    #auth-page .toggle-password:hover { color: #512da8; }

    /* Responsive */
    @media (max-width: 768px) {
      #auth-page .container { width: 100%; min-height: 100vh; border-radius: 0; }
      #auth-page .form-container { width: 100%; }
      #auth-page .container.active .sign-in,
      #auth-page .container.active .sign-up { transform: translateX(0); }
      #auth-page .toggle-container { display: none; }
      #auth-page .sign-in, #auth-page .sign-up { position: relative; width: 100%; }
    }
  </style>
</head>
<body>
<div id="auth-page">
  <div class="shelf"></div>

  {{-- Floating books --}}
  @for ($i = 0; $i < 15; $i++)
    <div class="book" style="top: {{ rand(0,100) }}vh; left: {{ rand(0,100) }}vw; animation-delay: {{ rand(0,10) }}s; animation-duration: {{ rand(10,30) }}s;"></div>
  @endfor

  <div class="container {{ request('tab') === 'register' ? 'active' : '' }}" id="container">
    {{-- SIGN UP --}}
    <div class="form-container sign-up">
      <form method="POST" action="{{ route('register') }}">
        @csrf
        <h1>Create Account</h1>
        <span>Use your email for registration</span>

        <input type="text" name="username" placeholder="Name" value="{{ old('username') }}" required />
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
        <div class="password-container">
          <input type="password" name="password" placeholder="Password" required />
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
        <input type="text" name="address" placeholder="Address" value="{{ old('address') }}" />
        <input type="date" name="date_of_birth" placeholder="Date of Birth" value="{{ old('date_of_birth') }}" />
        <input type="text" name="contact" placeholder="Contact Number" value="{{ old('contact') }}" />

        @if ($errors->any())
          <small style="color:#d33; margin-top:8px;">
            {{ $errors->first() }}
          </small>
        @endif
        @if (session('success')) <small style="color:#2e7d32;">{{ session('success') }}</small> @endif
        @if (session('error'))   <small style="color:#d33;">{{ session('error') }}</small> @endif

        <button class="sign-up-btn" type="submit">Sign Up</button>
      </form>
    </div>

    {{-- SIGN IN --}}
    <div class="form-container sign-in">
      <form method="POST" action="{{ url('/login') }}">
        @csrf
        <h1>Sign In</h1>
        <span>Use your email and password</span>

        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
        <div class="password-container">
          <input type="password" name="password" placeholder="Password" required />
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>

        <a href="{{ route('password.request') }}">Forgot Password?</a>

        @if (session('error')) <small style="color:#d33;">{{ session('error') }}</small> @endif

        <button type="submit">Sign In</button>
      </form>
    </div>

    {{-- TOGGLE PANEL --}}
    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-left">
          <h1>Welcome Back!</h1>
          <p>Enter your personal details to use all of the site's features</p>
          <button class="hidden" id="login" type="button">Log In</button>
        </div>
        <div class="toggle-panel toggle-right">
          <h1>Hello, Friend!</h1>
          <p>Register with your personal details to enjoy our services</p>
          <button class="hidden" id="register" type="button">Sign Up</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Toggle password visibility
  document.querySelectorAll('#auth-page .toggle-password').forEach(function (icon) {
    icon.addEventListener('click', function () {
      const input = this.previousElementSibling;
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      this.classList.toggle('fa-eye-slash');
      this.classList.toggle('fa-eye');
    });
  });

  // Switch panels
  const wrapper = document.getElementById('container');
  const toRegister = document.getElementById('register');
  const toLogin = document.getElementById('login');
  if (toRegister) toRegister.addEventListener('click', () => wrapper.classList.add('active'));
  if (toLogin) toLogin.addEventListener('click', () => wrapper.classList.remove('active'));
</script>
</body>
</html>
