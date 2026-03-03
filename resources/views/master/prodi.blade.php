@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Program Studi</h2>
            <form action="{{ route('master.prodi.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input name="nama_prodi" class="input-text" placeholder="Nama prodi">
                <select name="fakultas_id" class="input-text">
                    <option value="">Pilih fakultas</option>
                    @foreach($fakultas as $f)
                        <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                    @endforeach
                </select>
                @error('nama_prodi') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                @error('fakultas_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Daftar Program Studi</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Fakultas</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.prodi.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="nama_prodi" value="{{ $item->nama_prodi }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                            </td>
                            <td class="px-4 py-3">
                                    <select name="fakultas_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($fakultas as $f)
                                            <option value="{{ $f->id }}" @selected($f->id === $item->fakultas_id)>{{ $f->nama_fakultas }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Edit</button>
                                </form>
                                <form action="{{ route('master.prodi.destroy', $item->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin soft delete prodi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs text-white bg-red-500 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
