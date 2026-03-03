@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Kelola User dan Role</h2>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif
            <form method="GET" action="{{ route('super-admin.users.index') }}" class="flex flex-wrap items-center gap-2 mt-3">
                <input type="text" name="q" value="{{ $q }}" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" placeholder="Cari nama/email/role">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Cari</button>
            </form>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                @php
                    $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                @endphp
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => $nextDir])) }}">Nama</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_dir' => $nextDir])) }}">Email</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'role', 'sort_dir' => $nextDir])) }}">Role</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.users.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_dir' => $nextDir])) }}">Status</a></th>
                        <th class="px-4 py-3 text-left">Aksi</th>
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
                                    <select name="role_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($item->role_name === $role->role_name)>{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <select name="status" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                                        <option value="aktif" @selected($item->status === 'aktif')>aktif</option>
                                        <option value="nonaktif" @selected($item->status === 'nonaktif')>nonaktif</option>
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Update</button>
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
@endsection
