@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">Kontrol Periode KRS</h2>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif

            <div class="mt-4 table-wrap">
                <table class="table-base">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Tahun Akademik</th>
                        <th class="px-4 py-3 text-left">Status Aktif</th>
                        <th class="px-4 py-3 text-left">Kontrol Periode KRS</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->tahun }} {{ ucfirst($item->semester) }}</td>
                            <td class="px-4 py-3">{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-4 py-3">
                                <form action="{{ route('akademik.periode-krs.update', $item->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="flex items-center gap-2 text-xs">
                                        <input type="checkbox" name="krs_dibuka" value="1" @checked($item->krs_dibuka)>
                                        KRS Dibuka
                                    </label>
                                    <input type="datetime-local" name="krs_mulai" class="input-select" value="{{ $item->krs_mulai ? date('Y-m-d\\TH:i', strtotime($item->krs_mulai)) : '' }}">
                                    <input type="datetime-local" name="krs_selesai" class="input-select" value="{{ $item->krs_selesai ? date('Y-m-d\\TH:i', strtotime($item->krs_selesai)) : '' }}">
                                    <button class="btn-compact">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection



