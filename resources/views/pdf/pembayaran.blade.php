<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        p { margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
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
    <h1>Laporan Pembayaran UKT</h1>
    <p>Dicetak: {{ $generatedAt }}</p>
    <p>Filter: Periode={{ $filters['tahun_akademik_id'] ?: 'semua' }}, Dari={{ $filters['date_from'] ?: '-' }}, Sampai={{ $filters['date_to'] ?: '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Tahun</th>
                <th>Semester</th>
                <th>Jumlah Bayar</th>
                <th>Metode</th>
                <th>Bukti</th>
                <th>Rekonsiliasi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row->tanggal_bayar }}</td>
                <td>{{ $row->nim }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->tahun }}</td>
                <td>{{ ucfirst($row->semester) }}</td>
                <td>Rp{{ number_format($row->jumlah_bayar, 0, ',', '.') }}</td>
                <td>{{ strtoupper($row->metode_bayar) }}</td>
                <td>{{ $row->bukti_bayar ?? '-' }}</td>
                <td>{{ $row->is_reconciliation_error ? 'ERROR' : 'OK' }}</td>
            </tr>
        @empty
            <tr><td colspan="9">Belum ada data pembayaran.</td></tr>
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
