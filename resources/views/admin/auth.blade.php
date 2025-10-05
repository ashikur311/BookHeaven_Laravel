<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    :root { --primary-color:#4a6fa5; --secondary-color:#166088; --accent-color:#4fc3f7; --light-color:#f8f9fa; --dark-color:#343a40; --success-color:#28a745; --danger-color:#dc3545; }
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
    body{background:#f5f5f5;display:flex;justify-content:center;align-items:center;min-height:100vh;background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);}
    .container{position:relative;width:900px;max-width:100%;min-height:600px;background:#fff;border-radius:10px;box-shadow:0 14px 28px rgba(0,0,0,.25),0 10px 10px rgba(0,0,0,.22);overflow:hidden;}
    .form-container{position:absolute;top:0;height:100%;transition:all .6s ease-in-out;}
    .sign-in-container{left:0;width:50%;z-index:2;}
    .sign-up-container{left:0;width:50%;opacity:0;z-index:1;}
    .container.right-panel-active .sign-in-container{transform:translateX(100%);}
    .container.right-panel-active .sign-up-container{transform:translateX(100%);opacity:1;z-index:5;animation:show .6s;}
    @keyframes show{0%,49.99%{opacity:0;z-index:1;}50%,100%{opacity:1;z-index:5;}}
    .toggle-container{position:absolute;top:0;left:50%;width:50%;height:100%;overflow:hidden;transition:all .6s ease-in-out;border-radius:0 10px 10px 0;z-index:100;}
    .container.right-panel-active .toggle-container{transform:translateX(-100%);border-radius:10px 0 0 10px;}
    .toggle{background:linear-gradient(to right,var(--primary-color),var(--secondary-color));color:#fff;position:relative;left:-100%;height:100%;width:200%;transform:translateX(0);transition:all .6s ease-in-out;}
    .container.right-panel-active .toggle{transform:translateX(50%);}
    .toggle-panel{position:absolute;width:50%;height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:0 40px;text-align:center;top:0;transform:translateX(0);transition:all .6s ease-in-out;}
    .toggle-left{transform:translateX(-200%);}
    .container.right-panel-active .toggle-left{transform:translateX(0);}
    .toggle-right{right:0;transform:translateX(0);}
    .container.right-panel-active .toggle-right{transform:translateX(200%);}
    form{background:#fff;display:flex;flex-direction:column;padding:0 50px;height:100%;justify-content:center;align-items:center;text-align:center;}
    h1{font-weight:700;margin-bottom:20px;color:var(--dark-color);}
    .social-icons{margin:20px 0;}
    .social-icons a{border:1px solid #ddd;border-radius:50%;display:inline-flex;justify-content:center;align-items:center;margin:0 5px;height:40px;width:40px;color:var(--dark-color);text-decoration:none;transition:all .3s;}
    .social-icons a:hover{background:var(--primary-color);color:#fff;border-color:var(--primary-color);}
    input{background:#eee;border:none;padding:12px 15px;margin:8px 0;width:100%;border-radius:5px;font-size:14px;}
    .password-container{position:relative;width:100%;}
    .password-container input{padding-right:40px;}
    .toggle-password{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#777;}
    button{border-radius:20px;border:1px solid var(--primary-color);background:var(--primary-color);color:#fff;font-size:12px;font-weight:bold;padding:12px 45px;letter-spacing:1px;text-transform:uppercase;margin:20px 0;cursor:pointer;transition:transform 80ms ease-in, background .3s;}
    button:active{transform:scale(.95);}
    button:hover{background:var(--secondary-color);}
    button.hidden{background:transparent;border-color:#fff;}
    button.hidden:hover{background:rgba(255,255,255,.1);}
    p{font-size:14px;line-height:1.5;margin-bottom:20px;color:rgba(255,255,255,.9);}
    span{font-size:12px;color:#777;margin-bottom:15px;display:block;}
    a{color:#333;font-size:14px;text-decoration:none;margin:15px 0;transition:color .3s;}
    a:hover{color:var(--primary-color);}
    .alert{padding:15px;margin-bottom:20px;border-radius:4px;font-size:14px;width:100%;text-align:center;}
    .alert-danger{background:#f8d7da;color:var(--danger-color);border:1px solid #f5c6cb;}
    .alert-success{background:#d4edda;color:var(--success-color);border:1px solid #c3e6cb;}
    .close-btn{position:absolute;top:20px;right:20px;background:transparent;border:none;color:var(--dark-color);font-size:24px;cursor:pointer;z-index:1000;}
    .admin-badge{position:absolute;top:20px;left:20px;background:var(--dark-color);color:#fff;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:bold;z-index:1000;}
    @media (max-width:768px){
      .container{width:100%;height:auto;min-height:100vh;border-radius:0;}
      .sign-in-container,.sign-up-container{width:100%;height:auto;position:relative;}
      .toggle-container{display:none;}
      .container.right-panel-active .sign-in-container,
      .container.right-panel-active .sign-up-container{transform:none;}
    }
  </style>
</head>
<body>
  <div class="container" id="container">
    <div class="admin-badge">ADMIN PORTAL</div>

    {{-- Sign Up --}}
    <div class="form-container sign-up-container">
      <form method="POST" action="{{ route('admin.register') }}">
        @csrf
        <h1>Create Admin Account</h1>

        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @elseif(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger">
            {{ $errors->first() }}
          </div>
        @endif

        <input type="text" name="username" placeholder="Username" required value="{{ old('username') }}">
        <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">

        <div class="password-container">
          <input type="password" name="password" placeholder="Password (min 8 chars)" required>
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>

        <div class="password-container">
          <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>

        <input type="text" name="full_name" placeholder="Full Name (optional)" value="{{ old('full_name') }}">

        <button type="submit" name="admin_register">Register</button>
      </form>
    </div>

    {{-- Sign In --}}
    <div class="form-container sign-in-container">
      <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <h1>Admin Sign In</h1>

        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any() && !session('success'))
          <div class="alert alert-danger">
            {{ $errors->first() }}
          </div>
        @endif

        <input type="text" name="username" placeholder="Username" required value="{{ old('username') }}">
        <div class="password-container">
          <input type="password" name="password" placeholder="Password" required>
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>
        <button type="submit" name="admin_login">Sign In</button>
      </form>
    </div>

    {{-- Toggle Panel --}}
    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-left">
          <h1>Welcome Back!</h1>
          <p>Enter your admin credentials to access the dashboard</p>
          <button class="hidden" id="login" type="button">Sign In</button>
        </div>
        <div class="toggle-panel toggle-right">
          <h1>Hello, Admin!</h1>
          <p>Register a new admin account with secure credentials</p>
          <button class="hidden" id="register" type="button">Register</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');

    if (registerBtn) registerBtn.addEventListener('click', () => {
      container.classList.add("right-panel-active");
    });

    if (loginBtn) loginBtn.addEventListener('click', () => {
      container.classList.remove("right-panel-active");
    });

    // Show/Hide Password
    document.querySelectorAll('.toggle-password').forEach(toggle => {
      toggle.addEventListener('click', function () {
        const passwordField = this.parentNode.querySelector('input');
        if (passwordField.type === 'password') {
          passwordField.type = 'text';
          this.classList.remove('fa-eye-slash');
          this.classList.add('fa-eye');
        } else {
          passwordField.type = 'password';
          this.classList.remove('fa-eye');
          this.classList.add('fa-eye-slash');
        }
      });
    });

    // Auto switch to sign-in if registration success
    @if(session('switch_to_login'))
      container.classList.remove("right-panel-active");
    @else
      // default: keep sign-in; if user clicked "Register" previously you can add logic
    @endif
  </script>
</body>
</html>
