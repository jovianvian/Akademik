@extends('layouts.app')

@section('content')
    @php
        $statusLabel = fn (string $status) => match ($status) {
            'open' => 'Menunggu',
            'partial' => 'Sebagian Dibayar',
            'paid' => 'Lunas',
            'disputed' => 'Sengketa',
            'void' => 'Dibatalkan',
            default => strtoupper($status),
        };
    @endphp

    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="col-span-12 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Menunggu / Sebagian Dibayar</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ ($monitoring['open'] ?? 0) + ($monitoring['partial'] ?? 0) }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Lunas</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $monitoring['paid'] ?? 0 }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Sengketa / Dibatalkan</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ ($monitoring['disputed'] ?? 0) + ($monitoring['void'] ?? 0) }}</p>
            </div>
        </article>
    </section>

    <x-admin.page-layout
        title="Tagihan UKT"
        description="Kelola tagihan mahasiswa dan status penagihan."
        add-label="Tambah Tagihan UKT"
        add-target="tagihanFormModal"
    >
        <x-slot:toolbar>
            <x-admin.toolbar-filter>
                <input type="text" class="input-select w-full md:w-80" placeholder="Search mahasiswa/periode..." data-live-search-target="#tagihanTable">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary" disabled>Export CSV</button>
                    <button type="button" class="btn-secondary" disabled>Export Excel</button>
                </div>
            </x-admin.toolbar-filter>
        </x-slot:toolbar>

        <x-admin.data-table id="tagihanTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 24%;">Mahasiswa</th>
                <th class="px-4 py-3" style="width: 16%;">Periode</th>
                <th class="px-4 py-3" style="width: 14%;">Tagihan</th>
                <th class="px-4 py-3" style="width: 14%;">Terbayar</th>
                <th class="px-4 py-3" style="width: 12%;">Status</th>
                <th class="px-4 py-3 table-action-col" style="width: 20%;">Aksi Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                    <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                    <td class="px-4 py-3">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">Rp{{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusClass = match($item->status) {
                                'paid' => 'status-success',
                                'partial' => 'status-warning',
                                'disputed' => 'status-danger',
                                'void' => 'status-muted',
                                default => 'status-warning',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel($item->status) }}</span>
                    </td>
                    <td class="px-4 py-3 table-action-col">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            @if($item->status !== 'disputed')
                                <form action="{{ route('keuangan.tagihan.status', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="disputed">
                                    <button class="btn-warning" type="submit">Sengketa</button>
                                </form>
                            @endif
                            @if($item->status !== 'void')
                                <form action="{{ route('keuangan.tagihan.status', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="void">
                                    <button class="btn-compact-danger" type="submit">Batalkan</button>
                                </form>
                            @endif
                            @if(in_array($item->status, ['disputed', 'void'], true))
                                <form action="{{ route('keuangan.tagihan.status', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="open">
                                    <button class="btn-secondary" type="submit">Set Menunggu</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-gray-500">Belum ada data tagihan.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="tagihanFormModal"
        title="Tambah Tagihan UKT"
        action="{{ route('keuangan.tagihan.store') }}"
        method="POST"
        submit-label="Simpan Tagihan"
        size="max-w-2xl"
    >
        <select name="mahasiswa_id" class="input-text" required>
            <option value="">Pilih mahasiswa</option>
            @foreach($mahasiswa as $m)
                <option value="{{ $m->id }}" @selected((string) old('mahasiswa_id') === (string) $m->id)>{{ $m->nim }} - {{ $m->nama }}</option>
            @endforeach
        </select>
        <select name="tahun_akademik_id" class="input-text" required>
            <option value="">Pilih tahun akademik</option>
            @foreach($tahunAkademik as $ta)
                <option value="{{ $ta->id }}" @selected((string) old('tahun_akademik_id') === (string) $ta->id)>{{ $ta->tahun }} - {{ ucfirst($ta->semester) }}</option>
            @endforeach
        </select>
        <input type="number" name="jumlah" class="input-text" placeholder="Jumlah UKT" min="1" step="0.01" value="{{ old('jumlah') }}" required>
        @error('mahasiswa_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('tahun_akademik_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('jumlah') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'tagihanFormModal')
                const modal = document.getElementById('tagihanFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
