<!DOCTYPE html>
@php
    // Deteksi apakah ini ujian baru/hasil reset dengan menghitung jumlah jawaban tersimpan
    $jumlah_jawaban = \App\Models\JawabanSiswa::where('ujian_id', $ujian->id)
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
        .gform-container { width: 100%; height: calc(100vh - 60px); border: none; margin-top: 60px; pointer-events: auto; }
        .cbt-container { width: 100%; height: calc(100vh - 60px); margin-top: 60px; overflow-y: auto; padding: 20px; }
        .gate-center { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        
        /* Desain Tambahan untuk CBT */
        .btn-nav-soal { width: 45px; height: 45px; margin: 3px; font-weight: bold; }
        .opsi-jawaban { cursor: pointer; transition: 0.2s; border: 2px solid transparent; }
        .opsi-jawaban:hover { background-color: #e9ecef; }
        .opsi-jawaban.terpilih { border-color: #0d6efd; background-color: #e7f1ff; font-weight: bold; }
        .opsi-huruf { display: inline-block; width: 30px; font-weight: bold; }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

   <div class="top-bar">
    <div class="fw-bold"><i class="fas fa-laptop-code me-2"></i> {{ $ujian->mapel->nama_mapel }}</div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-sm btn-light text-primary fw-bold" id="btn-kembali">Kembali</a>
        
        @if(session()->has('ujian_verified_' . $ujian->id))
            @if($ujian->metode_ujian == 'gform')
                <form action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri ujian? Pastikan jawaban Google Form sudah di-SUBMIT!')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success fw-bold" id="btn-selesai">Selesai Ujian</button>
                </form>
            @else
                <button type="button" class="btn btn-sm btn-success fw-bold" id="btn-selesai-cbt" onclick="akhiriCBT()">Selesai Ujian</button>
            @endif
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
        
        <!-- ============================================== -->
        <!-- LOGIKA PEMISAH TAMPILAN GFORM VS CBT           -->
        <!-- ============================================== -->
        @if($ujian->metode_ujian == 'gform')
            <!-- TAMPILAN GOOGLE FORM -->
            <iframe src="{{ $ujian->link_gform }}?embedded=true" class="gform-container" id="gform-iframe" frameborder="0">Memuat...</iframe>
        @else
            <!-- TAMPILAN CBT LOKAL -->
            <div class="cbt-container">
                <div class="row">
                    <!-- Kolom Kiri: Teks Soal -->
                    <div class="col-md-8 mb-4">
                        <div class="card shadow border-0 h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-primary">Soal No. <span id="label-nomor-soal">1</span></h5>
                                <span class="badge bg-secondary fs-6" id="label-status-jawab">Belum Dijawab</span>
                            </div>
                            <div class="card-body fs-5" id="area-soal">
                                <!-- Konten soal dirender oleh JS -->
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-3"></i><br>Menyiapkan soal ujian...
                                </div>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between py-3">
                                <button class="btn btn-secondary px-4 fw-bold" id="btn-prev" onclick="navigasiSoal('prev')"><i class="fas fa-arrow-left me-2"></i> Sebelumnya</button>
                                <button class="btn btn-primary px-4 fw-bold" id="btn-next" onclick="navigasiSoal('next')">Selanjutnya <i class="fas fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Timer & Navigasi -->
                    <div class="col-md-4">
                        <!-- Kotak Timer -->
                        <div class="card shadow border-0 mb-3">
                            <div class="card-header bg-danger text-white text-center">
                                <h5 class="fw-bold mb-0"><i class="fas fa-clock me-2"></i> Sisa Waktu Ujian</h5>
                            </div>
                            <div class="card-body text-center bg-light">
                                <h1 id="timer-ujian" class="fw-bold text-danger mb-0" style="font-size: 3rem;">00:00:00</h1>
                            </div>
                        </div>

                        <!-- Kotak Navigasi Kotak -->
                        <div class="card shadow border-0">
                            <div class="card-header bg-white text-center">
                                <h6 class="fw-bold mb-0 text-secondary">Navigasi Soal</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-center" id="kotak-navigasi">
                                    <!-- Tombol kotak digenerate JS -->
                                </div>
                                <hr>
                                <div class="d-flex justify-content-around small text-muted mb-3">
                                    <div><span class="badge bg-success border border-success d-inline-block" style="width: 15px; height:15px;"></span> Sudah</div>
                                    <div><span class="badge bg-white text-dark border border-secondary d-inline-block" style="width: 15px; height:15px;"></span> Belum</div>
                                    <div><span class="badge bg-primary border border-primary d-inline-block" style="width: 15px; height:15px;"></span> Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Tersembunyi untuk Akhiri Ujian CBT -->
            <form id="form-selesai-cbt" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif
        <!-- ============================================== -->
        
    @endif

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script>
    @if(session()->has('ujian_verified_' . $ujian->id))
    
    // ==========================================
    // BAGIAN 1: SISTEM ANTI CHEAT (TETAP SAMA)
    // ==========================================
    let cheatCount = 0;
    const maxCheat = 3; 
    let isWarningOpen = false;

    const alarmSound = new Audio('https://assets.mixkit.co/active_storage/sfx/991/991-preview.mp3');
    alarmSound.loop = true;

    function triggerSelesai(isPelanggaran = false) {
        window.onbeforeunload = null;
        
        // Tandai ke sistem jika ini adalah submit paksa akibat curang
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
                triggerSelesai(true); // Lempar nilai TRUE untuk menandai curang
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
        overlay.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; display:flex; justify-content:center; align-items:center;";
        
        const btnFullscreen = document.createElement("button");
        btnFullscreen.innerHTML = "<i class='fas fa-expand'></i> MULAI UJIAN (LAYAR PENUH)";
        btnFullscreen.className = "btn btn-danger btn-lg shadow-lg fw-bold px-4 py-3";
        
        overlay.appendChild(btnFullscreen);
        document.body.appendChild(overlay);

        btnFullscreen.addEventListener("click", function() {
            openFullscreen();
            document.getElementById('fullscreen-overlay').remove(); 
            
            // Jika mode CBT, mulai inisialisasi soal setelah masuk fullscreen
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
        let waktuSelesai = new Date("{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('M d, Y H:i:s') }}").getTime();

        // 1. Inisialisasi CBT (Ambil soal dari server via AJAX)
        function initCBT() {
            $.ajax({
                url: "{{ route('siswa.ujian.get_soal', $ujian->id) }}", // ROUTE INI AKAN KITA BUAT NANTI
                type: "GET",
                success: function(response) {
                    dataSoal = response;
                    if(dataSoal.length > 0) {
                        renderNavigasi();
                        tampilkanSoal(0);
                        mulaiTimer();
                    } else {
                        $('#area-soal').html('<div class="alert alert-danger">Maaf, guru belum memasukkan bank soal untuk ujian ini.</div>');
                    }
                },
                error: function() {
                    alert("Terjadi kesalahan saat mengambil soal. Periksa koneksi internet Anda.");
                }
            });
        }

        // 2. Merender Kotak Navigasi Soal di Sebelah Kanan
        function renderNavigasi() {
            let html = '';
            dataSoal.forEach((soal, index) => {
                let statusClass = soal.jawaban_siswa ? 'btn-success text-white' : 'btn-outline-secondary';
                html += `<button class="btn ${statusClass} btn-nav-soal" id="nav-${index}" onclick="tampilkanSoal(${index})">${index + 1}</button>`;
            });
            $('#kotak-navigasi').html(html);
        }

        // 3. Menampilkan Soal di Kolom Tengah
        function tampilkanSoal(index) {
            indexAktif = index;
            let soal = dataSoal[index];
            
            $('#label-nomor-soal').text(index + 1);
            
            // Perbarui warna kotak navigasi yang aktif
            $('.btn-nav-soal').removeClass('btn-primary border-3').css('border', '');$(`#nav-${index}`).addClass('btn-primary').css('border', '2px solid #000');

            // Render Status
            if (soal.jawaban_siswa) {
                $('#label-status-jawab').removeClass('bg-secondary').addClass('bg-success').text('Terdijawab: ' + soal.jawaban_siswa);
            } else {
                $('#label-status-jawab').removeClass('bg-success').addClass('bg-secondary').text('Belum Dijawab');
            }

            // Render Teks Soal & Gambar
            let htmlSoal = '';
            if(soal.gambar) {
                // Sesuaikan path gambar jika diletakkan di folder public/uploads/soal/
                htmlSoal += `<img src="/uploads/soal/${soal.gambar}" class="img-fluid mb-4 rounded shadow-sm" style="max-height: 250px;">`;
            }
            htmlSoal += `<p class="mb-4">${soal.pertanyaan}</p>`;

            // Render Pilihan Jawaban
            const opsi = ['A', 'B', 'C', 'D', 'E'];
            opsi.forEach(opt => {
                let teksOpsi = soal['opsi_' + opt.toLowerCase()];
                if(teksOpsi) {
                    let isChecked = (soal.jawaban_siswa === opt) ? 'terpilih' : '';
                    htmlSoal += `
                        <div class="card mb-2 opsi-jawaban ${isChecked}" onclick="simpanJawaban(${soal.id}, '${opt}', ${index})">
                            <div class="card-body p-3">
                                <span class="opsi-huruf">${opt}.</span> ${teksOpsi}
                            </div>
                        </div>
                    `;
                }
            });

            $('#area-soal').html(htmlSoal);

            // Atur tombol prev/next
            $('#btn-prev').prop('disabled', index === 0);
            $('#btn-next').prop('disabled', index === dataSoal.length - 1);
        }

        // 4. Navigasi Tombol Bawah
        function navigasiSoal(arah) {
            if (arah === 'next' && indexAktif < dataSoal.length - 1) tampilkanSoal(indexAktif + 1);
            if (arah === 'prev' && indexAktif > 0) tampilkanSoal(indexAktif - 1);
        }

        // 5. Simpan Jawaban secara AJAX saat diklik
        function simpanJawaban(soal_id, jawaban, indexArr) {
            
            // Efek visual sementara agar terasa responsif
            $('.opsi-jawaban').removeClass('terpilih');
            $(event.currentTarget).addClass('terpilih');$.ajax({
                url: "{{ route('siswa.ujian.simpan_jawaban', $ujian->id) }}", // ROUTE INI AKAN KITA BUAT NANTI
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    soal_id: soal_id,
                    jawaban: jawaban
                },
                success: function(response) {
                    // Update data lokal
                    dataSoal[indexArr].jawaban_siswa = jawaban;
                    
                    // Update warna kotak navigasi
                    $(`#nav-${indexArr}`).removeClass('btn-outline-secondary btn-primary').addClass('btn-success text-white');
                    $('#label-status-jawab').removeClass('bg-secondary').addClass('bg-success').text('Terdijawab: ' + jawaban);
                },
                error: function() {
                    alert("Koneksi terputus! Gagal menyimpan jawaban. Periksa koneksi internet Anda.");
                    $('.opsi-jawaban').removeClass('terpilih'); // Batalkan visual
                }
            });
        }

        // 6. Timer Hitung Mundur Server
        function mulaiTimer() {
            let x = setInterval(function() {
                let sekarang = new Date().getTime();
                let jarak = waktuSelesai - sekarang;

                if (jarak < 0) {
                    clearInterval(x);
                    $('#timer-ujian').text("WAKTU HABIS");
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
                
                // Peringatan merah jika kurang dari 5 menit
                if (jarak < 300000) {
                    $('#timer-ujian').removeClass('text-danger').addClass('text-warning blink');
                }
            }, 1000);
        }

        // 7. Akhiri Ujian CBT Manual
        function akhiriCBT() {
            // Cek apakah ada yang belum dijawab
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

<script>
   
    // 4. Hitung mundur setiap 1 detik
        let durasiMenit = {{ $ujian->durasi }}; 
        let ujianId = {{ $ujian->id }};
        let batasTutupSistem = new Date("{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('Y-m-d\TH:i:s') }}").getTime();
        let storageKey = "waktu_selesai_ujian_" + ujianId;

        @if($jumlah_jawaban == 0)
        localStorage.removeItem(storageKey);
        @endif

        // 2. Buat kunci memori khusus untuk ujian ini
        let waktuSelesaiTarget = localStorage.getItem(storageKey);
        
        // Jika siswa baru pertama kali klik "Kerjakan" (belum ada memori)
                // 6. Timer Hitung Mundur Server (Versi Smart Duration)
        function mulaiTimer() {
            let waktuSelesaiTarget = localStorage.getItem(storageKey);
            
            // Jika siswa baru pertama kali klik "Kerjakan" (belum ada memori)
            if (!waktuSelesaiTarget) {
                let waktuSekarang = new Date().getTime();
                waktuSelesaiTarget = waktuSekarang + (durasiMenit * 60 * 1000);
                
                // Jika jatah durasi siswa melewati jam tutup ujian, paksa gunakan jam tutup
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
                    localStorage.removeItem(storageKey); // Bersihkan memori browser
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
                
                // Peringatan merah berkedip jika kurang dari 5 menit
                if (jarak < 300000) {
                    $('#timer-ujian').removeClass('text-danger').addClass('text-warning blink');
                }
            }, 1000);
        }
</script>

<!-- Form Tersembunyi untuk Akhiri Ujian CBT -->
<form id="form-selesai-cbt" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="pelanggaran" id="input-pelanggaran-cbt" value="0">
</form>

<!-- Form Tersembunyi untuk Akhiri Ujian G-Form -->
@if(session()->has('ujian_verified_' . $ujian->id) && $ujian->metode_ujian == 'gform')
<form id="form-auto-selesai" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="pelanggaran" id="input-pelanggaran-gform" value="0">
</form>
@endif
</body>
</html>