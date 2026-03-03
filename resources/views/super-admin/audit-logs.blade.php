@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-gray-900">Audit Log Sistem</h2>
                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('super-admin.audit-logs.index') }}" class="flex flex-wrap items-center gap-2">
                        <select name="modul" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                            <option value="">Semua modul</option>
                            @foreach($modulList as $modul)
                                <option value="{{ $modul }}" @selected($selectedModul === $modul)>{{ $modul }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" value="{{ $dateFrom }}">
                        <input type="date" name="date_to" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" value="{{ $dateTo }}">
                        <input type="text" name="q" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" value="{{ $q }}" placeholder="Cari aksi/user">
                        <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                        <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                        <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Filter</button>
                    </form>
                    <a href="{{ route('super-admin.audit-logs.export', ['modul' => $selectedModul, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'q' => $q, 'sort_by' => $sortBy, 'sort_dir' => $sortDir]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export CSV</a>
                    <a href="{{ route('super-admin.audit-logs.export-pdf', ['modul' => $selectedModul, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'q' => $q, 'sort_by' => $sortBy, 'sort_dir' => $sortDir]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export PDF</a>
                </div>
            </div>

            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                @php
                    $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                @endphp
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.audit-logs.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => $nextDir])) }}">Waktu</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.audit-logs.index', array_merge(request()->query(), ['sort_by' => 'user', 'sort_dir' => $nextDir])) }}">User</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.audit-logs.index', array_merge(request()->query(), ['sort_by' => 'modul', 'sort_dir' => $nextDir])) }}">Modul</a></th>
                        <th class="px-4 py-3 text-left"><a href="{{ route('super-admin.audit-logs.index', array_merge(request()->query(), ['sort_by' => 'aksi', 'sort_dir' => $nextDir])) }}">Aksi</a></th>
                        <th class="px-4 py-3 text-left">Entity</th>
                        <th class="px-4 py-3 text-left">Konteks</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->created_at }}</td>
                            <td class="px-4 py-3">{{ $item->user_name ?? '-' }}<br><span class="text-xs text-gray-500">{{ $item->user_email ?? 'guest/system' }}</span></td>
                            <td class="px-4 py-3">{{ $item->modul }}</td>
                            <td class="px-4 py-3">{{ $item->aksi }}</td>
                            <td class="px-4 py-3">{{ $item->entity_type ?? '-' }} #{{ $item->entity_id ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $item->konteks }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada audit log.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </article>
    </section>
@endsection
