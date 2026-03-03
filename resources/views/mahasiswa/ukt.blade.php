@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Status Pembayaran UKT</h2>
            @if($mahasiswa)
                <p class="mt-1 text-sm text-gray-500">{{ $mahasiswa->nim }} - {{ $mahasiswa->nama }}</p>
            @endif
            <div class="mt-4 overflow-hidden border border-gray-200 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Tagihan</th>
                        <th class="px-4 py-3 text-left">Terbayar</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Detail</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                            <td class="px-4 py-3">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp{{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ strtoupper($item->status) }}</td>
                            <td class="px-4 py-3">
                                <details>
                                    <summary class="cursor-pointer text-brand-600">Lihat transaksi</summary>
                                    <div class="mt-2 overflow-hidden border border-gray-200 rounded-lg">
                                        <table class="w-full text-xs">
                                            <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Tanggal</th>
                                                <th class="px-3 py-2 text-left">Jumlah</th>
                                                <th class="px-3 py-2 text-left">Metode</th>
                                                <th class="px-3 py-2 text-left">Status Validasi</th>
                                                <th class="px-3 py-2 text-left">Bukti</th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                            @forelse(($transactions[$item->id] ?? collect()) as $trx)
                                                <tr>
                                                    <td class="px-3 py-2">{{ $trx->tanggal_bayar }}</td>
                                                    <td class="px-3 py-2">Rp{{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</td>
                                                    <td class="px-3 py-2">{{ strtoupper($trx->metode_bayar) }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($trx->is_reconciliation_error)
                                                            <span class="text-error-600">Perlu review</span>
                                                        @else
                                                            <span class="text-success-700">Valid</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">{{ $trx->bukti_bayar ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="px-3 py-2 text-gray-500">Belum ada transaksi pembayaran.</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada data tagihan UKT.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
