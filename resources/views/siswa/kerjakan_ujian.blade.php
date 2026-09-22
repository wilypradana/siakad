<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD CBT - {{ $ujian->judul_ujian }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* PROTEKSI ANTI-COPY & SELECT TEXT */
        body, html { 
            margin: 0; padding: 0; height: 100%; background-color: #f8f9fa;
            -webkit-user-select: none; 
            -moz-user-select: none;    
            -ms-user-select: none;     
            user-select: none;         
            -webkit-touch-callout: none;
            overflow: hidden; /* Mencegah scroll di luar iframe */
        }
        .top-bar { position: fixed; top: 0; left: 0; width: 100%; height: 60px; background: #0d6efd; color: white; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 1000; }
        /* Iframe harus bisa diklik, tapi sisanya mati */
        .gform-container { width: 100%; height: calc(100vh - 60px); border: none; margin-top: 60px; pointer-events: auto; }
        .gate-center { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

   <div class="top-bar">
    <div class="fw-bold"><i class="fas fa-laptop-code me-2"></i> {{ $ujian->mapel->nama_mapel }}</div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-light text-primary fw-bold" id="btn-kembali">Kembali</a>
        
        @if(session()->has('ujian_verified_' . $ujian->id))
            <form action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri ujian? Pastikan jawaban Google Form sudah di-SUBMIT!')">
                @csrf
                <button type="submit" class="btn btn-sm btn-success fw-bold" id="btn-selesai">Selesai Ujian</button>
            </form>
        @endif
    </div>
</div>

    @if($siswa->status_bayar == 0)
        <div class="gate-center">
            <div class="card shadow border-0 text-center p-5" style="max-width: 500px;">
                <i class="fas fa-lock text-danger fa-4x mb-4"></i>
                <h3 class="fw-bold">Akses Terkunci!</h3>
                <p>Silakan selesaikan administrasi di Tata Usaha untuk mendapatkan kode kartu ujian.</p>
            </div>
        </div>
    @elseif(!session()->has('ujian_verified_' . $ujian->id))
        <div class="gate-center">
            <div class="card shadow border-0 p-5" style="max-width: 450px;">
                <h4 class="fw-bold text-center mb-4">Verifikasi Kode Kartu</h4>
                @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
                <form action="{{ route('siswa.ujian.verifikasi', $ujian->id) }}" method="POST">
                    @csrf
                    <input type="text" name="kode_input" class="form-control form-control-lg text-center mb-3 fw-bold" placeholder="Masukkan Kode Kartu" required>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Mulai Ujian</button>
                </form>
            </div>
        </div>
    @else
        <!-- IFRAME GOOGLE FORM -->
        <iframe src="{{ $ujian->link_gform }}?embedded=true" class="gform-container" id="gform-iframe" frameborder="0">Memuat...</iframe>
    @endif

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script>
    @if(session()->has('ujian_verified_' . $ujian->id))
    let cheatCount = 0;
    const maxCheat = 2; 
    let isWarningOpen = false; // Mencegah alert bertumpuk

    // 1. Siapkan Suara Alarm (Menggunakan file sirine publik atau ganti dengan file mp3 di folder public Anda)
    const alarmSound = new Audio('https://assets.mixkit.co/active_storage/sfx/991/991-preview.mp3');
    alarmSound.loop = true; // Buat suaranya berulang terus

    function triggerSelesai() {
        window.onbeforeunload = null; // Matikan pencegah reload sebelum submit paksa
        document.getElementById('form-auto-selesai').submit();
    }

    function catatPelanggaran(pesan) {
        if (isWarningOpen) return; 
        isWarningOpen = true;
        
        cheatCount++;
        
        // 2. Mainkan Suara Alarm secara paksa!
        alarmSound.play().catch(e => console.log("Audio tertahan browser"));

        // Beri jeda 100ms agar audio sempat diputar sebelum alert menghentikan layar
        setTimeout(() => {
            if (cheatCount >= maxCheat) {
                alert('🚨 PENTING: Anda telah mencapai batas maksimal pelanggaran. Ujian otomatis ditutup!');
                alarmSound.pause(); // Matikan suara saat di-redirect
                triggerSelesai();
            } else {
                alert(`🚨 PERINGATAN KECURANGAN: ${pesan} \n\nPelanggaran: ${cheatCount} dari ${maxCheat}`);
                alarmSound.pause(); // Matikan suara setelah siswa menekan tombol "OK"
                isWarningOpen = false;
            }
        }, 100);
    }

    // 1. DETEKSI PINDAH TAB / MINIMIZE
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            catatPelanggaran('Sistem mendeteksi Anda meninggalkan halaman ujian!');
        }
    });

    // 2. DETEKSI BLUR SUPER SENSITIF (Menangkap "Touch to Search" Chrome & Split Screen)
    window.addEventListener('blur', function() {
        setTimeout(function() {
            if (document.activeElement.tagName !== 'IFRAME') {
                catatPelanggaran('Layar kehilangan fokus! Dilarang membuka notifikasi atau aplikasi lain!');
            }
        }, 150); 
    });

    document.addEventListener('fullscreenchange', function() {
        // Jika layar penuh tiba-tiba mati (biasanya karena pop-up AI Google, notifikasi, atau menggeser layar atas/bawah)
        if (!document.fullscreenElement) {
            // Beri jeda sedikit agar tidak bentrok dengan proses submit
            setTimeout(function() {
                catatPelanggaran('PELANGGARAN! Anda terdeteksi keluar dari mode Layar Penuh (Membuka fitur pencarian/notifikasi dilarang)!');
                
                // Opsional: Paksa mereka masuk fullscreen lagi jika belum diblokir
                if (cheatCount < maxCheat) {
                    openFullscreen(); 
                }
            }, 200);
        }
    });

    // 3. DETEKSI KURSOR KELUAR BROWSER (Khusus Laptop/PC)
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY < 0 || e.clientX < 0 || (e.clientX > window.innerWidth || e.clientY > window.innerHeight)) {
            // Hanya deteksi sebagai pelanggaran jika tidak sedang berinteraksi dengan pop-up alert
            if(!isWarningOpen) {
                catatPelanggaran('Kursor Anda keluar dari area ujian! Tetap fokus pada halaman.');
            }
        }
    });

    // 4. ANTI-REFRESH / BACK BUTTON ACCIDENTAL
    window.onbeforeunload = function(e) {
        return "Yakin ingin memuat ulang? Jawaban Google Form Anda mungkin akan hilang!";
    };
    // Izinkan tombol "Selesai" dan "Kembali" kita sendiri tanpa memunculkan peringatan
    document.getElementById('btn-kembali')?.addEventListener('click', function() { window.onbeforeunload = null; });
    document.getElementById('btn-selesai')?.addEventListener('click', function() { window.onbeforeunload = null; });

    // 5. PROTEKSI KEYBOARD TOTAL (Block Inspect Element, Copy, Print)
    document.onkeydown = function(e) {
        if (e.keyCode === 123 || e.keyCode === 116 || (e.ctrlKey && e.keyCode === 82)) return false; // F12, F5, Ctrl+R
        if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 80)) return false; // Ctrl+U, Ctrl+P
        if (e.ctrlKey && e.shiftKey && e.keyCode === 73) return false; // Ctrl+Shift+I
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) return false; // Ctrl+C, V, X
    };

    // 6. AUTO FULLSCREEN PAKSA
    function openFullscreen() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) { elem.requestFullscreen(); } 
        else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); } 
        else if (elem.msRequestFullscreen) { elem.msRequestFullscreen(); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.createElement("div");
        overlay.id = "fullscreen-overlay";
        overlay.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; display:flex; justify-content:center; align-items:center;";
        
        const btnFullscreen = document.createElement("button");
        btnFullscreen.innerHTML = "<i class='fas fa-expand'></i> MULAI UJIAN (LAYAR PENUH)";
        btnFullscreen.className = "btn btn-danger btn-lg shadow-lg fw-bold px-4 py-3";
        
        overlay.appendChild(btnFullscreen);
        document.body.appendChild(overlay);

        btnFullscreen.addEventListener("click", function() {
            openFullscreen();
            document.getElementById('fullscreen-overlay').remove(); 
        });
    });
    @endif
</script>

@if(session()->has('ujian_verified_' . $ujian->id))
<form id="form-auto-selesai" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
</form>
@endif

</body>
</html>
