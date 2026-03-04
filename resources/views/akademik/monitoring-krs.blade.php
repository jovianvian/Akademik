@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Monitoring KRS</h2>
                    <p class="mt-1 text-sm text-gray-500">Pantau status pengisian KRS mahasiswa per periode.</p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <select name="tahun_akademik_id" class="input-select">
                        <option value="">Semua Tahun Akademik</option>
                        @foreach($tahunAkademikList as $ta)
                            <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>{{ $ta->tahun }} - {{ strtoupper($ta->semester) }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary">Filter</button>
                </form>
            </div>

            <div class="mt-5 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Tahun</th>
                        <th class="px-4 py-3 text-left">Status KRS</th>
                        <th class="px-4 py-3 text-left">SKS</th>
                        <th class="px-4 py-3 text-left">Total MK</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} / {{ strtoupper($item->semester) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($item->status_krs) }}</td>
                            <td class="px-4 py-3">{{ $item->total_sks }}</td>
                            <td class="px-4 py-3">{{ $item->total_mk }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-gray-500">Belum ada data KRS.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>
@endsection




