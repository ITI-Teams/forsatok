<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.auth-image {
    background: url('{{ asset('images/job-search.jpg') }}') center/cover no-repeat;
    min-height: 500px;
}
</style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="container">
  <div class="card shadow rounded-4 overflow-hidden mx-auto" style="max-width: 900px;">
    <div class="row g-0">


  <!-- Left Image -->
  <div class="col-md-6 d-none d-md-block auth-image"></div>

  <!-- Right Form -->
  <div class="col-md-6 p-4 d-flex flex-column justify-content-center">

    <h2 class="fw-bold mb-2">Register</h2>
    <p class="text-muted mb-4">Create your account now!</p>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('auth.register') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" name="name" placeholder="John Doe" value="{{ old('name') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" placeholder="username@gmail.com" value="{{ old('email') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="password_confirmation" required>
      </div>

      <button type="submit" class="btn w-100 text-white" style="background: linear-gradient(90deg, #6a11cb, #2575fc);">Register</button>
    </form>

    <div class="text-center mt-3">
      <small>Already have an account? <a href="{{ route('login') }}">Login</a></small>
    </div>

  </div>

</div>


  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
