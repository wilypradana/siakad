@extends('layouts.admin')

@section('title', 'Siswa Kelas ' . $kelas->nama_kelas)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-secondary mb-2"><i class="fas fa-arrow-left"></i> Kembali</a>
    <h2>Daftar Siswa: {{ $kelas->nama_kelas }}</h2>
    <p class="text-muted">Jurusan: {{ $kelas->jurusan }} | Total: {{ $data_siswa->count() }} Siswa</p>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data_siswa as $siswa)
                <tr>
                    <td><img src="{{ asset('storage/siswa/'.$siswa->foto) }}" width="40" class="rounded-circle"></td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td><span class="badge bg-success">{{ $siswa->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada siswa di kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection