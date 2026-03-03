@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900">Kartu Hasil Studi</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('khs.export', ['tahun_akademik_id' => $selectedTahunAkademikId]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export CSV</a>
                    <a href="{{ route('khs.export-pdf', ['tahun_akademik_id' => $selectedTahunAkademikId]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export PDF</a>
                </div>
            </div>
            @if($mahasiswa)
                <p class="mt-1 text-sm text-gray-500">{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}</p>
            @endif

            <form method="GET" action="{{ route('khs.index') }}" class="flex items-center gap-2 mt-3">
                <select name="tahun_akademik_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                    <option value="">Semua periode</option>
                    @foreach($tahunAkademikList as $ta)
                        <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>
                            {{ $ta->tahun }} {{ ucfirst($ta->semester) }}
                        </option>
                    @endforeach
                </select>
                <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Filter</button>
            </form>

            <div class="mt-4 space-y-4">
                @forelse($khs as $semester)
                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 bg-gray-50">
                            <p class="text-sm font-semibold text-gray-800">{{ $semester['tahun'] }} {{ ucfirst($semester['semester']) }}</p>
                            <p class="text-xs text-gray-600">
                                Status: {{ strtoupper($semester['status_krs']) }} | Total SKS: {{ $semester['total_sks'] }} | IPS: {{ number_format($semester['ips'], 2) }}
                            </p>
                        </div>
                        <table class="w-full text-sm">
                            <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Mata Kuliah</th>
                                <th class="px-4 py-3 text-left">SKS</th>
                                <th class="px-4 py-3 text-left">Nilai Angka</th>
                                <th class="px-4 py-3 text-left">Nilai Huruf</th>
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
