<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Akademik Sekolah</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .login-header {
            background: #4e54c8;
            background: -webkit-linear-gradient(to right, #8f94fb, #4e54c8);
            background: linear-gradient(to right, #8f94fb, #4e54c8);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .login-header h3 {
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .login-body {
            padding: 40px 30px;
        }

        /* Penyesuaian padding untuk ikon mata di sebelah kanan */
        .form-control {
            border-radius: 10px;
            padding: 12px 45px 12px 45px; 
            border: 1px solid #e1e1e1;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25 row rgba(78, 84, 200, 0.25);
            border-color: #4e54c8;
        }

        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #adb5bd;
        }

        /* Styling Ikon Mata Toggle Password */
        .toggle-password {
            background: transparent;
            border: none;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #adb5bd;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #4e54c8;
        }

        .btn-login {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 84, 200, 0.4);
            filter: brightness(1.1);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
        <h3>Sistem Akademik & Portal Ujian</h3>
        <p>SMA-SMK Mulia Buana</p>
    </div>
    
    <div class="login-body">
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
                <small><i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}</small>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-4 position-relative">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required autofocus>
            </div>
            
            <div class="mb-4 position-relative">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <!-- Tambahkan id="passwordInput" -->
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Masukkan Password" required>
                <!-- Tambahkan Tombol Mata -->
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 mt-2">
                MASUK SEKARANG <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="footer-text">
            &copy; {{ date('Y') }} IT Staff Mulia Buana <br>
            v1.0 - Academic System
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script JavaScript untuk Toggle Password -->
<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('passwordInput');
        var eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>