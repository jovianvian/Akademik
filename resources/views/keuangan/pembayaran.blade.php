@extends('layouts.app')

@section('content')
    @php
        $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
    @endphp

    <x-admin.page-layout
        title="Validasi Pembayaran"
        description="Input dan validasi transaksi pembayaran UKT."
        add-label="+ Tambah Validasi Pembayaran"
        add-target="pembayaranFormModal"
    >
        <x-slot:toolbar>
            <form method="GET" action="{{ route('keuangan.pembayaran.index') }}" class="admin-toolbar">
                <input type="text" class="input-select w-full md:w-72" placeholder="Search mahasiswa/periode..." data-live-search-target="#pembayaranTable">
                <select name="tahun_akademik_id" class="input-select">
                    <option value="">Semua periode</option>
                    @foreach($tahunAkademikList as $ta)
                        <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>
                            {{ $ta->tahun }} {{ ucfirst($ta->semester) }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date_from" class="input-select" value="{{ $dateFrom }}">
                <input type="date" name="date_to" class="input-select" value="{{ $dateTo }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                <button class="btn-compact" type="submit">Filter</button>
                <a href="{{ route('keuangan.pembayaran.export', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'sort_by' => $sortBy, 'sort_dir' => $sortDir]) }}" class="btn-secondary">Export CSV</a>
                <a href="{{ route('keuangan.pembayaran.export', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'sort_by' => $sortBy, 'sort_dir' => $sortDir]) }}" class="btn-secondary">Export Excel</a>
            </form>
        </x-slot:toolbar>

        <x-admin.data-table id="pembayaranTable">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3" style="width: 11%;">
                    <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'tanggal_bayar', 'sort_dir' => $nextDir])) }}">Tanggal</a>
                </th>
                <th class="px-4 py-3" style="width: 20%;">
                    <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'nim', 'sort_dir' => $nextDir])) }}">Mahasiswa</a>
                </th>
                <th class="px-4 py-3" style="width: 12%;">Periode</th>
                <th class="px-4 py-3" style="width: 12%;">
                    <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'jumlah_bayar', 'sort_dir' => $nextDir])) }}">Jumlah</a>
                </th>
                <th class="px-4 py-3" style="width: 12%;">Tagihan</th>
                <th class="px-4 py-3" style="width: 10%;">Metode</th>
                <th class="px-4 py-3" style="width: 13%;">Bukti</th>
                <th class="px-4 py-3" style="width: 10%;">Rekonsiliasi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->tanggal_bayar }}</td>
                    <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                    <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                    <td class="px-4 py-3">Rp{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">Rp{{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ strtoupper($item->metode_bayar) }}</td>
                    <td class="px-4 py-3">{{ $item->bukti_bayar ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($item->is_reconciliation_error)
                            <span class="status-badge status-danger">ERROR</span>
                        @else
                            <span class="status-badge status-success">OK</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-gray-500">Belum ada pembayaran.</td>
                </tr>
            @endforelse
            </tbody>
        </x-admin.data-table>
        <div class="mt-4">{{ $items->links() }}</div>
    </x-admin.page-layout>

    <x-admin.resource-form-modal
        id="pembayaranFormModal"
        title="Tambah Pembayaran"
        action="{{ route('keuangan.pembayaran.store') }}"
        method="POST"
        submit-label="Simpan Pembayaran"
        size="max-w-2xl"
    >
        <select name="tagihan_id" class="input-text" required>
            <option value="">Pilih tagihan menunggu/sebagian dibayar</option>
            @foreach($tagihanMenunggu as $t)
                <option value="{{ $t->id }}" @selected((string) old('tagihan_id') === (string) $t->id)>
                    {{ $t->nim }} - {{ $t->nama }} ({{ $t->tahun }} {{ ucfirst($t->semester) }}) Rp{{ number_format($t->jumlah,0,',','.') }}
                </option>
            @endforeach
        </select>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <input type="date" name="tanggal_bayar" class="input-text" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
            <input type="number" name="jumlah_bayar" class="input-text" min="1" step="0.01" placeholder="Jumlah bayar" value="{{ old('jumlah_bayar') }}" required>
        </div>
        <select name="metode_bayar" class="input-text" required>
            @foreach(['transfer', 'cash', 'va', 'qris'] as $metode)
                <option value="{{ $metode }}" @selected(old('metode_bayar', 'transfer') === $metode)>{{ strtoupper($metode) }}</option>
            @endforeach
        </select>
        <input type="text" name="bukti_bayar" class="input-text" placeholder="Bukti bayar (opsional / path / no. ref)" value="{{ old('bukti_bayar') }}">
        @error('tagihan_id') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('tanggal_bayar') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('jumlah_bayar') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('metode_bayar') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
        @error('bukti_bayar') <p class="text-sm text-error-600">{{ $message }}</p> @enderror
    </x-admin.resource-form-modal>
@endsection

@push('scripts')
    <script>
        (function () {
            @if($errors->any() && old('_modal') === 'pembayaranFormModal')
                const modal = document.getElementById('pembayaranFormModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                }
            @endif
        })();
    </script>
@endpush
