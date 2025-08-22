<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Quotation System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: rgb(255, 255, 255);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            
            background-color: #FF82E6;
            padding: 40px;
            border-radius: 0px;
            width: 400px;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            
        }

        .form-control {
            width: 95%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 0px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .btn-pink {
            
            background-color: #e5e5e5;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 0px;
            cursor: pointer;
            width: 50%;
            margin: 0 auto;
            font-size: 16px;
            font-weight: 500;
            
        }

        .btn-pink:hover {
            background-color: #ccc;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .error {
            color: red;
            font-size: 15px;
        }

        .text-center {
            
            text-align: center;
        }

        .link-white {
            color: white;
            text-decoration: none;
        }

        .link-white:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h3 class="text-center">Create Account</h3>

        @if ($errors->any())
            <div class="error">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="error">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">Name</label>
            <input id="name" type="text" name="name" class="form-control" required value="{{ old('name') }}">

            <label for="username">Username</label>
            <input id="username" type="text" name="username" class="form-control" required value="{{ old('username') }}">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" class="form-control" required value="{{ old('email') }}">

            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="">-- Select Role --</option>
                <!---<option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>--->
                <option value="presale" {{ old('role') == 'presale' ? 'selected' : '' }}>Presale</option>
                <option value="product" {{ old('role') == 'product' ? 'selected' : '' }}>Product</option>
            </select>

            <label for="password">Password</label>

       

<div style="position: relative;">
    <input id="password" type="password" name="password" class="form-control" required>
    <span id="toggle-eye" onclick="togglePassword()" style="position: absolute; right: 10px; top: 35%; transform: translateY(-50%); cursor: pointer;">
         👁️‍🗨
    </span>
</div>

        
          <ul id="password-requirements" style="display: none; font-size: 16px; margin-top: 0px; padding-left: 20px; color: white;">
    <li id="length">❌ At least 12 characters</li>
    <li id="uppercase">❌ Contains uppercase letter</li>
    <li id="number">❌ Contains number</li>
    <li id="symbol">❌ Contains symbol (@$!%*#?&)</li>
</ul>



            <label for="password_confirmation">Confirm Password</label>

            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>

           <div class="text-center mt-3">
    <button type="submit" class="btn btn-pink">Register</button>
</div>


        

             <div style="margin-top: 20px;" class="text-center">
            <a href="{{ route('login') }}" class="text-white">Back to Login</a>
        </div>
    </form>
        </form>



<script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const eyeIcon = document.getElementById('toggle-eye');

        if (pwd.type === 'password') {
            pwd.type = 'text';
            eyeIcon.textContent = '👁️'; // Mata buka
        } else {
            pwd.type = 'password';
            eyeIcon.textContent = '👁️‍🗨'; // Mata tutup
        }
    }
    const passwordInput = document.getElementById('password');
    const requirementList = document.getElementById('password-requirements');

    passwordInput.addEventListener('input', function () {
        const val = passwordInput.value;

        if (val.length > 0) {
            requirementList.style.display = 'block';
        } else {
            requirementList.style.display = 'none';
        }

    
        document.getElementById('length').textContent =
            (val.length >= 12 ? '✅' : '❌') + ' At least 12 characters';
        document.getElementById('uppercase').textContent =
            (/[A-Z]/.test(val) ? '✅' : '❌') + ' Contains uppercase letter';
        document.getElementById('number').textContent =
            (/\d/.test(val) ? '✅' : '❌') + ' Contains number';
        document.getElementById('symbol').textContent =
            (/[!@#$%^&*(),.?":{}|<>]/.test(val) ? '✅' : '❌') + ' Contains symbol (@$!%*#?&)';
    });
</script>


    </div>
</body>
</html>
