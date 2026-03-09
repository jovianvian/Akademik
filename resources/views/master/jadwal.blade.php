@extends('layouts.app')

@section('content')
    <x-admin.page-layout
        title="Master Jadwal"
        description="Kelola jadwal perkuliahan aktif."
        add-label="+ Tambah Jadwal"
        add-target="jadwalFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search mata kuliah/dosen/ruang..." data-live-search-target="#jadwalTable">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="jadwalTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 23%;">Mata Kuliah</th>
                <th class="px-4 py-3" style="width: 18%;">Dosen</th>
                <th class="px-4 py-3" style="width: 20%;">Waktu</th>
                <th class="px-4 py-3" style="width: 12%;">Ruang</th>
                <th class="px-4 py-3" style="width: 15%;">Periode</th>
                <th class="px-4 py-3 table-action-col" style="width: 12%;">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }}</td>
                    <td class="px-4 py-3">{{ $item->nama_dosen }}</td>
                    <td class="px-4 py-3">{{ $item->hari }}, {{ substr($item->jam_mulai,0,5) }} - {{ substr($item->jam_selesai,0,5) }}</td>
                    <td class="px-4 py-3">{{ $item->ruangan }}</td>
                    <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="table-actions justify-center">
                            <button
                                type="button"
                                class="action-btn action-btn-edit"
                                title="Edit"
                                data-modal-open="jadwalFormModal"
                                data-form-title="Edit Jadwal"
                                data-form-action="{{ route('master.jadwal.update', $item->id) }}"
                                data-form-method="PATCH"
                                data-form-submit="Simpan Perubahan"
                                data-form-values='{{ e(json_encode([
                                    "mata_kuliah_id" => (string) $item->mata_kuliah_id,
                                    "dosen_id" => (string) $item->dosen_id,
                                    "hari" => $item->hari,
                                    "jam_mulai" => substr($item->jam_mulai, 0, 5),
                                    "jam_selesai" => substr($item->jam_selesai, 0, 5),
                                    "ruangan" => $item->ruangan,
                                    "tahun_akademik_id" => (string) $item->tahun_akademik_id
                                ])) }}'
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('master.jadwal.destroy', $item->id) }}" method="POST" data-confirm-delete data-delete-label="jadwal ini">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-gray-500">Belum ada data jadwal.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="jadwalFormModal"
        title="Tambah Jadwal"
        action="{{ route('master.jadwal.store') }}"
        method="POST"
        submit-label="Simpan"
        size="max-w-3xl"
    >
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <select name="mata_kuliah_id" class="input-text" required>
                <option value="">Pilih mata kuliah</option>
                @foreach($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" @selected((string) old('mata_kuliah_id') === (string) $mk->id)>{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                @endforeach
            </select>
            <select name="dosen_id" class="input-text" required>
                <option value="">Pilih dosen</option>
                @foreach($dosen as $d)
                    <option value="{{ $d->id }}" @selected((string) old('dosen_id') === (string) $d->id)>{{ $d->nama }}</option>
                @endforeach
            </select>
            <select name="hari" class="input-text" required>
                <option value="">Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                    <option value="{{ $hari }}" @selected(old('hari') === $hari)>{{ $hari }}</option>
                @endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input name="jam_mulai" type="time" class="input-text" value="{{ old('jam_mulai') }}" required>
                <input name="jam_selesai" type="time" class="input-text" value="{{ old('jam_selesai') }}" required>
            </div>
            <input name="ruangan" class="input-text" placeholder="Ruangan" value="{{ old('ruangan') }}" required>
            <select name="tahun_akademik_id" class="input-text" required>
                <option value="">Pilih tahun akademik</option>
                @foreach($tahunAkademik as $ta)
                    <option value="{{ $ta->id }}" @selected((string) old('tahun_akademik_id') === (string) $ta->id)>{{ $ta->tahun }} - {{ ucfirst($ta->semester) }}</option>
                @endforeach
            </select>
        </div>
        @if($errors->has('jadwal')) <p class="text-sm text-error-600">{{ $errors->first('jadwal') }}</p> @endif
        @error('mata_kuliah_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('dosen_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('hari') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('jam_mulai') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('jam_selesai') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('ruangan') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('tahun_akademik_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'jadwalFormModal')
                const modal = document.getElementById('jadwalFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
