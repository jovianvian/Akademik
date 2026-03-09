@extends('layouts.app')

@section('content')
    <x-admin.page-layout
        title="Master Mata Kuliah"
        description="Kelola katalog mata kuliah setiap program studi."
        add-label="+ Tambah Mata Kuliah"
        add-target="mataKuliahFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search kode/nama/prodi..." data-live-search-target="#mataKuliahTable">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="mataKuliahTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 13%;">Kode</th>
                <th class="px-4 py-3" style="width: 25%;">Mata Kuliah</th>
                <th class="px-4 py-3" style="width: 12%;">SKS</th>
                <th class="px-4 py-3" style="width: 12%;">Semester</th>
                <th class="px-4 py-3" style="width: 23%;">Prodi</th>
                <th class="px-4 py-3 table-action-col" style="width: 15%;">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $item->kode_mk }}</td>
                    <td class="px-4 py-3">{{ $item->nama_mk }}</td>
                    <td class="px-4 py-3">{{ $item->sks }}</td>
                    <td class="px-4 py-3">{{ $item->semester }}</td>
                    <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="table-actions justify-center">
                            <button
                                type="button"
                                class="action-btn action-btn-edit"
                                title="Edit"
                                data-modal-open="mataKuliahFormModal"
                                data-form-title="Edit Mata Kuliah"
                                data-form-action="{{ route('master.mata-kuliah.update', $item->id) }}"
                                data-form-method="PATCH"
                                data-form-submit="Simpan Perubahan"
                                data-form-values='{{ e(json_encode([
                                    "kode_mk" => $item->kode_mk,
                                    "nama_mk" => $item->nama_mk,
                                    "sks" => (string) $item->sks,
                                    "semester" => (string) $item->semester,
                                    "prodi_id" => (string) $item->prodi_id
                                ])) }}'
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('master.mata-kuliah.destroy', $item->id) }}" method="POST" data-confirm-delete data-delete-label="mata kuliah ini">
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
                    <td colspan="6" class="px-4 py-4 text-gray-500">Belum ada data mata kuliah.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="mataKuliahFormModal"
        title="Tambah Mata Kuliah"
        action="{{ route('master.mata-kuliah.store') }}"
        method="POST"
        submit-label="Simpan"
        size="max-w-2xl"
    >
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input name="kode_mk" class="input-text" placeholder="Kode MK" value="{{ old('kode_mk') }}" required>
            <input name="nama_mk" class="input-text" placeholder="Nama MK" value="{{ old('nama_mk') }}" required>
            <input name="sks" class="input-text" type="number" min="1" max="6" placeholder="SKS" value="{{ old('sks') }}" required>
            <input name="semester" class="input-text" type="number" min="1" max="14" placeholder="Semester" value="{{ old('semester') }}" required>
            <div class="md:col-span-2">
                <select name="prodi_id" class="input-text w-full" required>
                    <option value="">Pilih prodi</option>
                    @foreach($prodi as $p)
                        <option value="{{ $p->id }}" @selected((string) old('prodi_id') === (string) $p->id)>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('kode_mk') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('nama_mk') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('sks') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('semester') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('prodi_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'mataKuliahFormModal')
                const modal = document.getElementById('mataKuliahFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
