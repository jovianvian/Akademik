@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Rekap Nilai Mahasiswa</h2>
                    <p class="mt-1 text-sm text-gray-500">Lihat nilai per mahasiswa, mata kuliah, dan dosen pengampu.</p>
                </div>
            </div>
            <div class="admin-toolbar mt-3">
                <input type="text" class="input-select w-full md:w-80" placeholder="Search mahasiswa/matkul/dosen..." data-live-search-target="#nilaiMahasiswaTable">
                <form method="GET" action="{{ route('akademik.nilai-mahasiswa.index') }}" class="flex flex-wrap items-center gap-2">
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
                    <select name="status_krs" class="input-select">
                        <option value="">Semua Status KRS</option>
                        <option value="draft" @selected(($selectedStatusKrs ?? '') === 'draft')>DRAFT</option>
                        <option value="final" @selected(($selectedStatusKrs ?? '') === 'final')>FINAL</option>
                    </select>
                    <button class="btn-compact" type="submit">Filter</button>
                </form>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </div>
            <div class="mt-5 table-wrap">
                <table class="table-base" id="nilaiMahasiswaTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Nilai</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ ($items->firstItem() ?? 1) + $loop->index }}</td>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama_mahasiswa }}</td>
                            <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                            <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $item->nama_dosen }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} / {{ strtoupper($item->semester) }}</td>
                            <td class="px-4 py-3">{{ $item->nilai_huruf ? ($item->nilai_huruf.' ('.number_format((float) $item->nilai_angka, 2).')') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-4 text-gray-500">Belum ada data nilai.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>
@endsection
