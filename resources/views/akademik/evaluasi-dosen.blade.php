@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Rekap Evaluasi Dosen</h2>
                    <p class="mt-1 text-sm text-gray-500">Monitoring kualitas pengajaran berdasarkan evaluasi mahasiswa.</p>
                </div>
            </div>
            <div class="admin-toolbar mt-3">
                <input type="text" class="input-select w-full md:w-80" placeholder="Search dosen/mata kuliah/periode..." data-live-search-target="#evaluasiDosenTable">
                <form method="GET" action="{{ route('akademik.evaluasi-dosen.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="tahun_akademik_id" class="input-select">
                        <option value="">Semua Periode</option>
                        @foreach($tahunAkademikList as $ta)
                            <option value="{{ $ta->id }}" @selected((string) ($selectedTahunAkademikId ?? '') === (string) $ta->id)>{{ $ta->tahun }} {{ strtoupper($ta->semester) }}</option>
                        @endforeach
                    </select>
                    <select name="prodi_id" class="input-select">
                        <option value="">Semua Prodi</option>
                        @foreach($prodiList as $p)
                            <option value="{{ $p->id }}" @selected((string) ($selectedProdiId ?? '') === (string) $p->id)>{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                    <select name="dosen_id" class="input-select">
                        <option value="">Semua Dosen</option>
                        @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}" @selected((string) ($selectedDosenId ?? '') === (string) $dosen->id)>{{ $dosen->nama }}</option>
                        @endforeach
                    </select>
                    <button class="btn-compact" type="submit">Filter</button>
                </form>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </div>
            <div class="mt-5 table-wrap">
                <table class="table-base" id="evaluasiDosenTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Tahun</th>
                        <th class="px-4 py-3 text-left">Responden</th>
                        <th class="px-4 py-3 text-left">Rata-rata</th>
                        <th class="px-4 py-3 text-left">Komponen</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($summary as $item)
                        <tr>
                            <td class="px-4 py-3">{{ ($summary->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-4 py-3">{{ $item->nama_dosen }}</td>
                            <td class="px-4 py-3">{{ $item->mata_kuliah }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} / {{ strtoupper($item->semester) }}</td>
                            <td class="px-4 py-3">{{ $item->jumlah_responden }}</td>
                            <td class="px-4 py-3 font-semibold text-brand-700">{{ $item->rata_rata }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $item->avg_1 }} / {{ $item->avg_2 }} / {{ $item->avg_3 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-4 text-gray-500">Belum ada data evaluasi dosen.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $summary->links() }}</div>
        </article>
    </section>
@endsection
