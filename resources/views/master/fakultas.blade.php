@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Fakultas</h2>
            <form action="{{ route('master.fakultas.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input name="nama_fakultas" class="input-text" placeholder="Nama fakultas">
                @error('nama_fakultas') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Daftar Fakultas</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Fakultas</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.fakultas.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="nama_fakultas" value="{{ $item->nama_fakultas }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Edit</button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.fakultas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin soft delete fakultas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs text-white bg-red-500 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-3 text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
