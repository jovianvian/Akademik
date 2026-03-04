@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900">Kartu Hasil Studi</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('khs.export', ['tahun_akademik_id' => $selectedTahunAkademikId]) }}" class="btn-compact">Export CSV</a>
                    <a href="{{ route('khs.export-pdf', ['tahun_akademik_id' => $selectedTahunAkademikId]) }}" class="btn-compact">Export PDF</a>
                </div>
            </div>
            @if($mahasiswa)
                <p class="mt-1 text-sm text-gray-500">{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}</p>
            @endif

            <form method="GET" action="{{ route('khs.index') }}" class="filter-toolbar mt-3">
                <select name="tahun_akademik_id" class="input-select">
                    <option value="">Semua periode</option>
                    @foreach($tahunAkademikList as $ta)
                        <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>
                            {{ $ta->tahun }} {{ ucfirst($ta->semester) }}
                        </option>
                    @endforeach
                </select>
                <button class="btn-compact">Filter</button>
            </form>

            <div class="mt-4 space-y-4">
                @forelse($khs as $semester)
                    <div class="table-wrap">
                        <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 bg-gray-50">
                            <p class="text-sm font-semibold text-gray-800">{{ $semester['tahun'] }} {{ ucfirst($semester['semester']) }}</p>
                            <p class="text-xs text-gray-600">
                                Status: {{ strtoupper($semester['status_krs']) }} | Total SKS: {{ $semester['total_sks'] }} | IPS: {{ number_format($semester['ips'], 2) }}
                            </p>
                        </div>
                        <table class="table-base">
                            <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Mata Kuliah</th>
                                <th class="px-4 py-3 text-left">SKS</th>
                                <th class="px-4 py-3 text-left">Nilai Angka</th>
                                <th class="px-4 py-3 text-left">Nilai Huruf</th>
                                <th class="px-4 py-3 text-left">Detail</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($semester['items'] as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->kode_mk }}</td>
                                    <td class="px-4 py-3">{{ $item->nama_mk }}</td>
                                    <td class="px-4 py-3">{{ $item->sks }}</td>
                                    <td class="px-4 py-3">{{ $item->nilai_angka !== null ? number_format((float) $item->nilai_angka, 2) : '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->nilai_huruf ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('nilai.detail', $item->krs_detail_id) }}" class="btn-primary">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @empty
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                        Belum ada data KHS.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
@endsection



