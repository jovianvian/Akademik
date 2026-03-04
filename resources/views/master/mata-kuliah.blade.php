@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Mata Kuliah</h2>
            <form action="{{ route('master.mata-kuliah.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input name="kode_mk" class="input-text" placeholder="Kode MK">
                <input name="nama_mk" class="input-text" placeholder="Nama MK">
                <div class="grid grid-cols-2 gap-3">
                    <input name="sks" class="input-text" type="number" min="1" max="6" placeholder="SKS">
                    <input name="semester" class="input-text" type="number" min="1" max="14" placeholder="Semester">
                </div>
                <select name="prodi_id" class="input-text">
                    <option value="">Pilih prodi</option>
                    @foreach($prodi as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                @error('kode_mk') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                @error('nama_mk') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                @error('sks') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                @error('semester') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                @error('prodi_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Daftar Mata Kuliah</h2>
            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">SKS</th>
                        <th class="px-4 py-3 text-left">Prodi</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.mata-kuliah.update', $item->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="kode_mk" value="{{ $item->kode_mk }}" class="input-select w-28">
                            </td>
                            <td class="px-4 py-3">
                                    <input name="nama_mk" value="{{ $item->nama_mk }}" class="input-select w-full">
                            </td>
                            <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <input name="sks" type="number" min="1" max="6" value="{{ $item->sks }}" class="input-select w-16">
                                        <input name="semester" type="number" min="1" max="14" value="{{ $item->semester }}" class="input-select w-20" title="Semester">
                                    </div>
                            </td>
                            <td class="px-4 py-3">
                                    <select name="prodi_id" class="input-select">
                                        @foreach($prodi as $p)
                                            <option value="{{ $p->id }}" @selected($p->id === $item->prodi_id)>{{ $p->nama_prodi }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <button class="btn-compact">Edit</button>
                                </form>
                                <form action="{{ route('master.mata-kuliah.destroy', $item->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin soft delete mata kuliah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-compact-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection



