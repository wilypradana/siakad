<!DOCTYPE html>
@php
    // Deteksi apakah ini ujian baru/hasil reset dengan menghitung jumlah jawaban tersimpan
    $jumlah_jawaban = \App\Models\JawabanSiswa::where('ujian_id',$ujian->id)
                        ->where('siswa_id', $siswa->id)
                        ->count();
@endphp
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background-color: #f1f5f9; /* Slate 100 */
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;         
            -webkit-touch-callout: none; overflow: hidden; 
        }

        /* MODERN TOP BAR */
        .top-bar { 
            position: fixed; top: 0; left: 0; width: 100%; height: 65px; 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
            color: white; display: flex; align-items: center; justify-content: space-between; 
            padding: 0 24px; z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .gform-container { width: 100%; height: calc(100vh - 65px); border: none; margin-top: 65px; pointer-events: auto; }
        .cbt-container { width: 100%; height: calc(100vh - 65px); margin-top: 65px; overflow-y: auto; padding: 24px; }
        .gate-center { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        
        /* MODERN CARDS */
        .card-modern {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            transition: transform 0.3s ease;
        }

        /* OPSI JAWABAN FUTURISTIK */
        .opsi-jawaban { 
            cursor: pointer; 
            border: 2px solid #e2e8f0; 
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            margin-bottom: 12px;
        }
        .opsi-jawaban:hover { 
            border-color: #cbd5e1; 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        }
        .opsi-jawaban.terpilih { 
            border-color: #3b82f6; 
            background-color: #eff6ff; 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .opsi-huruf { 
            display: inline-flex; justify-content: center; align-items: center;
            width: 36px; height: 36px; 
            font-weight: 700; border-radius: 10px;
            background: #f1f5f9; color: #64748b;
            transition: all 0.25s ease;
        }
        .opsi-jawaban.terpilih .opsi-huruf {
            background: #3b82f6; color: #ffffff;
        }

        /* DIGITAL HUD TIMER */
        .timer-box {
            background: #0f172a;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.2);
            position: relative;
            overflow: hidden;
        }
        .timer-box::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #ef4444, #f59e0b);
        }
        .timer-text {
            font-family: 'Space Mono', monospace;
            font-size: 3.2rem;
            font-weight: 700;
            color: #ef4444;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
            letter-spacing: -1px;
            line-height: 1;
        }
        .timer-text.blink { animation: pulse-danger 1s infinite; }

        @keyframes pulse-danger {
            0% { opacity: 1; text-shadow: 0 0 20px rgba(239, 68, 68, 0.6); }
            50% { opacity: 0.5; text-shadow: none; }
            100% { opacity: 1; text-shadow: 0 0 20px rgba(239, 68, 68, 0.6); }
        }

        /* GRID NAVIGASI SOAL */
        .btn-nav-soal { 
            width: 42px; height: 42px; margin: 4px; 
            font-weight: 600; border-radius: 12px;
            transition: all 0.2s;
            display: inline-flex; justify-content: center; align-items: center;
            padding: 0;
        }
        .btn-nav-soal.btn-outline-secondary { border: 2px solid #e2e8f0; color: #64748b; }
        .btn-nav-soal.btn-outline-secondary:hover { border-color: #cbd5e1; background: #f8fafc; color: #0f172a; }
        
        .btn-action { border-radius: 12px; font-weight: 600; padding: 10px 24px; transition: all 0.2s; }

        /* ANIMASI MUNCUL SOAL */
        .fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

   <div class="top-bar">
    <div class="fw-bold fs-5 d-flex align-items-center">
        <div class="bg-white bg-opacity-25 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 36px; height: 36px;">
            <i class="fas fa-laptop-code text-white"></i>
        </div>
        {{ $ujian->mapel->nama_mapel }}
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill fw-bold px-3 d-flex align-items-center" id="btn-kembali">Kembali</a>
        
        @if(session()->has('ujian_verified_' . $ujian->id))
            @if($ujian->metode_ujian == 'gform')
                <form action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri ujian? Pastikan jawaban Google Form sudah di-SUBMIT!')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill fw-bold px-4 shadow-sm" id="btn-selesai">Akhiri Ujian</button>
                </form>
            @else
                <button type="button" class="btn btn-sm btn-danger rounded-pill fw-bold px-4 shadow-sm" id="btn-selesai-cbt" onclick="akhiriCBT()">Akhiri Ujian</button>
            @endif
        @endif
    </div>
</div>

    @if($siswa->status_bayar == 0)
        <div class="gate-center">
            <div class="card card-modern text-center p-5" style="max-width: 500px;">
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-lock text-danger fa-3x"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-3">Akses Terkunci!</h3>
                <p class="text-muted">Silakan selesaikan administrasi di Tata Usaha untuk mendapatkan kode kartu ujian.</p>
            </div>
        </div>
    @elseif(!session()->has('ujian_verified_' . $ujian->id))
        <div class="gate-center">
            <div class="card card-modern p-5" style="max-width: 450px; width: 100%;">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-qrcode text-primary fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Verifikasi Kartu</h4>
                </div>
                @if(session('error')) <div class="alert alert-danger rounded-3">{{ session('error') }}</div> @endif
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
        
        <!-- ============================================== -->
        <!-- LOGIKA PEMISAH TAMPILAN GFORM VS CBT           -->
        <!-- ============================================== -->
        @if($ujian->metode_ujian == 'gform')
            <!-- TAMPILAN GOOGLE FORM -->
            <iframe src="{{ $ujian->link_gform }}?embedded=true" class="gform-container" id="gform-iframe" frameborder="0">Memuat...</iframe>
        @else
            <!-- TAMPILAN CBT LOKAL -->
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
                                <span class="badge rounded-pill px-3 py-2 fs-6 fw-semibold" id="label-status-jawab" style="background-color: #f1f5f9; color: #64748b;">Belum Dijawab</span>
                            </div>
                            
                            <div class="card-body px-4 py-3 flex-grow-1" id="area-soal">
                                <div class="text-center py-5 text-muted">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                                    <h5 class="fw-bold">Menyiapkan Database Ujian...</h5>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-2 d-flex justify-content-between">
                                <button class="btn btn-light btn-action text-dark border shadow-sm" id="btn-prev" onclick="navigasiSoal('prev')">
                                    <i class="fas fa-chevron-left me-2"></i> Sebelumnya
                                </button>
                                <button class="btn btn-primary btn-action shadow" id="btn-next" onclick="navigasiSoal('next')">
                                    Selanjutnya <i class="fas fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Timer & Navigasi -->
                    <div class="col-lg-4">
                        <!-- Kotak Timer HUD -->
                        <div class="timer-box p-4 mb-4 text-center">
                            <h6 class="text-uppercase mb-2 fw-bold text-white opacity-75" style="letter-spacing: 1px;"><i class="fas fa-stopwatch me-2"></i>Sisa Waktu</h6>
                            <div id="timer-ujian" class="timer-text">00:00:00</div>
                        </div>

                        <!-- Kotak Navigasi Modern -->
                        <div class="card card-modern">
                            <div class="card-header bg-transparent border-0 px-4 pt-4 pb-0 text-center">
                                <h6 class="fw-bold text-secondary text-uppercase mb-0" style="letter-spacing: 1px;">Navigasi Jawaban</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap justify-content-center mb-4" id="kotak-navigasi">
                                    <!-- Tombol kotak digenerate JS -->
                                </div>
                                <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-3 small text-secondary fw-semibold">
                                    <div class="d-flex align-items-center"><span class="bg-success rounded-circle me-2" style="width: 12px; height:12px;"></span> Terjawab</div>
                                    <div class="d-flex align-items-center"><span class="bg-white border border-secondary rounded-circle me-2" style="width: 12px; height:12px;"></span> Kosong</div>
                                    <div class="d-flex align-items-center"><span class="bg-primary rounded-circle me-2" style="width: 12px; height:12px; box-shadow: 0 0 8px rgba(13,110,253,0.5);"></span> Aktif</div>
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
    
    // ==========================================
    // BAGIAN 1: SISTEM ANTI CHEAT
    // ==========================================
    let cheatCount = 0;
    const maxCheat = 2; 
    let isWarningOpen = false;

    const alarmSound = new Audio('https://assets.mixkit.co/active_storage/sfx/991/991-preview.mp3');
    alarmSound.loop = true;

    function triggerSelesai(isPelanggaran = false) {
        window.onbeforeunload = null;
        if(isPelanggaran) {
            let inputCbt = document.getElementById('input-pelanggaran-cbt');
            let inputGform = document.getElementById('input-pelanggaran-gform');
            if(inputCbt) inputCbt.value = "1";
            if(inputGform) inputGform.value = "1";
        }
        @if($ujian->metode_ujian == 'gform')
            document.getElementById('form-auto-selesai').submit();
        @else
            document.getElementById('form-selesai-cbt').submit();
        @endif
    }

    function catatPelanggaran(pesan) {
        if (isWarningOpen) return; 
        isWarningOpen = true;
        cheatCount++;
        alarmSound.play().catch(e => console.log("Audio tertahan browser"));

        setTimeout(() => {
            if (cheatCount >= maxCheat) {
                alert('🚨 PENTING: Anda telah mencapai batas maksimal pelanggaran (3x). Ujian dihentikan paksa dan pekerjaan Anda langsung dikumpulkan!');
                alarmSound.pause();
                triggerSelesai(true);
            } else {
                alert(`🚨 PERINGATAN KECURANGAN: ${pesan} \n\nPelanggaran: ${cheatCount} dari ${maxCheat}. Jika mencapai batas maksimal, ujian akan tertutup otomatis!`);
                alarmSound.pause();
                isWarningOpen = false;
            }
        }, 100);
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') catatPelanggaran('Sistem mendeteksi Anda meninggalkan halaman ujian!');
    });

    window.addEventListener('blur', function() {
        setTimeout(function() {
            if (document.activeElement.tagName !== 'IFRAME' && document.activeElement.tagName !== 'BODY') {
                catatPelanggaran('Layar kehilangan fokus! Dilarang membuka notifikasi atau aplikasi lain!');
            }
        }, 150); 
    });

    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            setTimeout(function() {
                catatPelanggaran('PELANGGARAN! Anda terdeteksi keluar dari mode Layar Penuh!');
                if (cheatCount < maxCheat) openFullscreen(); 
            }, 200);
        }
    });

    document.addEventListener('mouseleave', function(e) {
        if (e.clientY < 0 || e.clientX < 0 || (e.clientX > window.innerWidth || e.clientY > window.innerHeight)) {
            if(!isWarningOpen) catatPelanggaran('Kursor Anda keluar dari area ujian! Tetap fokus pada halaman.');
        }
    });

    window.onbeforeunload = function(e) { return "Yakin ingin memuat ulang? Jawaban Anda mungkin akan hilang!"; };
    document.getElementById('btn-kembali')?.addEventListener('click', function() { window.onbeforeunload = null; });
    document.getElementById('btn-selesai')?.addEventListener('click', function() { window.onbeforeunload = null; });

    document.onkeydown = function(e) {
        if (e.keyCode === 123 || e.keyCode === 116 || (e.ctrlKey && e.keyCode === 82)) return false; 
        if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 80)) return false; 
        if (e.ctrlKey && e.shiftKey && e.keyCode === 73) return false; 
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) return false; 
    };

    function openFullscreen() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen(); } 
        else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); } 
        else if (elem.msRequestFullscreen) { elem.msRequestFullscreen(); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.createElement("div");
        overlay.id = "fullscreen-overlay";
        overlay.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.98); z-index:9999; display:flex; flex-direction:column; justify-content:center; align-items:center; backdrop-filter: blur(10px);";
        
        const lockIcon = document.createElement("div");
        lockIcon.innerHTML = "<i class='fas fa-shield-alt fa-4x text-primary mb-4'></i>";
        
        const btnFullscreen = document.createElement("button");
        btnFullscreen.innerHTML = "MASUK MODE UJIAN AMAN";
        btnFullscreen.className = "btn btn-primary btn-lg rounded-pill shadow-lg fw-bold px-5 py-3 fs-5";
        
        const textHint = document.createElement("p");
        textHint.innerHTML = "Layar penuh diperlukan. Sistem anti-curang akan aktif.";
        textHint.className = "text-white opacity-50 mt-4";

        overlay.appendChild(lockIcon);
        overlay.appendChild(btnFullscreen);
        overlay.appendChild(textHint);
        document.body.appendChild(overlay);

        btnFullscreen.addEventListener("click", function() {
            openFullscreen();
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
            
            // Animasi Fade-in Modern
            let areaSoal = $('#area-soal');
            areaSoal.removeClass('fade-in-up');
            void document.getElementById('area-soal').offsetWidth; 
            areaSoal.addClass('fade-in-up');

            $('#label-nomor-soal').text(index + 1);
            
            $('.btn-nav-soal').removeClass('btn-primary border-primary').css('box-shadow', 'none');$(`#nav-${index}`).addClass('btn-primary').css('box-shadow', '0 4px 12px rgba(13,110,253,0.3)');

            if (soal.jawaban_siswa) {
                $('#label-status-jawab').removeClass('text-secondary bg-secondary').addClass('text-success').css('background-color', '#dcfce7').html('<i class="fas fa-check-circle me-1"></i> Terdijawab: ' + soal.jawaban_siswa);
            } else {
                $('#label-status-jawab').removeClass('text-success').css('background-color', '#f1f5f9').addClass('text-secondary').text('Belum Dijawab');
            }

            let htmlSoal = '';
            if(soal.gambar) {
                htmlSoal += `<div class="text-center mb-4"><img src="/uploads/soal/${soal.gambar}" class="img-fluid rounded-4 shadow-sm border" style="max-height: 280px;"></div>`;
            }
            htmlSoal += `<div class="fs-5 mb-4 text-dark" style="line-height: 1.6;">${soal.pertanyaan}</div>`;

            // Render Opsi
            const opsi = ['A', 'B', 'C', 'D', 'E'];
            opsi.forEach(opt => {
                let teksOpsi = soal['opsi_' + opt.toLowerCase()];
                if(teksOpsi) {
                    let isChecked = (soal.jawaban_siswa === opt) ? 'terpilih' : '';
                    let kontenFormatted = formatKontenOpsi(teksOpsi);
                    
                    htmlSoal += `
                        <div class="opsi-jawaban ${isChecked}" onclick="simpanJawaban(${soal.id}, '${opt}', ${index})">
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

        function simpanJawaban(soal_id, jawaban, indexArr) {
            $('.opsi-jawaban').removeClass('terpilih');
            $(event.currentTarget).addClass('terpilih');$.ajax({
                url: "{{ route('siswa.ujian.simpan_jawaban', $ujian->id) }}",
                type: "POST",
                data: { _token: $('meta[name="csrf-token"]').attr('content'), soal_id: soal_id, jawaban: jawaban },
                success: function(response) {
                    dataSoal[indexArr].jawaban_siswa = jawaban;
                    $(`#nav-${indexArr}`).removeClass('btn-outline-secondary btn-primary').addClass('btn-success text-white border-success shadow-sm');
                    $('#label-status-jawab').removeClass('text-secondary bg-secondary').addClass('text-success').css('background-color', '#dcfce7').html('<i class="fas fa-check-circle me-1"></i> Terdijawab: ' + jawaban);
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
                document.getElementById('form-selesai-cbt').submit();
            }
        }
    @endif
    @endif
</script>

@if(session()->has('ujian_verified_' . $ujian->id) &&$ujian->metode_ujian == 'gform')
<form id="form-auto-selesai" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="pelanggaran" id="input-pelanggaran-gform" value="0">
</form>
@endif
</body>
</html>