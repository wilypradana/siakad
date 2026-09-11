<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD CBT - {{ $ujian->judul_ujian }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html { margin: 0; padding: 0; height: 100%; background-color: #f8f9fa; }
        .top-bar { position: fixed; top: 0; left: 0; width: 100%; height: 60px; background: #0d6efd; color: white; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 1000; }
        .gform-container { width: 100%; height: calc(100vh - 60px); border: none; margin-top: 60px; }
        .gate-center { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    </style>
</head>
<body>

   <div class="top-bar">
    <div class="fw-bold"><i class="fas fa-laptop-code me-2"></i> {{ $ujian->mapel->nama_mapel }}</div>
    
    <div class="d-flex gap-2">
        {{-- Tombol Kembali biasa (Hanya kembali tanpa menandai selesai) --}}
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-light text-primary fw-bold">Kembali</a>
        
        {{-- Tombol Konfirmasi Selesai (Hanya muncul jika sudah verifikasi kode) --}}
        @if(session()->has('ujian_verified_' . $ujian->id))
            <form action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri ujian? Pastikan jawaban Google Form sudah di-SUBMIT!')">
                @csrf
                <button type="submit" class="btn btn-sm btn-success fw-bold">Selesai Ujian</button>
            </form>
        @endif
    </div>
</div>


    {{-- Gunakan variabel $siswa yang sudah dipastikan tidak NULL oleh Controller --}}
    @if($siswa->status_bayar == 0)
        {{-- BLOKIR JIKA BELUM BAYAR --}}
        <div class="gate-center">
            <div class="card shadow border-0 text-center p-5" style="max-width: 500px;">
                <i class="fas fa-lock text-danger fa-4x mb-4"></i>
                <h3 class="fw-bold">Akses Terkunci!</h3>
                <p>Silakan selesaikan administrasi di Tata Usaha untuk mendapatkan kode kartu ujian.</p>
            </div>
        </div>
    @elseif(!session()->has('ujian_verified_' . $ujian->id))
        {{-- INPUT KODE JIKA SUDAH BAYAR TAPI BELUM VERIFIKASI --}}
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
        {{-- TAMPILKAN SOAL JIKA LOLOS SEMUA --}}
        <iframe src="{{ $ujian->link_gform }}?embedded=true" class="gform-container" frameborder="0">Memuat...</iframe>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script>
    @if(session()->has('ujian_verified_' . $ujian->id))
    // --- 1. DETEKSI PINDAH TAB / MINIMIZE ---
    let cheatCount = 0;
    const maxCheat = 3; 

    // Mencegah error MethodNotAllowed, kita gunakan form tersembunyi untuk auto-submit
    function triggerSelesai() {
        document.getElementById('form-auto-selesai').submit();
    }

    // Deteksi jika tab pindah (lebih stabil dari onblur)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            cheatCount++;
            
            if (cheatCount >= maxCheat) {
                alert('PENTING: Anda telah melebihi batas perpindahan halaman. Ujian otomatis ditutup!');
                triggerSelesai();
            } else {
                alert('PERINGATAN: Dilarang membuka tab lain atau meminimalkan browser selama ujian! \nPelanggaran: ' + cheatCount + '/' + maxCheat);
            }
        }
    });

    // Hapus window.onblur karena terlalu sensitif terhadap klik di luar iframe (seperti notif OS)
    // window.onblur = function() { ... } <-- DIHAPUS

    // --- 2. PROTEKSI KLIK KANAN & KEYBOARD ---
    document.addEventListener('contextmenu', event => event.preventDefault());

    document.onkeydown = function(e) {
        if (e.keyCode === 123) return false;
        if (e.ctrlKey && e.keyCode === 85) return false;
        if (e.ctrlKey && e.shiftKey && e.keyCode === 73) return false;
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86)) return false;
    };

    // --- 3. AUTO FULL SCREEN ---
    function openFullscreen() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    function closeFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    // Gunakan event listener untuk menghindari pemblokiran pop-up browser
    document.addEventListener('DOMContentLoaded', function() {
        const btnFullscreen = document.createElement("button");
        btnFullscreen.innerHTML = "Masuk Mode Ujian (Layar Penuh)";
        btnFullscreen.className = "btn btn-danger btn-lg position-absolute top-50 start-50 translate-middle shadow-lg";
        btnFullscreen.style.zIndex = "9999";
        
        document.body.appendChild(btnFullscreen);

        btnFullscreen.addEventListener("click", function() {
            openFullscreen();
            this.remove(); 
        });
    });

    $('.btn-light').on('click', function() {
        closeFullscreen();
    });
    @endif
</script>

{{-- Form tersembunyi untuk proses auto-selesai via JS --}}
@if(session()->has('ujian_verified_' . $ujian->id))
<form id="form-auto-selesai" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
</form>
@endif

</body>
</html>