@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900">Input Nilai Mahasiswa</h2>
                <a href="{{ route('dosen.nilai.export') }}" class="btn-compact">Export CSV</a>
            </div>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif

            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Nilai Sekarang</th>
                        <th class="px-4 py-3 text-left">Input Nilai</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama_mahasiswa }}</td>
                            <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }}</td>
                            <td class="px-4 py-3">
                                @if($item->nilai_angka !== null)
                                    {{ number_format((float) $item->nilai_angka, 2) }} ({{ $item->nilai_huruf }})
                                @else
                                    <span class="text-gray-500">Belum diinput</span>
                                @endif
                                @if($item->nilai_terkunci)
                                    <p class="mt-1 text-xs text-error-600">Terkunci (KHS sudah digenerate)</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('dosen.nilai.store') }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="krs_detail_id" value="{{ $item->krs_detail_id }}">
                                    <input
                                        type="number"
                                        name="nilai_tugas"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="{{ $item->nilai_tugas }}"
                                        class="h-9 w-20 rounded-lg border border-gray-200 px-2"
                                        placeholder="Tugas"
                                        @disabled($item->nilai_terkunci)
                                    >
                                    <input
                                        type="number"
                                        name="nilai_uts"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="{{ $item->nilai_uts }}"
                                        class="h-9 w-20 rounded-lg border border-gray-200 px-2"
                                        placeholder="UTS"
                                        @disabled($item->nilai_terkunci)
                                    >
                                    <input
                                        type="number"
                                        name="nilai_uas"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="{{ $item->nilai_uas }}"
                                        class="h-9 w-20 rounded-lg border border-gray-200 px-2"
                                        placeholder="UAS"
                                        @disabled($item->nilai_terkunci)
                                    >
                                    <input
                                        type="number"
                                        name="nilai_kehadiran"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="{{ $item->nilai_kehadiran }}"
                                        class="h-9 w-24 rounded-lg border border-gray-200 px-2"
                                        placeholder="Kehadiran"
                                        @disabled($item->nilai_terkunci)
                                    >
                                    <button class="btn-compact disabled:opacity-50" @disabled($item->nilai_terkunci)>Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-3 text-gray-500">Belum ada data KRS yang bisa dinilai.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>
@endsection



