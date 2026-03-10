@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-gray-900">Kelola User dan Role</h2>
                <button type="button" class="btn-primary" data-modal-open="userFormModal">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span class="ml-2">Tambah User</span>
                </button>
            </div>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif
            <div class="filter-toolbar mt-3">
                <input type="text" class="input-select w-full md:w-80" placeholder="Cari nama/email/role..." data-live-search-target="#usersTable">
            </div>
            <div class="mt-4 table-wrap">
                @php
                    $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                @endphp
                <table class="table-base" id="usersTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3" style="width: 20%;"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => $nextDir])) }}">Nama</a></th>
                        <th class="px-4 py-3" style="width: 25%;"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_dir' => $nextDir])) }}">Email</a></th>
                        <th class="px-4 py-3" style="width: 15%;"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'role', 'sort_dir' => $nextDir])) }}">Role</a></th>
                        <th class="px-4 py-3" style="width: 15%;"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_dir' => $nextDir])) }}">Status</a></th>
                        <th class="px-4 py-3 table-action-col" style="width: 25%;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->name }}</td>
                            <td class="px-4 py-3">{{ $item->email }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('super-admin.users.update', $item->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role_id" class="input-select">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($item->role_name === $role->role_name)>{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <select name="status" class="input-select">
                                        <option value="aktif" @selected($item->status === 'aktif')>aktif</option>
                                        <option value="nonaktif" @selected($item->status === 'nonaktif')>nonaktif</option>
                                    </select>
                            </td>
                            <td class="px-4 py-3 table-action-col">
                                    <button class="btn-compact">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </article>
    </section>

    <x-admin.resource-form-modal
        id="userFormModal"
        title="Tambah User"
        action="{{ route('super-admin.users.store') }}"
        method="POST"
        submit-label="Simpan User"
        size="max-w-2xl"
    >
        <input
            type="text"
            name="name"
            class="input-text"
            placeholder="Nama lengkap"
            value="{{ old('name') }}"
            required
        >
        <input
            type="email"
            name="email"
            class="input-text"
            placeholder="Email"
            value="{{ old('email') }}"
            required
        >
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input type="password" name="password" class="input-text" placeholder="Password (min. 8 karakter)" required>
            <input type="password" name="password_confirmation" class="input-text" placeholder="Konfirmasi password" required>
        </div>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <select name="role_id" class="input-text" required>
                <option value="">Pilih role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>{{ $role->role_name }}</option>
                @endforeach
            </select>
            <select name="status" class="input-text" required>
                <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>aktif</option>
                <option value="nonaktif" @selected(old('status') === 'nonaktif')>nonaktif</option>
            </select>
        </div>
        @error('name') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('email') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('password') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('role_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('status') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'userFormModal')
                const modal = document.getElementById('userFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush

