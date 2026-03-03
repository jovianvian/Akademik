@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Hak Akses Role</h2>
                    <p class="mt-1 text-sm text-gray-500">Kelola permission per level role dari halaman ini.</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-lg bg-brand-50 text-brand-600">
                    Super Admin Only
                </span>
            </div>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif

            <div class="grid grid-cols-1 gap-4 mt-4 xl:grid-cols-2">
                @foreach($roles as $role)
                    @php
                        $current = $matrix[$role->id] ?? [];
                    @endphp
                    <div class="perm-role-card">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-sm font-semibold text-gray-800">{{ strtoupper($role->role_name) }}</h3>
                            <span class="text-xs text-gray-500">{{ count($current) }} izin aktif</span>
                        </div>
                        <form method="POST" action="{{ route('super-admin.role-permissions.update', $role->id) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach($permissions as $permission)
                                    <label class="perm-check">
                                        <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $current, true))>
                                        <span>{{ $permission->nama }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <button class="btn-primary w-full justify-center" type="submit">Simpan Hak Akses</button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
