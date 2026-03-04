@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Detail Nilai Mata Kuliah</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}</p>
                </div>
                <a href="{{ route('khs.index') }}" class="btn-primary h-10 px-4">Kembali ke KHS</a>
            </div>

            <div class="grid grid-cols-1 gap-3 mt-5 md:grid-cols-2">
                <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500">Mata Kuliah</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->kode_mk }} - {{ $item->nama_mk }}</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500">Dosen / Periode</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->nama_dosen }} | {{ $item->tahun }} {{ strtoupper($item->semester) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-5 md:grid-cols-4">
                <div class="p-4 text-center border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-500">Tugas</p>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ $item->nilai_tugas ?? '-' }}</p>
                </div>
                <div class="p-4 text-center border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-500">UTS</p>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ $item->nilai_uts ?? '-' }}</p>
                </div>
                <div class="p-4 text-center border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-500">UAS</p>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ $item->nilai_uas ?? '-' }}</p>
                </div>
                <div class="p-4 text-center border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-500">Kehadiran</p>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ $item->nilai_kehadiran ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 mt-5 md:grid-cols-2">
                <div class="p-4 border border-gray-200 rounded-xl bg-success-50">
                    <p class="text-xs text-success-700">Nilai Akhir Angka</p>
                    <p class="text-xl font-bold text-success-700">{{ $item->nilai_angka !== null ? number_format((float) $item->nilai_angka, 2) : '-' }}</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-xl bg-blue-light-25">
                    <p class="text-xs text-gray-600">Nilai Huruf</p>
                    <p class="text-xl font-bold text-gray-900">{{ $item->nilai_huruf ?? '-' }}</p>
                </div>
            </div>
        </article>
    </section>
@endsection

