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
                        <th class="px-4 py-3 text-left">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left">Dosen</th>
                        <th class="px-4 py-3 text-left">Status Evaluasi</th>
                        <th class="px-4 py-3 text-left">Skor (1-5)</th>
                        <th class="px-4 py-3 text-left">Komentar</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
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
                            <td class="px-4 py-3">
                                <form action="{{ route('mahasiswa.evaluasi.store') }}" method="POST" class="grid gap-2 md:grid-cols-5">
                                    @csrf
                                    <input type="hidden" name="krs_detail_id" value="{{ $item->krs_detail_id }}">
                                    <select name="nilai_1" class="input-select" required>
                                        <option value="">Kejelasan</option>
                                        @for($i=1;$i<=5;$i++)
                                            <option value="{{ $i }}" @selected((int) $item->nilai_1 === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="nilai_2" class="input-select" required>
                                        <option value="">Interaksi</option>
                                        @for($i=1;$i<=5;$i++)
                                            <option value="{{ $i }}" @selected((int) $item->nilai_2 === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="nilai_3" class="input-select" required>
                                        <option value="">Disiplin</option>
                                        @for($i=1;$i<=5;$i++)
                                            <option value="{{ $i }}" @selected((int) $item->nilai_3 === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <input type="text" name="komentar" value="{{ $item->komentar }}" placeholder="Komentar (opsional)" class="input-select md:col-span-2">
                                    <button class="btn-primary">{{ $item->evaluasi_id ? 'Perbarui' : 'Kirim' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-gray-500">Belum ada mata kuliah untuk dievaluasi.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection


