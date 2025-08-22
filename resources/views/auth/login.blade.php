<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color:rgb(255, 255, 255);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #FF82E6;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 30px;
            text-align: center;
        }
        .logo {
            margin-bottom: 50px;
        }
        .logo img {
            height: 50px;
        }
        .input-row {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
        }
        .input-row label {
            width: 90px;
            margin-right: 50px;
            text-align: right;
            color: #555;
            font-weight: 500;
        }
        .input-row input {
            flex: 1;
            padding: 10px;
            border:1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            background-color: #E5E0E0;
            color: #333;
            border: none;
            padding: 12px 20px;
            border-radius: 0;
            cursor: pointer;
            width: 50%;
            font-size: 16px;
            font-weight: 500;
            margin-top: 10px;
        }
        button:hover {
            background-color: #E5E0E0;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo" style="padding-left: 15px;">
            <img src="{{ asset('assets/time_logo.png') }} " alt="Logo"">
        </div>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-row">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="input-row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>

<!---<div style="margin-top: 20px;" class="text-center">
    <a href="{{ route('register') }}" class="text-white">Create an account</a>
</div>--->

          

        </form>
    </div>
</body>
</html>
