@extends('layouts.app')

@section('content')
    <x-admin.page-layout
        title="Master Fakultas"
        description="Kelola data fakultas secara terpusat."
        add-label="Tambah Fakultas"
        add-target="fakultasFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search fakultas..." data-live-search-target="#fakultasTable">
                <form method="GET" action="{{ route('master.fakultas.index') }}" class="flex items-center gap-2">
                    <select name="sort_nama" class="input-select">
                        <option value="asc" @selected(($selectedSortNama ?? 'asc') === 'asc')>Nama A-Z</option>
                        <option value="desc" @selected(($selectedSortNama ?? 'asc') === 'desc')>Nama Z-A</option>
                    </select>
                    <button class="btn-compact" type="submit">Filter</button>
                </form>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="fakultasTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 70%;">Nama Fakultas</th>
                <th class="px-4 py-3 table-action-col" style="width: 30%;">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $item->nama_fakultas }}</td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="table-actions justify-center">
                            <button
                                type="button"
                                class="action-btn action-btn-edit"
                                title="Edit"
                                data-modal-open="fakultasFormModal"
                                data-form-title="Edit Fakultas"
                                data-form-action="{{ route('master.fakultas.update', $item->id) }}"
                                data-form-method="PATCH"
                                data-form-submit="Simpan Perubahan"
                                data-form-values='{{ e(json_encode(["nama_fakultas" => $item->nama_fakultas])) }}'
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('master.fakultas.destroy', $item->id) }}" method="POST" data-confirm-delete data-delete-label="fakultas ini">
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
                    <td colspan="2" class="px-4 py-4 text-gray-500">Belum ada data fakultas.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="fakultasFormModal"
        title="Tambah Fakultas"
        action="{{ route('master.fakultas.store') }}"
        method="POST"
        submit-label="Simpan"
        size="max-w-md"
    >
        <input name="nama_fakultas" class="input-text" placeholder="Nama fakultas" value="{{ old('nama_fakultas') }}" required>
        @error('nama_fakultas') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'fakultasFormModal')
                const modal = document.getElementById('fakultasFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
