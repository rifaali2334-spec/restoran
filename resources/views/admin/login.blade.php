<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Healthy Food</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #faf7f5;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }
        
        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            position: relative;
        }
        
        .left-section {
            flex: 1;
            background: #d4776b;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            height: 100vh;
            clip-path: ellipse(80% 100% at 0% 50%);
        }
        
        .food-image {
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: url('{{ asset('img/hero.png') }}') center/cover;
            box-shadow: none;
            z-index: 15;
            position: absolute;
            border: none;
            left: 35%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #faf7f5;
            padding: 0 50px;
            height: 100vh;
            justify-content: center;
            align-items: flex-end;
            position: relative;
            z-index: 10;
        }
        
        .login-form {
            max-width: 350px;
            margin-right: 170px;
        }
        
        h2 {
            font-size: 2.8rem;
            color: #333;
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: -1px;
            white-space: nowrap;
        }
        
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .subtitle a {
            color: #d4776b;
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
            color: #333;
        }
        
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #d4776b;
            box-shadow: 0 0 0 3px rgba(212, 119, 107, 0.1);
        }
        
        .login-btn {
            width: 100%;
            background: #d4776b;
            color: white;
            padding: 16px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        
        .login-btn:hover {
            background: #c96a5e;
        }
        
        .forgot-link {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .forgot-link a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
        }
        
        .google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: border-color 0.3s;
            font-size: 14px;
        }
        
        .google-login:hover {
            border-color: #d4776b;
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEwIDguMTgxODJWMTJIMTUuNDU0NUMxNS4yNzI3IDEzLjEzNjQgMTQuNTQ1NSAxNC4wOTA5IDEzLjQ1NDUgMTQuNzI3M0wxNi4zNjM2IDE2LjkwOTFDMTguMjcyNyAxNS4yNzI3IDE5LjM2MzYgMTIuODE4MiAxOS4zNjM2IDEwQzE5LjM2MzYgOS4yNzI3MyAxOS4yNzI3IDguNTQ1NDUgMTkuMDkwOSA3LjgxODE4SDEwVjguMTgxODJaIiBmaWxsPSIjNDI4NUY0Ii8+CjxwYXRoIGQ9Ik00LjU0NTQ1IDExLjgxODJDNC41NDU0NSAxMS4wOTA5IDQuNzI3MjcgMTAuMzYzNiA1IDkuNzI3MjdMMS45MDkwOSA3LjI3MjczQzEuMjcyNzMgOC41NDU0NSAwLjkwOTA5MSAxMC4xODE4IDAuOTA5MDkxIDEyQzAuOTA5MDkxIDEzLjgxODIgMS4yNzI3MyAxNS40NTQ1IDEuOTA5MDkgMTYuNzI3M0w1IDEzLjgxODJDNC43MjcyNyAxMy4xODE4IDQuNTQ1NDUgMTIuNTQ1NSA0LjU0NTQ1IDExLjgxODJaIiBmaWxsPSIjRkJCQzA0Ii8+CjxwYXRoIGQ9Ik0xMCAxNi4zNjM2QzEyLjcyNzMgMTYuMzYzNiAxNS4wOTA5IDE1LjQ1NDUgMTYuMzYzNiAxMy44MTgyTDE2LjM2MzYgMTMuODE4MkwxMy40NTQ1IDE0LjcyNzNDMTIuNTQ1NSAxNS4yNzI3IDExLjM2MzYgMTUuNjM2NCAxMCAxNS42MzY0QzcuNTQ1NDUgMTUuNjM2NCA1LjQ1NDU1IDE0LjA5MDkgNC43MjcyNyAxMS44MTgyTDEuOTA5MDkgMTYuNzI3M0MzLjE4MTgyIDE5LjI3MjcgNi4zNjM2NCAyMSAxMCAyMUMxMy4wOTA5IDIxIDE1LjYzNjQgMTkuOTA5MSAxNi4zNjM2IDE2LjM2MzZaIiBmaWxsPSIjMzRBODUzIi8+CjxwYXRoIGQ9Ik0xMCA0LjM2MzY0QzExLjU0NTUgNC4zNjM2NCAxMi45MDkxIDQuOTA5MDkgMTMuOTU0NSA1Ljg2MzY0TDE2LjU0NTUgMy4yNzI3M0MxNC44MTgyIDEuNzI3MjcgMTIuNTQ1NSAwLjkwOTA5MSAxMCAwLjkwOTA5MUM2LjM2MzY0IDAuOTA5MDkxIDMuMTgxODIgMi42MzYzNiAxLjkwOTA5IDUuMjcyNzNMNC43MjcyNyA4LjE4MTgyQzUuNDU0NTUgNS45MDkwOSA3LjU0NTQ1IDQuMzYzNjQgMTAgNC4zNjM2NFoiIGZpbGw9IiNFQTQzMzUiLz4KPC9zdmc+') center/contain;
        }
        
        .alert {
            padding: 12px 16px;
            margin-bottom: 25px;
            border-radius: 6px;
            background: #ffeaea;
            color: #d63031;
            border: 1px solid #fab1a0;
            font-size: 14px;
        }
        
        .bottom-spacer {
            height: 15px;
        }
        
        @media (max-width: 768px) {
            body {
                display: block;
                overflow: hidden;
            }
            
            .container {
                flex-direction: column;
                height: 100vh;
                max-height: 100vh;
                position: relative;
                overflow: hidden;
            }
            
            .left-section {
                display: none;
            }
            
            .food-image {
                position: absolute;
                width: 300px;
                height: 300px;
                right: -80px;
                top: -80px;
                left: auto;
                transform: none;
                margin: 0;
                display: block;
                z-index: 10;
            }
            
            .right-section {
                flex: 1;
                padding: 20px;
                padding-top: 80px;
                align-items: center;
                justify-content: center;
                height: 100vh;
                position: relative;
                z-index: 1;
                overflow: hidden;
            }
            
            .login-form {
                margin-right: 0;
                width: 100%;
                max-width: 400px;
                margin-top: 0;
            }
            
            h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .food-image {
                width: 240px;
                height: 240px;
                right: -70px;
                top: -60px;
            }
            
            .right-section {
                padding: 15px;
                padding-top: 60px;
            }
            
            .login-form {
                max-width: 100%;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            input[type="email"], input[type="password"] {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .login-btn {
                padding: 14px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section"></div>
        <div class="food-image"></div>
        
        <div class="right-section">
            <div class="login-form">
                <h2>Admin Login</h2>
                <p class="subtitle">Please enter your credentials</p>
                
                @if(session('error'))
                    <div class="alert">{{ session('error') }}</div>
                @endif
                
                <form action="{{ route('admin.login.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="login-btn">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>