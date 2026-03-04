@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="col-span-12 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Open/Partial</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ ($monitoring['open'] ?? 0) + ($monitoring['partial'] ?? 0) }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Paid</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $monitoring['paid'] ?? 0 }}</p>
            </div>
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-theme-sm">
                <p class="text-sm text-gray-500">Tagihan Disputed/Void</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ ($monitoring['disputed'] ?? 0) + ($monitoring['void'] ?? 0) }}</p>
            </div>
        </article>

        <article class="card-panel xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Tambah Tagihan UKT</h2>
            <form action="{{ route('keuangan.tagihan.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <select name="mahasiswa_id" class="input-text">
                    <option value="">Pilih mahasiswa</option>
                    @foreach($mahasiswa as $m)
                        <option value="{{ $m->id }}">{{ $m->nim }} - {{ $m->nama }}</option>
                    @endforeach
                </select>
                <select name="tahun_akademik_id" class="input-text">
                    <option value="">Pilih tahun akademik</option>
                    @foreach($tahunAkademik as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->tahun }} - {{ ucfirst($ta->semester) }}</option>
                    @endforeach
                </select>
                <input type="number" name="jumlah" class="input-text" placeholder="Jumlah UKT" min="1" step="0.01">
                @if($errors->any()) <p class="text-sm text-error-600">{{ $errors->first() }}</p> @endif
                <button class="btn-primary" type="submit">Simpan Tagihan</button>
            </form>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
        </article>

        <article class="card-panel xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Data Tagihan Mahasiswa</h2>
            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Mahasiswa</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-left">Tagihan</th>
                        <th class="px-4 py-3 text-left">Terbayar</th>
                        <th class="px-4 py-3 text-left">Status</th>
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
                                <form action="{{ route('keuangan.tagihan.status', $item->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="input-select">
                                        <option value="open" @selected($item->status === 'open')>Open</option>
                                        <option value="partial" @selected($item->status === 'partial')>Partial</option>
                                        <option value="paid" @selected($item->status === 'paid')>Paid</option>
                                        <option value="disputed" @selected($item->status === 'disputed')>Disputed</option>
                                        <option value="void" @selected($item->status === 'void')>Void</option>
                                    </select>
                                    <button class="btn-compact">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-gray-500">Belum ada data tagihan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection



