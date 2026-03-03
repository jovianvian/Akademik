@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Jadwal Mengajar</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Hari/Jam</th>
                        <th class="px-4 py-3 text-left">Ruangan</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }} ({{ $item->sks }} SKS)</td>
                            <td class="px-4 py-3">{{ $item->hari }}, {{ substr($item->jam_mulai, 0, 5) }}-{{ substr($item->jam_selesai, 0, 5) }}</td>
                            <td class="px-4 py-3">{{ $item->ruangan }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-3 text-gray-500">Belum ada jadwal mengajar.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection

