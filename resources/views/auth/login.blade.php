<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Akademik - SMA/SMK Mulia Buana</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fe;
            margin: 0;
            height: 100vh;
            overflow-x: hidden;
        }

        /* Container Utama Split Screen */
        .split-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sisi Kiri (Landing Page Info) */
        .landing-side {
            flex: 1.2;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi Lingkaran Latar Belakang */
        .landing-side::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .landing-side::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            z-index: 1;
        }

        .landing-title {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            z-index: 1;
        }

        .landing-desc {
            font-size: 1.1rem;
            opacity: 0.8;
            max-width: 80%;
            margin-bottom: 3rem;
            line-height: 1.6;
            z-index: 1;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            backdrop-filter: blur(5px);
        }

        /* Sisi Kanan (Login Form) */
        .login-side {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-box h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .login-box p.subtitle {
            color: #64748b;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-control {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            padding: 14px 45px 14px 45px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #334155;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 10;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10;
        }

        .toggle-password:hover {
            color: #4f46e5;
        }

        .btn-login {
            background: #4f46e5;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #ef4444;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Responsif untuk Layar Kecil (HP/Tablet) */
        @media (max-width: 991px) {
            .split-container {
                flex-direction: column;
            }
            .landing-side {
                padding: 3rem 2rem;
                text-align: center;
                flex: none;
            }
            .landing-desc {
                max-width: 100%;
                margin: 0 auto 2rem auto;
            }
            .features-grid {
                display: none; /* Sembunyikan fitur di HP agar tidak terlalu panjang */
            }
            .login-side {
                padding: 2rem;
                border-radius: 30px 30px 0 0;
                margin-top: -30px;
                z-index: 2;
            }
        }
    </style>
</head>
<body>

<div class="split-container">
    
    <!-- SISI KIRI: LANDING PAGE -->
    <div class="landing-side">
        <div class="brand-logo">
            <i class="fas fa-graduation-cap me-2 text-warning"></i> SIMUBUN
        </div>
        
        <h1 class="landing-title">Selamat Datang di<br>Sistem Informasi Mutu Belajar Mulia Buana</h1>
        
        <p class="landing-desc">
            Sistem Informasi Akademik dan E-Learning SMA-SMK Mulia Buana. Ujian, jadwal, Nilai,Absensi dan materi pembelajaran dengan mudah dalam satu pintu.
        </p>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-laptop-code text-white"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold">E-Learning Modern</h6>
                    <small class="text-white-50">Materi & Tugas Digital</small>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-chart-line text-white"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold">Pantau Progres</h6>
                    <small class="text-white-50">Rekap Nilai Real-time</small>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-users text-white"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold">Multi Akses</h6>
                    <small class="text-white-50">Siswa, Guru, Admin</small>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt text-white"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold">Sistem Aman</h6>
                    <small class="text-white-50">Proteksi Data Tersentral</small>
                </div>
            </div>
        </div>
    </div>

    <!-- SISI KANAN: LOGIN FORM -->
    <div class="login-side">
        <div class="login-box">
            
            <div class="text-center mb-4 d-lg-none">
                <!-- Logo Muncul Hanya di Layar HP -->
                <i class="fas fa-graduation-cap fa-3x text-primary mb-2"></i>
            </div>

            <h2>Masuk ke Akun</h2>
            <p class="subtitle">Silakan masukkan username dan password Anda.</p>

            @if(session('error'))
                <div class="alert-error mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                
                <div class="mb-4 position-relative">
                    <i class="fas fa-user input-icon-left"></i>
                    <input type="text" name="username" class="form-control" placeholder="ID Pengguna / Username" required autofocus autocomplete="off">
                </div>
                
                <div class="mb-4 position-relative">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Kata Sandi" required>
                    
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input shadow-sm" type="checkbox" id="remember" style="cursor: pointer;">
                        <label class="form-check-label small fw-medium text-muted" for="remember" style="cursor: pointer;">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                    Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-5">
                <p class="small text-muted mb-0">&copy; {{ date('Y') }} IT Staff Mulia Buana</p>
                <p class="small text-muted" style="font-size: 11px;">Versi 2.0 (Integrated E-Learning)</p>
            </div>
        </div>
    </div>

</div>

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