@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Generate / Finalisasi KHS</h2>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->has('khs')) <p class="mt-3 text-sm text-error-600">{{ $errors->first('khs') }}</p> @endif
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Status KRS</th>
                        <th class="px-4 py-3 text-left">Lock Nilai</th>
                        <th class="px-4 py-3 text-left">Nilai Terisi</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($item->status_krs) }}</td>
                            <td class="px-4 py-3">
                                @if($item->nilai_terkunci)
                                    <span class="text-success-700">Terkunci</span>
                                @else
                                    <span class="text-gray-500">Belum</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $item->total_nilai }}/{{ $item->total_mk }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('akademik.generate-khs.finalize', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs text-white rounded bg-brand-500 disabled:opacity-50" @disabled($item->nilai_terkunci)>
                                        Generate KHS
                                    </button>
                                </form>
                                @if($item->nilai_terkunci)
                                    <form method="POST" action="{{ route('akademik.generate-khs.unlock-nilai', $item->id) }}" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs text-white bg-orange-500 rounded">Override Unlock</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada data KRS.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
