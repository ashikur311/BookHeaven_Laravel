{{-- resources/views/community/members.blade.php --}}
@include('partials.header')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Community Members</title>
  <style>
    :root{--primary-color:#57abd2;--primary-dark:#3d8eb4;--secondary-color:#f8f5fc;--accent-color:rgb(223,219,227);--text-color:#333;--text-light:#666;--light-purple:#e6d9f2;--dark-text:#212529;--light-text:#f8f9fa;--card-bg:#ffffff;--aside-bg:#f0f2f5;--nav-hover:#e0e0e0;--success-color:#28a745;--warning-color:#ffc107;--danger-color:#dc3545;--border-color:#e0e0e0;--hover-bg:#f5f5f5;--even-row-bg:#f9f9f9;--header-bg:#f0f0f0;--header-text:#333;--card-shadow:0 2px 15px rgba(0,0,0,0.1);--transition:all .3s ease}
    .dark-mode{--primary-color:#57abd2;--primary-dark:#4a9bc1;--secondary-color:#2d3748;--accent-color:#4a5568;--text-color:#f8f9fa;--text-light:#a0aec0;--light-purple:#4a5568;--dark-text:#f8f9fa;--light-text:#212529;--card-bg:#1a202c;--aside-bg:#1a202c;--nav-hover:#4a5568;--border-color:#4a5568;--hover-bg:#2d3748;--even-row-bg:#2d3748;--header-bg:#1a202c;--header-text:#f8f9fa;--card-shadow:0 2px 15px rgba(0,0,0,.3)}
    body{background:var(--secondary-color)}
    main{min-height:50vh;padding:20px}
  </style>
</head>
<body>
  <main>
    {{-- Put your members UI here if you later need it --}}
  </main>
  @include('partials.footer')
</body>
</html>
