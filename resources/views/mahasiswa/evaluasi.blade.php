@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Evaluasi Dosen</h2>
            @if(session('success'))
                <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p>
            @endif
            @if($errors->any())
                <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p>
            @endif

            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left" style="width: 26%;">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left" style="width: 18%;">Dosen</th>
                        <th class="px-4 py-3 text-left" style="width: 12%;">Status Evaluasi</th>
                        <th class="px-4 py-3 text-left" style="width: 12%;">Skor (1-5)</th>
                        <th class="px-4 py-3 text-left" style="width: 20%;">Komentar</th>
                        <th class="px-4 py-3 text-left table-action-col" style="width: 12%;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->kode_mk }} - {{ $item->nama_mk }}</td>
                            <td class="px-4 py-3">{{ $item->nama_dosen }}</td>
                            <td class="px-4 py-3">
                                @if($item->evaluasi_id)
                                    <span class="text-success-700">Selesai</span>
                                @else
                                    <span class="text-gray-500">Belum</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($item->evaluasi_id)
                                    {{ $item->nilai_1 }}/{{ $item->nilai_2 }}/{{ $item->nilai_3 }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-[260px]">
                                {{ $item->komentar ? \Illuminate\Support\Str::limit($item->komentar, 80) : '-' }}
                            </td>
                            <td class="px-4 py-3 table-action-col">
                                <button
                                    type="button"
                                    class="btn-compact"
                                    data-evaluasi-open
                                    data-krs-detail-id="{{ $item->krs_detail_id }}"
                                    data-mk="{{ $item->kode_mk }} - {{ $item->nama_mk }}"
                                    data-dosen="{{ $item->nama_dosen }}"
                                    data-nilai-1="{{ $item->nilai_1 ?? '' }}"
                                    data-nilai-2="{{ $item->nilai_2 ?? '' }}"
                                    data-nilai-3="{{ $item->nilai_3 ?? '' }}"
                                    data-komentar="{{ $item->komentar ?? '' }}"
                                    data-submit-label="{{ $item->evaluasi_id ? 'Perbarui' : 'Kirim' }}"
                                >
                                    {{ $item->evaluasi_id ? 'Perbarui' : 'Isi' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-gray-500">Belum ada mata kuliah untuk dievaluasi.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </article>
    </section>

    <div id="evaluasiModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Form Evaluasi Dosen</h3>
                    <p id="evaluasiMeta" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <button id="evaluasiModalClose" type="button" class="btn-secondary">Tutup</button>
            </div>

            <form id="evaluasiForm" action="{{ route('mahasiswa.evaluasi.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="krs_detail_id" id="evaluasiKrsDetailId">
                <div class="grid gap-3 md:grid-cols-3">
                    <select name="nilai_1" id="evaluasiNilai1" class="input-select w-full" required>
                        <option value="">Kejelasan</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <select name="nilai_2" id="evaluasiNilai2" class="input-select w-full" required>
                        <option value="">Interaksi</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <select name="nilai_3" id="evaluasiNilai3" class="input-select w-full" required>
                        <option value="">Disiplin</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <input type="text" name="komentar" id="evaluasiKomentar" placeholder="Komentar (opsional)" class="input-text">
                <div class="flex justify-end">
                    <button type="submit" id="evaluasiSubmitBtn" class="btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('evaluasiModal');
            const closeBtn = document.getElementById('evaluasiModalClose');
            const meta = document.getElementById('evaluasiMeta');
            const krsDetail = document.getElementById('evaluasiKrsDetailId');
            const nilai1 = document.getElementById('evaluasiNilai1');
            const nilai2 = document.getElementById('evaluasiNilai2');
            const nilai3 = document.getElementById('evaluasiNilai3');
            const komentar = document.getElementById('evaluasiKomentar');
            const submitBtn = document.getElementById('evaluasiSubmitBtn');
            if (!modal) return;

            const openModal = (payload) => {
                meta.textContent = `${payload.mk} | ${payload.dosen}`;
                krsDetail.value = payload.krsDetailId || '';
                nilai1.value = payload.nilai1 || '';
                nilai2.value = payload.nilai2 || '';
                nilai3.value = payload.nilai3 || '';
                komentar.value = payload.komentar || '';
                submitBtn.textContent = payload.submitLabel || 'Kirim';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            document.querySelectorAll('[data-evaluasi-open]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    openModal({
                        krsDetailId: btn.dataset.krsDetailId,
                        mk: btn.dataset.mk,
                        dosen: btn.dataset.dosen,
                        nilai1: btn.dataset.nilai1,
                        nilai2: btn.dataset.nilai2,
                        nilai3: btn.dataset.nilai3,
                        komentar: btn.dataset.komentar,
                        submitLabel: btn.dataset.submitLabel,
                    });
                });
            });

            closeBtn?.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
        })();
    </script>
@endpush
