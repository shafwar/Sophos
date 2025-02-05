<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sophos Dashboard</title>

  <!-- Include compiled CSS from Laravel Mix -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <!-- React Navbar -->
  <div id="navbar"></div> <!-- React component will be rendered here -->

  <!-- Main Content -->
  <div class="container mt-5">
    @yield('content')
  </div>

  <!-- Include compiled JS from Laravel Mix -->
  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
