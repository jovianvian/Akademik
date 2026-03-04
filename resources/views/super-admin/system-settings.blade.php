@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-12 gap-4 md:gap-6">
        <article class="card-panel col-span-12">
            <h2 class="text-base font-semibold text-gray-900">System Configuration</h2>
            @if(session('success')) <p class="mt-3 text-sm text-success-700">{{ session('success') }}</p> @endif
            @if($errors->any()) <p class="mt-3 text-sm text-error-600">{{ $errors->first() }}</p> @endif

            <form method="POST" action="{{ route('super-admin.system-settings.update') }}" class="grid grid-cols-1 gap-4 mt-4 xl:grid-cols-2">
                @csrf
                @method('PATCH')

                <div class="p-4 border border-gray-200 rounded-xl">
                    <h3 class="text-sm font-semibold text-gray-800">Konfigurasi Akademik</h3>
                    <div class="mt-3 space-y-3">
                        <label class="text-xs text-gray-600">Maksimal SKS Default</label>
                        <input type="number" name="max_sks_default" min="1" max="30" value="{{ old('max_sks_default', $settings->max_sks_default) }}" class="input-text">
                        <label class="text-xs text-gray-600">Maksimal SKS Jika IPS >= 3.00</label>
                        <input type="number" name="max_sks_ips_3" min="1" max="30" value="{{ old('max_sks_ips_3', $settings->max_sks_ips_3) }}" class="input-text">

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="evaluasi_enabled" value="1" @checked(old('evaluasi_enabled', $settings->evaluasi_enabled))>
                            Fitur evaluasi dosen aktif
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="auto_nonaktif_if_ukt_unpaid" value="1" @checked(old('auto_nonaktif_if_ukt_unpaid', $settings->auto_nonaktif_if_ukt_unpaid))>
                            Auto nonaktif jika UKT unpaid
                        </label>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl">
                    <h3 class="text-sm font-semibold text-gray-800">Maintenance</h3>
                    <div class="mt-3 space-y-3">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings->maintenance_mode))>
                            Maintenance mode (hanya super admin bisa login)
                        </label>
                        <label class="text-xs text-gray-600">KRS Open At (global)</label>
                        <input type="datetime-local" name="krs_open_at" class="input-text" value="{{ old('krs_open_at', $settings->krs_open_at ? date('Y-m-d\\TH:i', strtotime($settings->krs_open_at)) : '') }}">
                        <label class="text-xs text-gray-600">KRS Close At (global)</label>
                        <input type="datetime-local" name="krs_close_at" class="input-text" value="{{ old('krs_close_at', $settings->krs_close_at ? date('Y-m-d\\TH:i', strtotime($settings->krs_close_at)) : '') }}">
                        <label class="text-xs text-gray-600">Nilai Input Close At</label>
                        <input type="datetime-local" name="nilai_input_close_at" class="input-text" value="{{ old('nilai_input_close_at', $settings->nilai_input_close_at ? date('Y-m-d\\TH:i', strtotime($settings->nilai_input_close_at)) : '') }}">
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl">
                    <h3 class="text-sm font-semibold text-gray-800">Bobot Nilai (%)</h3>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="text-xs text-gray-600">Tugas</label>
                            <input type="number" step="0.01" name="bobot_tugas" value="{{ old('bobot_tugas', $settings->bobot_tugas) }}" class="input-text">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">UTS</label>
                            <input type="number" step="0.01" name="bobot_uts" value="{{ old('bobot_uts', $settings->bobot_uts) }}" class="input-text">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">UAS</label>
                            <input type="number" step="0.01" name="bobot_uas" value="{{ old('bobot_uas', $settings->bobot_uas) }}" class="input-text">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Kehadiran</label>
                            <input type="number" step="0.01" name="bobot_kehadiran" value="{{ old('bobot_kehadiran', $settings->bobot_kehadiran) }}" class="input-text">
                        </div>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl">
                    <h3 class="text-sm font-semibold text-gray-800">Range Konversi Nilai</h3>
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <input type="number" name="grade_a_min" step="0.01" class="input-text" value="{{ old('grade_a_min', $settings->grade_a_min) }}" placeholder="A min">
                        <input type="number" name="grade_a_minus_min" step="0.01" class="input-text" value="{{ old('grade_a_minus_min', $settings->grade_a_minus_min) }}" placeholder="A- min">
                        <input type="number" name="grade_b_plus_min" step="0.01" class="input-text" value="{{ old('grade_b_plus_min', $settings->grade_b_plus_min) }}" placeholder="B+ min">
                        <input type="number" name="grade_b_min" step="0.01" class="input-text" value="{{ old('grade_b_min', $settings->grade_b_min) }}" placeholder="B min">
                        <input type="number" name="grade_b_minus_min" step="0.01" class="input-text" value="{{ old('grade_b_minus_min', $settings->grade_b_minus_min) }}" placeholder="B- min">
                        <input type="number" name="grade_c_plus_min" step="0.01" class="input-text" value="{{ old('grade_c_plus_min', $settings->grade_c_plus_min) }}" placeholder="C+ min">
                        <input type="number" name="grade_c_min" step="0.01" class="input-text" value="{{ old('grade_c_min', $settings->grade_c_min) }}" placeholder="C min">
                        <input type="number" name="grade_d_min" step="0.01" class="input-text" value="{{ old('grade_d_min', $settings->grade_d_min) }}" placeholder="D min">
                    </div>
                </div>

                <div class="col-span-1 xl:col-span-2">
                    <button class="btn-primary" type="submit">Simpan Konfigurasi</button>
                </div>
            </form>
        </article>
    </section>
@endsection


