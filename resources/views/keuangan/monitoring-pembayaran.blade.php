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
            <div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Monitoring Pembayaran</h2>
                    <p class="mt-1 text-sm text-gray-500">Pantau status tagihan dan histori pembayaran mahasiswa.</p>
                </div>
            </div>
            <div class="mt-5 table-wrap">
                <table class="table-base" id="monitoringPembayaranTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Tagihan</th>
                        <th class="px-4 py-3 text-left">Total Bayar</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Pembayaran Terakhir</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->nim }} - {{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->tahun }} / {{ strtoupper($item->semester) }}</td>
                            <td class="px-4 py-3">Rp{{ number_format((float) $item->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp{{ number_format((float) $item->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $statusLabel($item->status) }}</td>
                            <td class="px-4 py-3">{{ $item->pembayaran_terakhir ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-gray-500">Belum ada data pembayaran.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>
@endsection
