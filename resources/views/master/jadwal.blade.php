@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Jadwal</h2>
            <form action="{{ route('master.jadwal.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <select name="mata_kuliah_id" class="input-text">
                    <option value="">Pilih mata kuliah</option>
                    @foreach($mataKuliah as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                    @endforeach
                </select>
                <select name="dosen_id" class="input-text">
                    <option value="">Pilih dosen</option>
                    @foreach($dosen as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                    @endforeach
                </select>
                <div class="grid grid-cols-3 gap-3">
                    <select name="hari" class="input-text col-span-1">
                        <option value="">Hari</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                            <option value="{{ $hari }}">{{ $hari }}</option>
                        @endforeach
                    </select>
                    <input name="jam_mulai" type="time" class="input-text col-span-1">
                    <input name="jam_selesai" type="time" class="input-text col-span-1">
                </div>
                <input name="ruangan" class="input-text" placeholder="Ruangan">
                <select name="tahun_akademik_id" class="input-text">
                    <option value="">Pilih tahun akademik</option>
                    @foreach($tahunAkademik as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->tahun }} - {{ ucfirst($ta->semester) }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                @if($errors->any()) <p class="text-sm text-error-600">{{ $errors->first() }}</p> @endif
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Daftar Jadwal</h2>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">MK</th>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Ruang</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <form action="{{ route('master.jadwal.update', $item->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="mata_kuliah_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($mataKuliah as $mk)
                                            <option value="{{ $mk->id }}" @selected($mk->id === $item->mata_kuliah_id)>{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <select name="dosen_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($dosen as $d)
                                            <option value="{{ $d->id }}" @selected($d->id === $item->dosen_id)>{{ $d->nama }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <select name="hari" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                                <option value="{{ $hari }}" @selected($hari === $item->hari)>{{ $hari }}</option>
                                            @endforeach
                                        </select>
                                        <input name="jam_mulai" type="time" value="{{ substr($item->jam_mulai,0,5) }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        <input name="jam_selesai" type="time" value="{{ substr($item->jam_selesai,0,5) }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                    </div>
                            </td>
                            <td class="px-4 py-3">
                                    <input name="ruangan" value="{{ $item->ruangan }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg w-24">
                            </td>
                            <td class="px-4 py-3">
                                    <select name="tahun_akademik_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($tahunAkademik as $ta)
                                            <option value="{{ $ta->id }}" @selected($ta->id === $item->tahun_akademik_id)>{{ $ta->tahun }} {{ ucfirst($ta->semester) }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Edit</button>
                                </form>
                                <form action="{{ route('master.jadwal.destroy', $item->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin soft delete jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 text-xs text-white bg-red-500 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
