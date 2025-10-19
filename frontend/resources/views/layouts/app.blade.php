<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Book Heaven')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
<link rel="stylesheet" href="{{ asset('css/app.css') }}">


</head>
<body>
  {{-- Global header --}}
  @include('partials.header')

  <main>
    @yield('content')
  </main>

  {{-- Global footer --}}
  @include('partials.footer')

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @yield('scripts')
</body>
</html>
