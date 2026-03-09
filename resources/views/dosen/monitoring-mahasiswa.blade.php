@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Monitoring Mahasiswa</h2>
            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Status KRS</th>
                        <th class="px-4 py-3 text-left">MK Diambil</th>
                        <th class="px-4 py-3 text-left">Nilai Terisi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }} ({{ $item->angkatan }})</td>
                            <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($item->status_krs) }}</td>
                            <td class="px-4 py-3">{{ $item->jumlah_mk }}</td>
                            <td class="px-4 py-3">{{ $item->jumlah_nilai }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada data mahasiswa untuk dimonitor.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>
@endsection



