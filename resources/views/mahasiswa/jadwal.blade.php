@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Jadwal Mingguan</h2>
            <p class="mt-1 text-sm text-gray-500">
                Tahun Akademik Aktif: {{ $tahunAktif ? $tahunAktif->tahun.' '.ucfirst($tahunAktif->semester) : '-' }}
            </p>

            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Hari</th>
                        <th class="px-4 py-3 text-left">Jam</th>
                        <th class="px-4 py-3 text-left">Kode MK</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Ruangan</th>
                        <th class="px-4 py-3 text-left">SKS</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->hari }}</td>
                            <td class="px-4 py-3">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</td>
                            <td class="px-4 py-3">{{ $item->kode_mk }}</td>
                            <td class="px-4 py-3">{{ $item->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $item->ruangan }}</td>
                            <td class="px-4 py-3">{{ $item->sks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-gray-500">Belum ada jadwal kuliah untuk mahasiswa ini.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection

