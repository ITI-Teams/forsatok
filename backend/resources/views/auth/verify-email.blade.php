<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .auth-image {
            background: url('{{ asset('images/job-search.jpg') }}') center/cover no-repeat;
            min-height: 500px;
        }

        .input-code {
            width: 50px;
            text-align: center;
            font-size: 1.5rem;
        }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="container">
        <div class="card shadow rounded-4 overflow-hidden mx-auto" style="max-width: 900px;">
            <div class="row g-0">

                ```
                <div class="col-md-6 d-none d-md-block auth-image"></div>

                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold mb-2">Verify Email</h2>
                    <p class="text-muted mb-4">Enter the 4-digit code sent to your email.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verify-email') }}" class="d-flex justify-content-between"
                        style="max-width:250px;">
                        @csrf
                        <input type="text" name="code_1" maxlength="1" class="form-control input-code" required>
                        <input type="text" name="code_2" maxlength="1" class="form-control input-code" required>
                        <input type="text" name="code_3" maxlength="1" class="form-control input-code" required>
                        <input type="text" name="code_4" maxlength="1" class="form-control input-code" required>
                        <button type="submit" class="btn btn-primary ms-2">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.input-code');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
