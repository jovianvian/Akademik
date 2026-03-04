@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-6 xl:col-start-4">
            <h2 class="text-base font-semibold text-gray-900">Profil Saya</h2>
            <div class="mt-4 space-y-3 text-sm text-gray-700">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Nama</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ auth()->user()->email }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Role</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ config('permissions.roles.'.auth()->user()->role?->role_name.'.label', auth()->user()->role?->role_name ?? '-') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Status Akun</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ strtoupper(auth()->user()->status ?? '-') }}</p>
                </div>
            </div>
        </article>
    </section>
@endsection
