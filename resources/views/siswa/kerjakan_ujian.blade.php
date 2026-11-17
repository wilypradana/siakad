<!DOCTYPE html>
@php
    $jumlah_jawaban = \App\Models\JawabanSiswa::where('ujian_id', $ujian->id)
                        ->where('siswa_id', $siswa->id)
                        ->count();
@endphp
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIAKAD CBT - {{ $ujian->judul_ujian }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@700&display=swap');

        /* PROTEKSI ANTI-COPY & SELECT TEXT */
        body, html { 
            margin: 0; padding: 0; height: 100%; 
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;         
            -webkit-touch-callout: none; overflow: hidden; 
        }

        /* MODERN TOP BAR */
        .top-bar { 
            position: fixed; top: 0; left: 0; width: 100%; height: 65px; 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
            color: white; display: flex; align-items: center; justify-content: space-between; 
            padding: 0 16px; z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .gform-container { width: 100%; height: calc(100vh - 65px); border: none; margin-top: 65px; pointer-events: auto; }
        .cbt-container { width: 100%; height: calc(100vh - 65px); margin-top: 65px; overflow-y: auto; padding: 20px 16px; }
        .gate-center { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px; }
        
        .card-modern {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        /* OPSI JAWABAN */
        .opsi-jawaban { 
            cursor: pointer; 
            border: 2px solid #e2e8f0; 
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.2s ease; 
            margin-bottom: 12px;
        }
        .opsi-jawaban:hover { border-color: #cbd5e1; }
        .opsi-jawaban.terpilih { 
            border-color: #2563eb; 
            background-color: #eff6ff; 
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .opsi-huruf { 
            display: inline-flex; justify-content: center; align-items: center;
            min-width: 36px; height: 36px; margin-right: 12px;
            font-weight: 700; border-radius: 10px;
            background: #f1f5f9; color: #64748b;
            transition: all 0.2s ease;
        }
        .opsi-jawaban.terpilih .opsi-huruf {
            background: #2563eb; color: #ffffff;
        }

        /* DIGITAL HUD TIMER */
        .timer-box {
            background: #0f172a;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            position: relative;
            overflow: hidden;
        }
        .timer-box::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #ef4444, #f59e0b);
        }
        .timer-text {
            font-family: 'Space Mono', monospace;
            font-size: 2.6rem;
            font-weight: 700;
            color: #ef4444;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
            letter-spacing: -1px;
            line-height: 1;
        }
        @media (max-width: 576px) {
            .timer-text { font-size: 2.1rem; }
        }
        .timer-text.blink { animation: pulse-danger 1s infinite; }

        @keyframes pulse-danger {
            0% { opacity: 1; }
            50% { opacity: 0.45; }
            100% { opacity: 1; }
        }

        /* GRID NAVIGASI SOAL */
        .btn-nav-soal { 
            width: 40px; height: 40px; margin: 4px; 
            font-weight: 600; border-radius: 10px;
            display: inline-flex; justify-content: center; align-items: center;
            padding: 0;
        }
        .btn-nav-soal.btn-outline-secondary { border: 2px solid #e2e8f0; color: #64748b; }
        .btn-action { border-radius: 12px; font-weight: 600; padding: 10px 20px; }

        .fade-in-up { animation: fadeInUp 0.35s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

    <div class="top-bar">
        <div class="fw-bold d-flex align-items-center text-truncate" style="max-width: 50%;">
            <div class="bg-white bg-opacity-25 rounded-circle d-none d-sm-flex justify-content-center align-items-center me-2 flex-shrink-0" style="width: 34px; height: 34px;">
                <i class="fas fa-laptop-code text-white small"></i>
            </div>
            <span class="text-truncate" style="font-size: 0.95rem;">{{ $ujian->mapel->nama_mapel }}</span>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            @if(session()->has('ujian_verified_' . $ujian->id))
                {{-- INDIKATOR PENGAWASAN REAL-TIME --}}
                <span id="badge-pelanggaran" class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;" title="Status Pengawasan Ujian">
                    <i class="fas fa-shield-alt me-1 text-warning"></i> <span id="teks-pelanggaran">Aman (0/3)</span>
                </span>
            @endif

            <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill fw-bold px-3 d-none d-md-flex align-items-center" id="btn-kembali">Kembali</a>
            
            @if(session()->has('ujian_verified_' . $ujian->id))
                @if($ujian->metode_ujian == 'gform')
                    <form action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri ujian? Pastikan jawaban Google Form sudah di-SUBMIT!')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 shadow-sm" id="btn-selesai">Selesai</button>
                    </form>
                @else
                    <button type="button" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 shadow-sm" id="btn-selesai-cbt" onclick="akhiriCBT()">Akhiri Ujian</button>
                @endif
            @endif
        </div>
    </div>

    @if($siswa->status_bayar == 0)
        <div class="gate-center">
            <div class="card card-modern text-center p-5" style="max-width: 500px;">
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-lock text-danger fa-3x"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-3">Akses Terkunci!</h3>
                <p class="text-muted">Silakan selesaikan administrasi di Tata Usaha untuk mendapatkan kode kartu ujian.</p>
            </div>
        </div>
    @elseif(!session()->has('ujian_verified_' . $ujian->id))
        <div class="gate-center">
            <div class="card card-modern p-4 p-md-5" style="max-width: 440px; width: 100%;">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 75px; height: 75px;">
                        <i class="fas fa-qrcode text-primary fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Verifikasi Kartu</h4>
                    <p class="text-muted small mb-0">Masukkan kode akses yang tertera pada kartu ujian Anda.</p>
                </div>
                @if(session('error')) <div class="alert alert-danger rounded-3 small">{{ session('error') }}</div> @endif
                <form action="{{ route('siswa.ujian.verifikasi', $ujian->id) }}" method="POST">
                    @csrf
                    <div class="form-floating mb-4">
                        <input type="text" name="kode_input" class="form-control fw-bold text-center fs-5 rounded-3" id="kodeInput" placeholder="Masukkan Kode Kartu" required style="letter-spacing: 2px;">
                        <label for="kodeInput">Kode Ujian Anda</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">Verifikasi & Mulai</button>
                </form>
            </div>
        </div>
    @else
        @if($ujian->metode_ujian == 'gform')
            <iframe src="{{ $ujian->link_gform }}?embedded=true" class="gform-container" id="gform-iframe" frameborder="0">Memuat...</iframe>
        @else
            <div class="cbt-container">
                <div class="row g-4">
                    <!-- Kolom Kiri: Teks Soal -->
                    <div class="col-lg-8">
                        <div class="card card-modern h-100 d-flex flex-column">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center px-4 pt-4 pb-2">
                                <h5 class="fw-bold mb-0 text-primary d-flex align-items-center">
                                    <span class="bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fs-6 me-2">Soal</span>
                                    <span class="fs-4" id="label-nomor-soal">1</span>
                                </h5>
                                <span class="badge rounded-pill px-3 py-2 fw-semibold" id="label-status-jawab" style="background-color: #f1f5f9; color: #64748b; font-size: 0.8rem;">Belum Dijawab</span>
                            </div>
                            
                            <div class="card-body px-4 py-3 flex-grow-1" id="area-soal">
                                <div class="text-center py-5 text-muted">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                                    <h6 class="fw-bold">Menyiapkan Soal Ujian...</h6>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-2 d-flex justify-content-between gap-2">
                                <button class="btn btn-light btn-action text-dark border shadow-sm" id="btn-prev" onclick="navigasiSoal('prev')">
                                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                                </button>
                                <button class="btn btn-primary btn-action shadow" id="btn-next" onclick="navigasiSoal('next')">
                                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Timer & Navigasi -->
                    <div class="col-lg-4">
                        <div class="timer-box p-3 p-md-4 mb-4 text-center">
                            <h6 class="text-uppercase mb-2 fw-bold text-white opacity-75 small" style="letter-spacing: 1px;"><i class="fas fa-stopwatch me-2"></i>Sisa Waktu</h6>
                            <div id="timer-ujian" class="timer-text">00:00:00</div>
                        </div>

                        <div class="card card-modern">
                            <div class="card-header bg-transparent border-0 px-4 pt-4 pb-0 text-center">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-0" style="letter-spacing: 1px;">Navigasi Jawaban</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-wrap justify-content-center mb-3" id="kotak-navigasi"></div>
                                <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-2.5 small text-secondary fw-semibold" style="font-size: 0.78rem;">
                                    <div class="d-flex align-items-center"><span class="bg-success rounded-circle me-1.5" style="width: 10px; height:10px;"></span> Terjawab</div>
                                    <div class="d-flex align-items-center"><span class="bg-white border border-secondary rounded-circle me-1.5" style="width: 10px; height:10px;"></span> Kosong</div>
                                    <div class="d-flex align-items-center"><span class="bg-primary rounded-circle me-1.5" style="width: 10px; height:10px;"></span> Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="form-selesai-cbt" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="pelanggaran" id="input-pelanggaran-cbt" value="0">
            </form>
        @endif
    @endif

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script>
    @if(session()->has('ujian_verified_' . $ujian->id))
    
    // ========================================================
    // BAGIAN 1: SISTEM ANTI CHEAT (DESKTOP + HP ANDROID/IPHONE)
    // ========================================================
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    const cheatStorageKey = "cheat_count_ujian_{{ $ujian->id }}_siswa_{{ $siswa->id }}";
    
    @if($jumlah_jawaban == 0)
        localStorage.removeItem(cheatStorageKey);
    @endif

    let cheatCount = parseInt(localStorage.getItem(cheatStorageKey)) || 0;
    const maxCheat = 3; 
    let isWarningOpen = false;
    let lastViolationTime = 0;
    let initialScreenHeight = window.innerHeight;
    let initialScreenWidth = window.innerWidth;

    const alarmSound = new Audio('https://assets.mixkit.co/active_storage/sfx/991/991-preview.mp3');
    alarmSound.loop = true;

    function updateIndikatorPelanggaran() {
        const badge = document.getElementById('badge-pelanggaran');
        const teks = document.getElementById('teks-pelanggaran');
        if (!badge || !teks) return;

        if (cheatCount === 0) {
            badge.className = "badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 rounded-pill px-2.5 py-1.5";
            teks.textContent = `Aman (0/${maxCheat})`;
        } else if (cheatCount === 1) {
            badge.className = "badge bg-warning text-dark rounded-pill px-2.5 py-1.5 fw-bold";
            teks.textContent = `Peringatan (${cheatCount}/${maxCheat})`;
        } else {
            badge.className = "badge bg-danger text-white rounded-pill px-2.5 py-1.5 fw-bold";
            teks.textContent = `Bahaya (${cheatCount}/${maxCheat})`;
        }
    }

    function triggerSelesai(isPelanggaran = false) {
        window.onbeforeunload = null;
        localStorage.removeItem(cheatStorageKey);
        localStorage.removeItem("waktu_selesai_ujian_{{ $ujian->id }}");

        if (isPelanggaran) {
            let inputCbt = document.getElementById('input-pelanggaran-cbt');
            let inputGform = document.getElementById('input-pelanggaran-gform');
            if (inputCbt) inputCbt.value = cheatCount;
            if (inputGform) inputGform.value = cheatCount;
        }

        @if($ujian->metode_ujian == 'gform')
            document.getElementById('form-auto-selesai').submit();
        @else
            document.getElementById('form-selesai-cbt').submit();
        @endif
    }

    function catatPelanggaran(pesan) {
        const now = Date.now();
        // Debounce 1000ms agar satu peristiwa tidak dihitung ganda
        if (isWarningOpen || (now - lastViolationTime < 1000)) return;

        lastViolationTime = now;
        isWarningOpen = true;
        cheatCount++;
        localStorage.setItem(cheatStorageKey, cheatCount);
        updateIndikatorPelanggaran();

        alarmSound.play().catch(e => console.log("Audio tertahan browser"));

        setTimeout(() => {
            if (cheatCount >= maxCheat) {
                alert(`🚨 UJIAN DIKUNCI OTOMATIS!\n\nAnda telah melakukan pelanggaran sebanyak ${cheatCount}x (batas maksimal ${maxCheat}x).\nSistem otomatis menghentikan ujian dan menyimpan jawaban terakhir Anda.`);
                alarmSound.pause();
                triggerSelesai(true);
            } else {
                const sisa = maxCheat - cheatCount;
                alert(`🚨 PERINGATAN ANTI-CURANG (${cheatCount}/${maxCheat})!\n\n${pesan}\n\nSisa toleransi Anda: ${sisa}x lagi sebelum ujian dikunci otomatis!`);
                alarmSound.pause();
                isWarningOpen = false;
                if (!isIOS && !document.fullscreenElement) {
                    openFullscreen();
                }
            }
        }, 120);
    }

    // 1. Page Visibility API (Utama untuk HP & Laptop: Pindah tab, tekan tombol Home, buka Recent Apps)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            catatPelanggaran('Terdeteksi meninggalkan halaman ujian, berpindah tab, atau membuka aplikasi lain!');
        }
    });

    // 2. Window Blur (Khusus Desktop agar tidak salah deteksi saat pull-down notifikasi HP)
    if (!isMobile) {
        window.addEventListener('blur', function() {
            setTimeout(function() {
                if (document.visibilityState === 'visible' && document.activeElement.tagName !== 'IFRAME') {
                    catatPelanggaran('Jendela ujian kehilangan fokus! Dilarang membuka aplikasi lain.');
                }
            }, 200);
        });
    }

    // 3. Deteksi Split-Screen / Floating Window pada HP Android
    if (isMobile) {
        window.addEventListener('resize', function() {
            // Cek jika tinggi/lebar layar menyusut drastis (>35%) bukan karena rotasi layar
            const isRotated = (window.innerWidth > window.innerHeight) !== (initialScreenWidth > initialScreenHeight);
            if (isRotated) {
                initialScreenWidth = window.innerWidth;
                initialScreenHeight = window.innerHeight;
                return;
            }
            if (window.innerHeight < initialScreenHeight * 0.65 || window.innerWidth < initialScreenWidth * 0.75) {
                catatPelanggaran('Terdeteksi mode Split-Screen / Jendela Mengambang di HP! Kembalikan layar ke ukuran penuh.');
            }
        });
    }

    // 4. Fullscreen Exit Detection (Untuk browser yang mendukung Fullscreen API)
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            catatPelanggaran('Anda keluar dari mode Layar Penuh (Fullscreen)!');
        }
    });

    window.onbeforeunload = function(e) { return "Yakin ingin memuat ulang? Jawaban tersimpan, namun pelanggaran tetap tercatat."; };
    document.getElementById('btn-kembali')?.addEventListener('click', function() { window.onbeforeunload = null; });
    document.getElementById('btn-selesai')?.addEventListener('click', function() { window.onbeforeunload = null; });

    document.onkeydown = function(e) {
        if (e.keyCode === 123 || e.keyCode === 116 || (e.ctrlKey && e.keyCode === 82)) return false; 
        if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 80)) return false; 
        if (e.ctrlKey && e.shiftKey && e.keyCode === 73) return false; 
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) return false; 
    };

    function openFullscreen() {
        if (isIOS) return; // Safari iOS tidak mendukung Fullscreen API untuk div/document
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen().catch(() => {}); } 
        else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); } 
        else if (elem.msRequestFullscreen) { elem.msRequestFullscreen(); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateIndikatorPelanggaran();

        const overlay = document.createElement("div");
        overlay.id = "fullscreen-overlay";
        overlay.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.98); z-index:9999; display:flex; flex-direction:column; justify-content:center; align-items:center; padding: 24px; text-align: center; backdrop-filter: blur(10px);";
        
        const lockIcon = document.createElement("div");
        lockIcon.innerHTML = "<i class='fas fa-shield-alt fa-4x text-primary mb-4'></i>";
        
        const btnFullscreen = document.createElement("button");
        btnFullscreen.innerHTML = "MASUK MODE UJIAN AMAN";
        btnFullscreen.className = "btn btn-primary btn-lg rounded-pill shadow-lg fw-bold px-5 py-3 fs-6";
        
        const textHint = document.createElement("p");
        textHint.innerHTML = isMobile 
            ? "Sistem pengawasan aktif. Dilarang berpindah tab, membuka WhatsApp/aplikasi lain, atau menggunakan Split-Screen."
            : "Layar penuh diperlukan. Sistem anti-curang akan aktif selama ujian berlangsung.";
        textHint.className = "text-white opacity-75 mt-4 small";
        textHint.style.maxWidth = "420px";

        overlay.appendChild(lockIcon);
        overlay.appendChild(btnFullscreen);
        overlay.appendChild(textHint);
        document.body.appendChild(overlay);

        btnFullscreen.addEventListener("click", function() {
            openFullscreen();
            initialScreenHeight = window.innerHeight;
            initialScreenWidth = window.innerWidth;
            document.getElementById('fullscreen-overlay').remove(); 
            
            @if($ujian->metode_ujian == 'cbt')
                initCBT();
            @endif
        });
    });
    
    // ==========================================
    // BAGIAN 2: MESIN LOGIKA CBT LOKAL
    // ==========================================
    @if($ujian->metode_ujian == 'cbt')
        let dataSoal = [];
        let indexAktif = 0;
        
        let durasiMenit = {{ $ujian->durasi }}; 
        let ujianId = {{ $ujian->id }};
        let batasTutupSistem = new Date("{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('Y-m-d\TH:i:s') }}").getTime();
        let storageKey = "waktu_selesai_ujian_" + ujianId;

        @if($jumlah_jawaban == 0)
            localStorage.removeItem(storageKey);
        @endif

        function formatKontenOpsi(teks) {
            if (!teks) return '';
            const ektensiGambar = /\.(jpg|jpeg|png|gif|webp)$/i;
            if (ektensiGambar.test(teks.trim())) {
                return `<img src="/uploads/soal/${teks.trim()}" class="img-fluid rounded mt-2 shadow-sm" style="max-height: 140px; object-fit: contain;">`;
            }
            return teks;
        }

        function initCBT() {
            $.ajax({
                url: "{{ route('siswa.ujian.get_soal', $ujian->id) }}",
                type: "GET",
                success: function(response) {
                    dataSoal = response;
                    if(dataSoal.length > 0) {
                        renderNavigasi();
                        tampilkanSoal(0);
                        mulaiTimer();
                    } else {
                        $('#area-soal').html('<div class="alert alert-danger rounded-3">Maaf, guru belum memasukkan bank soal untuk ujian ini.</div>');
                    }
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengambil soal. Periksa koneksi internet Anda.");
                }
            });
        }

        function renderNavigasi() {
            let html = '';
            dataSoal.forEach((soal, index) => {
                let statusClass = soal.jawaban_siswa ? 'btn-success text-white border-success shadow-sm' : 'btn-outline-secondary';
                html += `<button class="btn ${statusClass} btn-nav-soal" id="nav-${index}" onclick="tampilkanSoal(${index})">${index + 1}</button>`;
            });
            $('#kotak-navigasi').html(html);
        }

        function tampilkanSoal(index) {
            indexAktif = index;
            let soal = dataSoal[index];
            
            let areaSoal = $('#area-soal');
            areaSoal.removeClass('fade-in-up');
            void document.getElementById('area-soal').offsetWidth; 
            areaSoal.addClass('fade-in-up');

            $('#label-nomor-soal').text(index + 1);
            
            $('.btn-nav-soal').removeClass('btn-primary border-primary').css('box-shadow', 'none');
            $(`#nav-${index}`).addClass('btn-primary').css('box-shadow', '0 4px 12px rgba(37,99,235,0.3)');

            if (soal.jawaban_siswa) {
                $('#label-status-jawab').removeClass('text-secondary bg-secondary').addClass('text-success').css('background-color', '#dcfce7').html('<i class="fas fa-check-circle me-1"></i> Terjawab: ' + soal.jawaban_siswa);
            } else {
                $('#label-status-jawab').removeClass('text-success').css('background-color', '#f1f5f9').addClass('text-secondary').text('Belum Dijawab');
            }

            let htmlSoal = '';
            if(soal.gambar) {
                htmlSoal += `<div class="text-center mb-4"><img src="/uploads/soal/${soal.gambar}" class="img-fluid rounded-4 shadow-sm border" style="max-height: 280px;"></div>`;
            }
            htmlSoal += `<div class="fs-5 mb-4 text-dark" style="line-height: 1.6;">${soal.pertanyaan}</div>`;

            const opsi = ['A', 'B', 'C', 'D', 'E'];
            opsi.forEach(opt => {
                let teksOpsi = soal['opsi_' + opt.toLowerCase()];
                if(teksOpsi) {
                    let isChecked = (soal.jawaban_siswa === opt) ? 'terpilih' : '';
                    let kontenFormatted = formatKontenOpsi(teksOpsi);
                    
                    htmlSoal += `
                        <div class="opsi-jawaban ${isChecked}" onclick="simpanJawaban(${soal.id}, '${opt}', ${index}, this)">
                            <div class="p-3 d-flex align-items-center">
                                <span class="opsi-huruf">${opt}</span> 
                                <div class="flex-grow-1 text-dark">${kontenFormatted}</div>
                            </div>
                        </div>
                    `;
                }
            });

            areaSoal.html(htmlSoal);
            $('#btn-prev').prop('disabled', index === 0);
            $('#btn-next').prop('disabled', index === dataSoal.length - 1);
        }

        function navigasiSoal(arah) {
            if (arah === 'next' && indexAktif < dataSoal.length - 1) tampilkanSoal(indexAktif + 1);
            if (arah === 'prev' && indexAktif > 0) tampilkanSoal(indexAktif - 1);
        }

        function simpanJawaban(soal_id, jawaban, indexArr, el) {
            $('.opsi-jawaban').removeClass('terpilih');
            $(el).addClass('terpilih');

            $.ajax({
                url: "{{ route('siswa.ujian.simpan_jawaban', $ujian->id) }}",
                type: "POST",
                data: { _token: $('meta[name="csrf-token"]').attr('content'), soal_id: soal_id, jawaban: jawaban },
                success: function(response) {
                    dataSoal[indexArr].jawaban_siswa = jawaban;
                    $(`#nav-${indexArr}`).removeClass('btn-outline-secondary btn-primary').addClass('btn-success text-white border-success shadow-sm');
                    $('#label-status-jawab').removeClass('text-secondary bg-secondary').addClass('text-success').css('background-color', '#dcfce7').html('<i class="fas fa-check-circle me-1"></i> Terjawab: ' + jawaban);
                },
                error: function() {
                    alert("Koneksi terputus! Gagal menyimpan jawaban.");
                    $('.opsi-jawaban').removeClass('terpilih'); 
                }
            });
        }

        function mulaiTimer() {
            let waktuSelesaiTarget = localStorage.getItem(storageKey);
            
            if (!waktuSelesaiTarget) {
                let waktuSekarang = new Date().getTime();
                waktuSelesaiTarget = waktuSekarang + (durasiMenit * 60 * 1000);
                
                if (waktuSelesaiTarget > batasTutupSistem) {
                    waktuSelesaiTarget = batasTutupSistem;
                }
                localStorage.setItem(storageKey, waktuSelesaiTarget);
            }

            let x = setInterval(function() {
                let sekarang = new Date().getTime();
                let jarak = waktuSelesaiTarget - sekarang;

                if (jarak < 0) {
                    clearInterval(x);
                    localStorage.removeItem(storageKey);
                    $('#timer-ujian').text("00:00:00");
                    alert('Waktu Ujian Telah Habis! Sistem akan mengunci dan menyimpan pekerjaan Anda.');
                    triggerSelesai();
                    return;
                }

                let jam = Math.floor((jarak % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let menit = Math.floor((jarak % (1000 * 60 * 60)) / (1000 * 60));
                let detik = Math.floor((jarak % (1000 * 60)) / 1000);

                $('#timer-ujian').text(
                    (jam < 10 ? "0" + jam : jam) + ":" + 
                    (menit < 10 ? "0" + menit : menit) + ":" + 
                    (detik < 10 ? "0" + detik : detik)
                );
                
                if (jarak < 300000) {
                    $('#timer-ujian').addClass('text-danger blink');
                }
            }, 1000);
        }

        function akhiriCBT() {
            let belumDijawab = dataSoal.filter(s => s.jawaban_siswa == null).length;
            let pesan = belumDijawab > 0 
                ? `Masih ada ${belumDijawab} soal yang BELUM DIJAWAB! Yakin ingin mengakhiri ujian sekarang?`
                : `Semua soal telah dijawab. Yakin ingin mengumpulkan dan mengakhiri ujian?`;
            
            if(confirm(pesan)) {
                window.onbeforeunload = null;
                localStorage.removeItem(cheatStorageKey);
                document.getElementById('form-selesai-cbt').submit();
            }
        }
    @endif
    @endif
</script>

@if(session()->has('ujian_verified_' . $ujian->id) && $ujian->metode_ujian == 'gform')
<form id="form-auto-selesai" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="pelanggaran" id="input-pelanggaran-gform" value="0">
</form>
@endif
</body>
</html>