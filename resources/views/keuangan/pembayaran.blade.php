@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Input Pembayaran</h2>
            <form action="{{ route('keuangan.pembayaran.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <select name="tagihan_id" class="input-text">
                    <option value="">Pilih tagihan menunggu</option>
                    @foreach($tagihanMenunggu as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->nim }} - {{ $t->nama }} ({{ $t->tahun }} {{ ucfirst($t->semester) }}) Rp{{ number_format($t->jumlah,0,',','.') }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="tanggal_bayar" class="input-text" value="{{ date('Y-m-d') }}">
                <input type="number" name="jumlah_bayar" class="input-text" min="1" step="0.01" placeholder="Jumlah bayar">
                <select name="metode_bayar" class="input-text">
                    @foreach(['transfer', 'cash', 'va', 'qris'] as $metode)
                        <option value="{{ $metode }}">{{ strtoupper($metode) }}</option>
                    @endforeach
                </select>
                <input type="text" name="bukti_bayar" class="input-text" placeholder="Bukti bayar (opsional / path / no. ref)">
                @if($errors->any()) <p class="text-sm text-error-600">{{ $errors->first() }}</p> @endif
                <button class="btn-primary" type="submit">Simpan Pembayaran</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900">Riwayat Pembayaran</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.pembayaran.export', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'sort_by' => $sortBy, 'sort_dir' => $sortDir]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export CSV</a>
                    <a href="{{ route('keuangan.pembayaran.export-pdf', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="px-3 py-1 text-xs text-white rounded bg-brand-500">Export PDF</a>
                </div>
            </div>
            <form method="GET" action="{{ route('keuangan.pembayaran.index') }}" class="flex flex-wrap items-center gap-2 mt-3">
                <select name="tahun_akademik_id" class="h-9 px-2 text-xs border border-gray-200 rounded-lg">
                    <option value="">Semua periode</option>
                    @foreach($tahunAkademikList as $ta)
                        <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>
                            {{ $ta->tahun }} {{ ucfirst($ta->semester) }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date_from" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" value="{{ $dateFrom }}">
                <input type="date" name="date_to" class="h-9 px-2 text-xs border border-gray-200 rounded-lg" value="{{ $dateTo }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                <button class="px-3 py-1 text-xs text-white rounded bg-brand-500">Filter</button>
            </form>
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                @php
                    $nextDir = $sortDir === 'asc' ? 'desc' : 'asc';
                @endphp
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'tanggal_bayar', 'sort_dir' => $nextDir])) }}">Tanggal</a>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'nim', 'sort_dir' => $nextDir])) }}">Mahasiswa</a>
                        </th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">
                            <a href="{{ route('keuangan.pembayaran.index', array_merge(request()->query(), ['sort_by' => 'jumlah_bayar', 'sort_dir' => $nextDir])) }}">Jumlah</a>
                        </th>
                        <th class="px-4 py-3 text-left">Tagihan</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-left">Bukti</th>
                        <th class="px-4 py-3 text-left">Rekonsiliasi</th>
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
                                    <span class="text-error-600">ERROR</span>
                                @else
                                    <span class="text-success-700">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-3 text-gray-500">Belum ada pembayaran.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </article>
    </section>
@endsection
