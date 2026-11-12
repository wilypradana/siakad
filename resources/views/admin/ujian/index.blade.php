@extends('layouts.admin')

@section('title', 'Manajemen Portal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-laptop-code me-2 text-primary"></i> Portal Ujian (G-Form & CBT)
        </h3>
        <p class="text-muted small mb-0">Kelola jadwal ujian, jenis ujian, serta pemantauan akses siswa.</p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->role == 'admin')
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJenis">
                <i class="fas fa-tags me-1"></i> Buat Jenis Ujian
            </button>
        @endif
        
        <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUjian">
            <i class="fas fa-plus me-1"></i> Buat Jadwal Ujian
        </button>
    </div>
</div>

@if(auth()->user()->role == 'admin')
<div class="card border shadow-sm rounded-3 mb-4" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex align-items-center">
        <h5 class="fw-bold text-dark mb-0 fs-6"><i class="fas fa-tags me-2 text-primary"></i> Data Master Jenis Ujian</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0; font-size: 0.85rem;">
                    <tr>
                        <th class="py-3 ps-4" width="8%">No</th>
                        <th class="py-3">Nama Jenis Ujian</th>
                        <th class="py-3 text-center pe-4" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_jenis as $index => $jenis)
                    <tr>
                        <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $jenis->nama_jenis }}</td>
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-outline-warning border-0 px-2" data-bs-toggle="modal" data-bs-target="#modalEditJenis{{ $jenis->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0 px-2" data-bs-toggle="modal" data-bs-target="#modalHapusJenis{{ $jenis->id }}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit Jenis -->
                    <div class="modal fade" id="modalEditJenis{{ $jenis->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                                <div class="modal-header bg-light border-bottom">
                                    <h5 class="modal-title fw-bold text-dark fs-6">Edit Jenis Ujian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('ujian.jenis.update', $jenis->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body text-start">
                                        <label class="fw-bold small text-dark mb-1">Nama Jenis Ujian</label>
                                        <input type="text" name="nama_jenis" class="form-control" value="{{ $jenis->nama_jenis }}" required style="border-radius: 8px;">
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus Jenis -->
                    <div class="modal fade" id="modalHapusJenis{{ $jenis->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                                <div class="modal-header bg-danger text-white border-0">
                                    <h5 class="modal-title fw-bold fs-6">Hapus Jenis Ujian</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start text-dark">
                                    Yakin ingin menghapus jenis ujian <b>{{ $jenis->nama_jenis }}</b>?
                                </div>
                                <div class="modal-footer bg-light border-top">
                                    <form action="{{ route('ujian.jenis.destroy', $jenis->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4 small">Belum ada jenis ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3" role="alert" style="background-color: #f8d7da; color: #842029;">
        {{ session('error') }}
    </div>
@endif

<div class="card border shadow-sm rounded-3 mb-5" style="border-color: #e2e8f0 !important;">
    <!-- FORM HAPUS MASSAL -->
    <form action="{{ route('ujian.bulkDelete') }}" method="POST">
        @csrf
        
        <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold text-dark mb-0 fs-6"><i class="fas fa-list me-2 text-primary"></i> Daftar Jadwal Ujian Aktif</h5>
            <div class="d-flex gap-2">
                <button type="submit" formaction="{{ route('ujian.bulkToggle', ['status' => 1]) }}" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                    <i class="fas fa-lock-open me-1"></i> Buka Terpilih
                </button>
                <button type="submit" formaction="{{ route('ujian.bulkToggle', ['status' => 0]) }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark shadow-sm">
                    <i class="fas fa-lock me-1"></i> Tutup Terpilih
                </button>
                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold shadow-sm" onclick="return confirm('Yakin ingin menghapus semua jadwal ujian yang diceklis?')">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
                </button>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle w-100 mb-0" id="tableUjian" style="font-size: 0.88rem;">
                    <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th width="3%" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th width="4%" class="text-center">No</th>
                            <th width="20%">Judul & Jenis Ujian</th>
                            <th width="15%">Mata Pelajaran</th>
                            <th width="10%">Kelas</th>
                            <th width="16%">Waktu Pelaksanaan</th>
                            <th width="14%">Guru</th>
                            <th width="9%" class="text-center">Status</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_ujian as $index => $ujian)
                            @php
                                $sekarang = \Carbon\Carbon::now();
                                $mulai = \Carbon\Carbon::parse($ujian->waktu_mulai);
                                $selesai = \Carbon\Carbon::parse($ujian->waktu_selesai);
                                
                                if ($sekarang->lt($mulai)) { $status = 'Akan Datang'; $badge = 'bg-secondary'; } 
                                elseif ($sekarang->between($mulai, $selesai)) { $status = 'Sedang Berjalan'; $badge = 'bg-success blink'; } 
                                else { $status = 'Selesai'; $badge = 'bg-danger'; }
                            @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $ujian->id }}" class="form-check-input check-item" style="cursor: pointer;">
                            </td>
                            <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1 px-2 py-0.5" style="font-size: 10px;">{{ $ujian->jenisUjian->nama_jenis ?? 'Umum' }}</span><br>
                                <span class="fw-bold text-dark">{{ $ujian->judul_ujian }}</span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $ujian->mapel->nama_mapel }}</td>
                            <td><span class="badge rounded-pill border px-2.5 py-1" style="background-color: #eff6ff; color: #1e40af; border-color: #bfdbfe !important;">{{ $ujian->kelas->nama_kelas }}</span></td>
                            <td style="font-size: 0.8rem;">
                                <i class="fas fa-play-circle text-success me-1"></i> {{ $mulai->format('d/m/Y H:i') }} <br>
                                <i class="fas fa-stop-circle text-danger me-1"></i> {{ $selesai->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <span class="text-uppercase small text-muted fw-semibold" style="font-size: 0.78rem;">{{ $ujian->guru->nama ?? 'Admin' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badge }} px-2.5 py-1">{{ $status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($ujian->is_active)
                                        <a href="{{ route('ujian.toggle_status', $ujian->id) }}" class="btn btn-sm btn-danger text-white px-2 py-1" title="Kunci Akses">
                                            <i class="fas fa-lock fa-xs"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('ujian.toggle_status', $ujian->id) }}" class="btn btn-sm btn-success text-white px-2 py-1 pulse-button" title="Mulai Ujian">
                                            <i class="fas fa-lock-open fa-xs"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.soal.index', $ujian->id) }}" class="btn btn-sm btn-success text-white px-2 py-1" title="Kelola Soal CBT">
                                        <i class="fas fa-list-ol fa-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.ujian.hasil', $ujian->id) }}" class="btn btn-sm btn-primary text-white px-2 py-1" title="Rekap Nilai Siswa">
                                        <i class="fas fa-chart-bar fa-xs"></i>  
                                    </a>
                                    <a href="{{ $ujian->link_gform }}" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" title="Cek Link">
                                        <i class="fas fa-external-link-alt fa-xs"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info text-white px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalResetSiswa{{ $ujian->id }}" title="Reset Ujian Siswa">
                                        <i class="fas fa-sync-alt fa-xs"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning text-dark px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditUjian{{ $ujian->id }}" title="Edit">
                                        <i class="fas fa-edit fa-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<!-- MODAL TAMBAH JENIS UJIAN -->
<div class="modal fade" id="modalTambahJenis" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-6"><i class="fas fa-tags me-2 text-primary"></i>Tambah Jenis Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ujian.jenis.store') }}" method="POST">
                @csrf
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="fw-bold small text-dark mb-1">Nama Jenis Ujian <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jenis" class="form-control" placeholder="Contoh: Sumatif Tengah Semester Genap" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold"><i class="fas fa-save me-1"></i> Simpan Jenis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT DAN RESET -->
@foreach($data_ujian as $ujian)
    <!-- Modal Edit Jadwal Ujian -->
    <div class="modal fade" id="modalEditUjian{{ $ujian->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow text-start" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="fas fa-edit me-2 text-primary"></i>Edit Jadwal Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ujian.update', $ujian->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Jenis Ujian</label>
                            <select name="jenis_ujian_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                                @foreach($data_jenis as $jenis)
                                    <option value="{{ $jenis->id }}" {{ $ujian->jenis_ujian_id == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Judul Ujian Khusus</label>
                            <input type="text" name="judul_ujian" class="form-control form-control-sm" value="{{ $ujian->judul_ujian }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                                @foreach($data_mapel as $mapel)
                                    <option value="{{ $mapel->id }}" {{ $ujian->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Kelas Peserta</label>
                            <select name="kelas_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                                @foreach($data_kelas as $kelas)
                                    <option value="{{ $kelas->id }}" {{ $ujian->kelas_id == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if(auth()->user()->role == 'admin')
                        <div class="col-md-12">
                            <label class="small fw-bold">Guru Pengawas/Pembuat (Khusus Admin)</label>
                            <select name="guru_id" class="form-select form-select-sm" style="border-radius: 8px;">
                                <option value="">-- Ujian Umum / Dibuat Admin --</option>
                                @foreach($data_guru as $guru)
                                    <option value="{{ $guru->id }}" {{ $ujian->guru_id == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-12">
                            <label class="small fw-bold">Metode Ujian <span class="text-danger">*</span></label>
                            <select name="metode_ujian" class="form-select form-select-sm select-metode-edit" data-id="{{ $ujian->id }}" required style="border-radius: 8px;">
                                <option value="gform" {{ $ujian->metode_ujian == 'gform' ? 'selected' : '' }}>Google Form</option>
                                <option value="cbt" {{ $ujian->metode_ujian == 'cbt' ? 'selected' : '' }}>CBT Lokal (Bank Soal)</option>
                            </select>
                        </div>

                        <div class="col-md-12 kotak-link-edit-{{ $ujian->id }}" style="{{ $ujian->metode_ujian == 'cbt' ? 'display: none;' : '' }}">
                            <label class="small fw-bold">Link Google Form <span class="text-danger">*</span></label>
                            <input type="url" name="link_gform" id="input_link_gform_edit_{{ $ujian->id }}" class="form-control form-control-sm" value="{{ $ujian->link_gform }}" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold">Durasi Ujian (Menit) <span class="text-danger">*</span></label>
                            <input type="number" name="durasi" class="form-control form-control-sm" value="{{ $ujian->durasi }}" required style="border-radius: 8px;">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="small fw-bold">Waktu Mulai</label>
                            <input type="datetime-local" name="waktu_mulai" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('Y-m-d\TH:i') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Waktu Selesai</label>
                            <input type="datetime-local" name="waktu_selesai" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('Y-m-d\TH:i') }}" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-4 fw-semibold text-dark">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reset Ujian Siswa -->
    <div class="modal fade" id="modalResetSiswa{{ $ujian->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow text-start" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="fas fa-sync-alt me-2 text-primary"></i>Reset Ujian Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ujian.reset_siswa', $ujian->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning small border-0 py-2 mb-3 rounded-3" style="background-color: #fffbeb; color: #92400e;">
                            Pilih siswa yang mengalami kendala (tidak sengaja ter-submit/keluar) untuk mereset statusnya.
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small text-dark mb-1">Pilih Siswa</label>
                            <select name="siswa_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                                <option value="">-- Cari Nama Siswa --</option>
                                @foreach(\App\Models\Siswa::where('kelas_id', $ujian->kelas_id)->orderBy('nama', 'asc')->get() as $s)
                                    <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 fw-semibold text-white"><i class="fas fa-check me-1"></i> Reset Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- MODAL TAMBAH UJIAN -->
<div class="modal fade" id="modalTambahUjian" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-6"><i class="fas fa-plus-circle me-2 text-primary"></i>Buat Jadwal Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ujian.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">Jenis Ujian</label>
                        <select name="jenis_ujian_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($data_jenis as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Judul Ujian Khusus</label>
                        <input type="text" name="judul_ujian" class="form-control form-control-sm" placeholder="Contoh: Susulan TKJ" required style="border-radius: 8px;">
                    </div>
                    
                    <div class="col-md-12">
                        <label class="small fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" id="mapel_select" class="form-select form-select-sm" required style="border-radius: 8px;">
                            <option value="">-- Pilih Mapel Terlebih Dahulu --</option>
                            @foreach($data_mapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="fw-bold small text-primary">Kelas Peserta Ujian <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-1" style="font-size: 0.75rem;">Pilih kelas yang akan mengikuti ujian. Daftar kelas menyesuaikan Mapel yang dipilih.</p>
                        
                        <div class="border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto; border-radius: 8px;" id="container_kelas">
                            <div class="text-center text-muted small py-2">
                                Silakan pilih Mata Pelajaran terlebih dahulu.
                            </div>
                        </div>
                    </div>                    
                    
                    @if(auth()->user()->role == 'admin')
                    <div class="col-md-12">
                        <label class="small fw-bold">Guru Pengawas/Pembuat (Khusus Admin)</label>
                        <select name="guru_id" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="">-- Ujian Umum / Dibuat Admin --</option>
                            @foreach($data_guru as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-12">
                        <label class="small fw-bold">Metode Ujian <span class="text-danger">*</span></label>
                        <select name="metode_ujian" id="pilih_metode_tambah" class="form-select form-select-sm fw-bold text-primary" required style="border-radius: 8px;">
                            <option value="gform">Google Form</option>
                            <option value="cbt">CBT Lokal (Bank Soal)</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="small fw-bold">Durasi Ujian (Menit) <span class="text-danger">*</span></label>
                        <input type="number" name="durasi" class="form-control form-control-sm" placeholder="Contoh: 60" value="{{ $ujian->durasi ?? '' }}" required style="border-radius: 8px;">
                    </div>

                    <div class="col-md-12" id="kotak_link_gform_tambah">
                        <label class="small fw-bold">Link Google Form <span class="text-danger">*</span></label>
                        <input type="url" name="link_gform" id="input_link_gform_tambah" class="form-control form-control-sm" placeholder="https://docs.google.com/forms/..." style="border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" class="form-control form-control-sm" required style="border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control form-control-sm" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tableUjian').DataTable({
        "language": {
            "search": "Cari Guru / Mapel / Kelas:",
            "lengthMenu": "Tampilkan _MENU_ jadwal",
            "zeroRecords": "Jadwal ujian tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
            "infoEmpty": "Menampilkan 0 data",
            "paginate": { "next": "Berikutnya", "previous": "Sebelumnya" }
        },
        "pageLength": 15,
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ]
    });

    $('#checkAll').on('click', function() {
        var isChecked = this.checked;
        $('.check-item', table.rows({ filter: 'applied' }).nodes()).prop('checked', isChecked);
    });

    $('#mapel_select').on('change', function() {
        var mapel_id = $(this).val();
        var container = $('#container_kelas');

        if(mapel_id) {
            container.html('<div class="text-center text-muted small py-2"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar kelas...</div>');
            
            $.ajax({
                url: "{{ route('ujian.get_kelas') }}",
                type: "GET",
                data: { mapel_id: mapel_id },
                success: function(data) {
                    if(data.length > 0) {
                        var html = '<div class="row">';
                        $.each(data, function(key, kelas) {
                            html += '<div class="col-md-4 mb-2">';
                            html += '<div class="form-check">';
                            html += '<input class="form-check-input" type="checkbox" name="kelas_id[]" value="'+kelas.id+'" id="ujian_kelas_'+kelas.id+'" checked>';
                            html += '<label class="form-check-label fw-bold small" for="ujian_kelas_'+kelas.id+'">'+kelas.nama_kelas+'</label>';
                            html += '</div></div>';
                        });
                        html += '</div>';
                        container.html(html);
                    } else {
                        container.html('<div class="text-center text-danger small py-2">Tidak ada kelas untuk mapel ini. Pastikan Set Pembelajaran sudah diisi.</div>');
                    }
                },
                error: function() {
                    container.html('<div class="text-center text-danger small py-2">Gagal mengambil data kelas dari server.</div>');
                }
            });
        } else {
            container.html('<div class="text-center text-muted small py-2">Silakan pilih Mata Pelajaran terlebih dahulu.</div>');
        }
    });
});

$('#pilih_metode_tambah').on('change', function() {
    if ($(this).val() == 'gform') {
        $('#kotak_link_gform_tambah').slideDown();
        $('#input_link_gform_tambah').prop('required', true);
    } else {
        $('#kotak_link_gform_tambah').slideUp();
        $('#input_link_gform_tambah').prop('required', false).val('');
    }
});

$('.select-metode-edit').on('change', function() {
    var id = $(this).data('id');
    if ($(this).val() == 'gform') {
        $('.kotak-link-edit-' + id).slideDown();
        $('#input_link_gform_edit_' + id).prop('required', true);
    } else {
        $('.kotak-link-edit-' + id).slideUp();
        $('#input_link_gform_edit_' + id).prop('required', false);
    }
});
</script>
@endsection