@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-4">
            <h2 class="text-base font-semibold text-gray-900">Validasi Akademik</h2>
            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                <li class="{{ $statusAkademik['mahasiswa_aktif'] ? 'status-ok' : 'status-no' }}">Status mahasiswa aktif</li>
                <li class="{{ $statusAkademik['periode_krs'] ? 'status-ok' : 'status-no' }}">Periode KRS aktif</li>
                <li class="{{ $statusAkademik['krs_window'] ? 'status-ok' : 'status-no' }}">Window KRS dibuka admin akademik</li>
                <li class="{{ $statusAkademik['ukt_lunas'] ? 'status-ok' : 'status-no' }}">UKT lunas</li>
                <li class="status-ok">Tidak bentrok jadwal (divalidasi saat simpan)</li>
                <li class="status-ok">Prasyarat terpenuhi (tahap berikutnya)</li>
            </ul>
            <div class="p-3 mt-4 border border-blue-light-200 rounded-xl bg-blue-light-25">
                <p class="text-sm text-gray-700">Maksimal SKS semester ini: <strong>{{ $maxSks }} SKS</strong></p>
                <p class="mt-1 text-sm text-gray-700">Total pilihan: <strong id="sksTotal">0 SKS</strong></p>
                <p class="mt-1 text-sm text-gray-700">Status KRS saat ini: <strong>{{ strtoupper($krsStatus ?? 'belum ada') }}</strong></p>
            </div>

            @if(session('success'))
                <div class="p-3 mt-4 text-sm border rounded-xl border-success-200 bg-success-50 text-success-700">
                    {{ session('success') }}
                </div>
            @endif
            @if(!$statusAkademik['mahasiswa_aktif'])
                <div class="p-3 mt-4 text-sm border rounded-xl border-error-600/20 bg-red-50 text-error-600">
                    Status akademik Anda bukan `aktif`. Menu KRS bersifat baca-saja.
                </div>
            @endif
        </article>

        <article class="col-span-12 p-5 bg-white border border-gray-200 rounded-2xl shadow-theme-sm xl:col-span-8">
            <h2 class="text-base font-semibold text-gray-900">Form Isi KRS</h2>
            <form action="{{ route('krs.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="total_sks" id="totalSksInput" value="0" data-max-sks="{{ $maxSks }}">
                <div class="overflow-hidden border border-gray-200 rounded-xl">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                        <tr class="text-left text-gray-600">
                            <th class="px-4 py-3">Pilih</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Mata Kuliah</th>
                            <th class="px-4 py-3">Jadwal</th>
                            <th class="px-4 py-3">Ruangan</th>
                            <th class="px-4 py-3">SKS</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach($jadwal as $mk)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">
                                    <input
                                        type="checkbox"
                                        name="jadwal_id[]"
                                        value="{{ $mk->id }}"
                                        data-sks="{{ $mk->sks }}"
                                        class="krs-checkbox rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                        @checked(in_array($mk->id, $selectedJadwalIds, true))
                                        @disabled(!$statusAkademik['mahasiswa_aktif'] || !$statusAkademik['krs_window'] || $krsStatus === 'final')
                                    >
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $mk->kode }}</td>
                                <td class="px-4 py-3">{{ $mk->nama }}</td>
                                <td class="px-4 py-3">{{ $mk->hari }}, {{ $mk->jam }}</td>
                                <td class="px-4 py-3">{{ $mk->ruang }}</td>
                                <td class="px-4 py-3">{{ $mk->sks }}</td>
                            </tr>
                        @endforeach
                        @if($jadwal->isEmpty())
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada jadwal tersedia untuk prodi mahasiswa di semester aktif.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                @error('jadwal_id')
                <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                @enderror
                @error('total_sks')
                <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                @enderror

                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <button type="submit" class="btn-primary" @disabled(!$statusAkademik['mahasiswa_aktif'] || !$statusAkademik['krs_window'] || $krsStatus === 'final')>Simpan Draft KRS</button>
                    <span class="text-sm text-gray-500">Status awal akan tersimpan sebagai <strong>draft</strong>.</span>
                </div>
            </form>

            <form action="{{ route('krs.generate-auto') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg disabled:opacity-50" @disabled(!$statusAkademik['mahasiswa_aktif'] || !$statusAkademik['krs_window'] || $krsStatus === 'final')>Generate Otomatis</button>
                <p class="mt-2 text-xs text-gray-500">Sistem akan memilih mata kuliah draft otomatis berdasarkan data nilai dan batas SKS.</p>
            </form>

            <form action="{{ route('krs.finalize') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-success-600 disabled:opacity-50" @disabled(!$statusAkademik['mahasiswa_aktif'] || !$statusAkademik['krs_window'] || $krsStatus === 'final')>Finalisasi KRS</button>
                <p class="mt-2 text-xs text-gray-500">Setelah final, KRS mahasiswa tidak dapat diubah lagi dari akun mahasiswa.</p>
            </form>
        </article>
    </section>
@endsection
