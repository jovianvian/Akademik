@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12 xl:col-span-5">
            <h2 class="text-base font-semibold text-gray-900">Profil Mahasiswa</h2>
            @if($mahasiswa)
                <dl class="mt-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between gap-4"><dt>NIM</dt><dd class="font-medium">{{ $mahasiswa->nim }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Nama</dt><dd class="font-medium">{{ $mahasiswa->nama }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Angkatan</dt><dd class="font-medium">{{ $mahasiswa->angkatan }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Status Akademik</dt><dd class="font-medium uppercase">{{ $mahasiswa->status_akademik }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Catatan</dt><dd class="font-medium">{{ $mahasiswa->catatan_status ?? '-' }}</dd></div>
                </dl>
            @else
                <p class="mt-3 text-sm text-gray-500">Data profil mahasiswa belum tersedia.</p>
            @endif
        </article>

        <article class="card-panel col-span-12 xl:col-span-7">
            <h2 class="text-base font-semibold text-gray-900">Dosen PA Aktif</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Dosen</th>
                        <th class="px-4 py-3 text-left">NIDN</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($dosenPaAktif as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->nidn }}</td>
                            <td class="px-4 py-3">{{ $item->periode_mulai }} s/d {{ $item->periode_selesai ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-gray-500">Belum ada data dosen PA aktif.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Status Akademik</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Status Lama</th>
                        <th class="px-4 py-3 text-left">Status Baru</th>
                        <th class="px-4 py-3 text-left">Sumber</th>
                        <th class="px-4 py-3 text-left">Diubah Oleh</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($statusLogs as $log)
                        <tr>
                            <td class="px-4 py-3">{{ $log->created_at }}</td>
                            <td class="px-4 py-3">{{ strtoupper($log->status_lama ?? '-') }}</td>
                            <td class="px-4 py-3">{{ strtoupper($log->status_baru) }}</td>
                            <td class="px-4 py-3">{{ $log->sumber }}</td>
                            <td class="px-4 py-3">{{ $log->changed_by_name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-gray-500">Belum ada riwayat status akademik.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
