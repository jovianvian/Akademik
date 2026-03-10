@extends('layouts.app')

@section('content')
    <x-admin.page-layout
        title="Master Program Studi"
        description="Kelola program studi dan pemetaan fakultas."
        add-label="Tambah Prodi"
        add-target="prodiFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search prodi/fakultas..." data-live-search-target="#prodiTable">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="prodiTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 38%;">Program Studi</th>
                <th class="px-4 py-3" style="width: 42%;">Fakultas</th>
                <th class="px-4 py-3 table-action-col" style="width: 20%;">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->nama_prodi }}</td>
                    <td class="px-4 py-3">{{ $item->nama_fakultas }}</td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="table-actions justify-center">
                            <button
                                type="button"
                                class="action-btn action-btn-edit"
                                title="Edit"
                                data-modal-open="prodiFormModal"
                                data-form-title="Edit Program Studi"
                                data-form-action="{{ route('master.prodi.update', $item->id) }}"
                                data-form-method="PATCH"
                                data-form-submit="Simpan Perubahan"
                                data-form-values='{{ e(json_encode(["nama_prodi" => $item->nama_prodi, "fakultas_id" => (string) $item->fakultas_id])) }}'
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('master.prodi.destroy', $item->id) }}" method="POST" data-confirm-delete data-delete-label="program studi ini">
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
                    <td colspan="3" class="px-4 py-4 text-gray-500">Belum ada data program studi.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="prodiFormModal"
        title="Tambah Program Studi"
        action="{{ route('master.prodi.store') }}"
        method="POST"
        submit-label="Simpan"
        size="max-w-xl"
    >
        <input name="nama_prodi" class="input-text" placeholder="Nama prodi" value="{{ old('nama_prodi') }}" required>
        <select name="fakultas_id" class="input-text" required>
            <option value="">Pilih fakultas</option>
            @foreach($fakultas as $f)
                <option value="{{ $f->id }}" @selected((string) old('fakultas_id') === (string) $f->id)>{{ $f->nama_fakultas }}</option>
            @endforeach
        </select>
        @error('nama_prodi') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('fakultas_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'prodiFormModal')
                const modal = document.getElementById('prodiFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
