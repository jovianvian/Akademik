@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900">Kelola Status Mahasiswa</h2>
                <form action="{{ route('akademik.mahasiswa-status.sync-ukt') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Sinkron dari Status UKT</button>
                </form>
            </div>
            @if($tahunAktif)
                <p class="mt-1 text-sm text-gray-500">Periode aktif: {{ $tahunAktif->tahun }} {{ ucfirst($tahunAktif->semester) }}</p>
            @endif
            @if(session('success')) <p class="mt-2 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-2 text-sm text-error-600">{{ $errors->first() }}</p> @endif

            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Status UKT Aktif</th>
                        <th class="px-4 py-3 text-left">Status Akademik</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                            <td class="px-4 py-3">{{ strtoupper($item->status_tagihan_aktif ?? 'tidak_ada') }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('akademik.mahasiswa-status.update', $item->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status_akademik" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($statusOptions as $status)
                                            <option value="{{ $status }}" @selected($item->status_akademik === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <input type="text" name="catatan_status" value="{{ $item->catatan_status }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg w-64">
                            </td>
                            <td class="px-4 py-3">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Perubahan Status Mahasiswa</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Perubahan</th>
                        <th class="px-4 py-3 text-left">Sumber</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                        <th class="px-4 py-3 text-left">Diubah Oleh</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($statusLogs as $log)
                        <tr>
                            <td class="px-4 py-3">{{ $log->created_at }}</td>
                            <td class="px-4 py-3">{{ $log->nim }} - {{ $log->nama }}</td>
                            <td class="px-4 py-3">{{ strtoupper($log->status_lama ?? '-') }} -> {{ strtoupper($log->status_baru) }}</td>
                            <td class="px-4 py-3">{{ $log->sumber }}</td>
                            <td class="px-4 py-3">{{ $log->catatan ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $log->changed_by_name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada riwayat perubahan status.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
