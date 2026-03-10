@extends('layouts.app')

@section('content')
    <x-admin.page-layout
        title="Master Jabatan Dosen"
        description="Kelola struktur jabatan dan periode aktif dosen."
        add-label="Tambah Jabatan Dosen"
        add-target="jabatanDosenFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search dosen/jabatan..." data-live-search-target="#jabatanDosenTable">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="jabatanDosenTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 24%;">Dosen</th>
                <th class="px-4 py-3" style="width: 20%;">Jabatan</th>
                <th class="px-4 py-3" style="width: 24%;">Periode</th>
                <th class="px-4 py-3" style="width: 12%;">Status</th>
                <th class="px-4 py-3 table-action-col" style="width: 20%;">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->nidn }} - {{ $item->nama_dosen }}</td>
                    <td class="px-4 py-3">{{ $item->jabatan }}</td>
                    <td class="px-4 py-3">{{ $item->periode_mulai }} s/d {{ $item->periode_selesai ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($item->status_aktif)
                            <span class="status-badge status-success">Aktif</span>
                        @else
                            <span class="status-badge status-muted">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="table-actions justify-center">
                            <button
                                type="button"
                                class="action-btn action-btn-edit"
                                title="Edit"
                                data-modal-open="jabatanDosenFormModal"
                                data-form-title="Edit Jabatan Dosen"
                                data-form-action="{{ route('master.jabatan-dosen.update', $item->id) }}"
                                data-form-method="PATCH"
                                data-form-submit="Simpan Perubahan"
                                data-form-values='{{ e(json_encode([
                                    "dosen_id" => (string) $item->dosen_id,
                                    "jabatan" => $item->jabatan,
                                    "periode_mulai" => $item->periode_mulai,
                                    "periode_selesai" => $item->periode_selesai,
                                    "status_aktif" => (bool) $item->status_aktif
                                ])) }}'
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('master.jabatan-dosen.destroy', $item->id) }}" method="POST" data-confirm-delete data-delete-label="jabatan dosen ini">
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
                    <td colspan="5" class="px-4 py-4 text-gray-500">Belum ada data jabatan dosen.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="jabatanDosenFormModal"
        title="Tambah Jabatan Dosen"
        action="{{ route('master.jabatan-dosen.store') }}"
        method="POST"
        submit-label="Simpan"
        size="max-w-2xl"
    >
        <select name="dosen_id" class="input-text" required>
            <option value="">Pilih dosen</option>
            @foreach($dosen as $d)
                <option value="{{ $d->id }}" @selected((string) old('dosen_id') === (string) $d->id)>{{ $d->nidn }} - {{ $d->nama }}</option>
            @endforeach
        </select>
        <select name="jabatan" class="input-text" required>
            <option value="">Pilih jabatan</option>
            @foreach($jabatanOptions as $jabatan)
                <option value="{{ $jabatan }}" @selected(old('jabatan') === $jabatan)>{{ $jabatan }}</option>
            @endforeach
        </select>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input type="date" name="periode_mulai" class="input-text" value="{{ old('periode_mulai') }}" required>
            <input type="date" name="periode_selesai" class="input-text" value="{{ old('periode_selesai') }}">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="status_aktif" value="1" @checked(old('status_aktif', '1') === '1' || old('status_aktif') === 1)>
            Tetapkan sebagai jabatan aktif
        </label>
        @if($errors->has('jabatan')) <p class="text-sm text-error-600">{{ $errors->first('jabatan') }}</p> @endif
        @error('dosen_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('jabatan') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('periode_mulai') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('periode_selesai') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'jabatanDosenFormModal')
                const modal = document.getElementById('jabatanDosenFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
