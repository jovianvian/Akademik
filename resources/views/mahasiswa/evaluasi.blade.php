@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Evaluasi Dosen</h2>
            @if(session('success'))
                <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p>
            @endif
            @if($errors->any())
                <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p>
            @endif

            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Status Evaluasi</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $item->nama_dosen }}</td>
                            <td class="px-4 py-3">
                                @if($item->status_selesai)
                                    <span class="text-success-700">Selesai</span>
                                @else
                                    <span class="text-gray-500">Belum</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('mahasiswa.evaluasi.store') }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="krs_detail_id" value="{{ $item->krs_detail_id }}">
                                    <select name="status_selesai" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        <option value="0" @selected((int) $item->status_selesai === 0)>Belum</option>
                                        <option value="1" @selected((int) $item->status_selesai === 1)>Selesai</option>
                                    </select>
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-gray-500">Belum ada mata kuliah untuk dievaluasi.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
