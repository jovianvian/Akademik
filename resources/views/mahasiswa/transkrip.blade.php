@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Transkrip Akademik</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="px-4 py-3 text-center border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500">Total SKS</p>
                        <p class="text-lg font-bold text-gray-900">{{ $totalSks }}</p>
                    </div>
                    <div class="px-4 py-3 text-center border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500">IPK</p>
                        <p class="text-lg font-bold text-brand-700">{{ number_format((float) $ipk, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">SKS</th>
                        <th class="px-4 py-3 text-left">Nilai</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->tahun }} / {{ strtoupper($row->semester) }}</td>
                            <td class="px-4 py-3">{{ $row->kode_mk }} - {{ $row->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $row->sks }}</td>
                            <td class="px-4 py-3">{{ $row->nilai_huruf ? ($row->nilai_huruf.' ('.number_format((float) $row->nilai_angka, 2).')') : '-' }}</td>
                            <td class="px-4 py-3">{{ in_array($row->nilai_huruf, ['A','A-','B+','B','B-','C+','C'], true) ? 'LULUS' : 'MENGULANG' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-4 text-gray-500">Belum ada data transkrip.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection


