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
        <article class="card-panel col-span-12">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Laporan Keuangan</h2>
                    <p class="mt-1 text-sm text-gray-500">Rekap total tagihan, pembayaran, dan sisa per periode.</p>
                </div>
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="tahun_akademik_id" class="input-select">
                        <option value="">Semua Tahun Akademik</option>
                        @foreach($tahunAkademikList as $ta)
                            <option value="{{ $ta->id }}" @selected((string) $selectedTahunAkademikId === (string) $ta->id)>
                                {{ $ta->tahun }} - {{ strtoupper($ta->semester) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" class="input-select" value="{{ $dateFrom }}">
                    <input type="date" name="date_to" class="input-select" value="{{ $dateTo }}">
                    <button class="btn-primary">Filter</button>
                    <a href="{{ route('keuangan.laporan.export', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn-secondary">Export CSV</a>
                    <a href="{{ route('keuangan.laporan.export-pdf', ['tahun_akademik_id' => $selectedTahunAkademikId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn-secondary">Export PDF</a>
                </form>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs text-gray-500">Total Tagihan</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">Rp{{ number_format($summary['total_tagihan'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs text-gray-500">Total Pembayaran</p>
                    <p class="mt-1 text-lg font-semibold text-emerald-700">Rp{{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs text-gray-500">Sisa Tagihan</p>
                    <p class="mt-1 text-lg font-semibold text-rose-700">Rp{{ number_format($summary['total_sisa'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                @foreach(['open', 'partial', 'paid', 'disputed', 'void'] as $status)
                    <span class="rounded-lg border border-gray-200 bg-white px-3 py-1">
                        {{ $statusLabel($status) }}: <strong>{{ $statusCounts[$status] ?? 0 }}</strong>
                    </span>
                @endforeach
            </div>

            <div class="mt-5 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Jumlah Tagihan</th>
                        <th class="px-4 py-3 text-left">Total Tagihan</th>
                        <th class="px-4 py-3 text-left">Total Pembayaran</th>
                        <th class="px-4 py-3 text-left">Sisa</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->tahun }} / {{ strtoupper($row->semester) }}</td>
                            <td class="px-4 py-3">{{ $row->jumlah_tagihan }}</td>
                            <td class="px-4 py-3">Rp{{ number_format((float) $row->total_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp{{ number_format((float) $row->total_pembayaran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp{{ number_format((float) $row->total_sisa, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-4 text-gray-500">Belum ada data laporan keuangan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection

