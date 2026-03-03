<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        p { margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d9d9d9; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .header-table td { border: none; padding: 0; }
        .line { border-bottom: 2px solid #111; margin: 8px 0 10px; }
        .sign-table td { border: none; width: 50%; text-align: center; padding-top: 24px; }
    </style>
</head>
<body>
    <table class="header-table" width="100%">
        <tr>
            <td width="70">
                @if(!empty($docMeta['logo_data_uri']))
                    <img src="{{ $docMeta['logo_data_uri'] }}" style="width:58px;height:58px;">
                @endif
            </td>
            <td>
                <div style="font-size: 15px; font-weight: bold;">{{ $docMeta['institution_name'] }}</div>
                <div style="font-size: 12px; font-weight: bold;">{{ $docMeta['faculty_name'] }}</div>
                <div style="font-size: 10px;">{{ $docMeta['address'] }} | {{ $docMeta['phone'] }} | {{ $docMeta['website'] }}</div>
            </td>
        </tr>
    </table>
    <div class="line"></div>
    <h1>Kartu Hasil Studi</h1>
    <p>Mahasiswa: {{ $mahasiswa->nim ?? '-' }} - {{ $mahasiswa->nama ?? '-' }}</p>
    <p>Dicetak: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Nilai Angka</th>
                <th>Nilai Huruf</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row->tahun }}</td>
                <td>{{ ucfirst($row->semester) }}</td>
                <td>{{ strtoupper($row->status_krs) }}</td>
                <td>{{ $row->kode_mk }}</td>
                <td>{{ $row->nama_mk }}</td>
                <td>{{ $row->sks }}</td>
                <td>{{ $row->nilai_angka !== null ? number_format((float) $row->nilai_angka, 2) : '-' }}</td>
                <td>{{ $row->nilai_huruf ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="8">Belum ada data KHS.</td></tr>
        @endforelse
        </tbody>
    </table>

    <table class="sign-table" width="100%">
        <tr>
            @foreach(($docMeta['signatures'] ?? []) as $signature)
                <td>
                    <div>{{ $docMeta['city'] }}, {{ date('d-m-Y') }}</div>
                    <div>{{ $signature['label'] }}</div>
                    <div style="height: 56px;"></div>
                    <div><strong>{{ $signature['jabatan'] }}</strong></div>
                </td>
            @endforeach
        </tr>
    </table>
</body>
</html>
