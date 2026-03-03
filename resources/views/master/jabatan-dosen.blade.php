@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Jabatan Dosen</h2>
            <form action="{{ route('master.jabatan-dosen.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <select name="dosen_id" class="input-text">
                    <option value="">Pilih dosen</option>
                    @foreach($dosen as $d)
                        <option value="{{ $d->id }}">{{ $d->nidn }} - {{ $d->nama }}</option>
                    @endforeach
                </select>
                <select name="jabatan" class="input-text">
                    <option value="">Pilih jabatan</option>
                    @foreach($jabatanOptions as $jabatan)
                        <option value="{{ $jabatan }}">{{ $jabatan }}</option>
                    @endforeach
                </select>
                <input type="date" name="periode_mulai" class="input-text">
                <input type="date" name="periode_selesai" class="input-text">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="status_aktif" value="1" checked>
                    Tetapkan sebagai jabatan aktif
                </label>
                @if($errors->any()) <p class="text-sm text-error-600">{{ $errors->first() }}</p> @endif
                <button class="btn-primary" type="submit">Simpan Jabatan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Daftar Jabatan Dosen</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Jabatan</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.jabatan-dosen.update', $item->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="dosen_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($dosen as $d)
                                            <option value="{{ $d->id }}" @selected($d->id === $item->dosen_id)>{{ $d->nidn }} - {{ $d->nama }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <select name="jabatan" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($jabatanOptions as $jabatan)
                                            <option value="{{ $jabatan }}" @selected($jabatan === $item->jabatan)>{{ $jabatan }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <input type="date" name="periode_mulai" value="{{ $item->periode_mulai }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        <input type="date" name="periode_selesai" value="{{ $item->periode_selesai }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                    </div>
                            </td>
                            <td class="px-4 py-3">
                                    <label class="flex items-center gap-2 text-xs">
                                        <input type="checkbox" name="status_aktif" value="1" @checked($item->status_aktif)>
                                        Aktif
                                    </label>
                            </td>
                            <td class="px-4 py-3">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Edit</button>
                                </form>
                                <form action="{{ route('master.jabatan-dosen.destroy', $item->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin soft delete jabatan dosen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs text-white bg-red-500 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada data jabatan dosen.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
