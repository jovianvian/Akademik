@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Recovery Data Master (Soft Delete)</h2>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif
            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left" style="width: 16%;">Tabel</th>
                        <th class="px-4 py-3 text-left" style="width: 31%;">Data</th>
                        <th class="px-4 py-3 text-left" style="width: 19%;">Deleted At</th>
                        <th class="px-4 py-3 text-left" style="width: 16%;">Deleted By</th>
                        <th class="px-4 py-3 text-left table-action-col" style="width: 18%;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($deletedRows as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->table_name }}</td>
                            <td class="px-4 py-3">{{ $row->label }}</td>
                            <td class="px-4 py-3">{{ $row->deleted_at }}</td>
                            <td class="px-4 py-3">{{ $row->deleted_by_name ?? '-' }}</td>
                            <td class="px-4 py-3 table-action-col">
                                <form method="POST" action="{{ route('super-admin.master-recovery.restore') }}">
                                    @csrf
                                    <input type="hidden" name="table_name" value="{{ $row->table_name }}">
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button class="btn-compact">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada data master yang terhapus.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Log Perubahan Master (Edit & Delete)</h2>
            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                        <th class="px-4 py-3 text-left">Modul</th>
                        <th class="px-4 py-3 text-left">Before</th>
                        <th class="px-4 py-3 text-left">After</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($editDeleteLogs as $log)
                        <tr>
                            <td class="px-4 py-3">{{ $log->created_at }}</td>
                            <td class="px-4 py-3">{{ $log->user_name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->aksi }}</td>
                            <td class="px-4 py-3">{{ $log->modul }}</td>
                            <td class="px-4 py-3">
                                <pre class="max-w-xl p-2 overflow-x-auto text-xs text-gray-700 bg-gray-50 rounded">{{ $log->before_data ?? '-' }}</pre>
                            </td>
                            <td class="px-4 py-3">
                                <pre class="max-w-xl p-2 overflow-x-auto text-xs text-gray-700 bg-gray-50 rounded">{{ $log->after_data ?? ($log->konteks ?? '-') }}</pre>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Belum ada log perubahan master.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection

