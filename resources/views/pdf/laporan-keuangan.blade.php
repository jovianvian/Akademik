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
        .summary td { border: 1px solid #e5e7eb; }
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
    <h1>Laporan Keuangan</h1>
    <p>Dicetak: {{ $generatedAt }}</p>
    <p>Filter: Periode={{ $filters['tahun_akademik_id'] ?: 'semua' }}, Dari={{ $filters['date_from'] ?: '-' }}, Sampai={{ $filters['date_to'] ?: '-' }}</p>

    <table class="summary">
        <tr>
            <td><strong>Total Tagihan</strong><br>Rp{{ number_format((float) $summary['total_tagihan'], 0, ',', '.') }}</td>
            <td><strong>Total Pembayaran</strong><br>Rp{{ number_format((float) $summary['total_pembayaran'], 0, ',', '.') }}</td>
            <td><strong>Sisa Tagihan</strong><br>Rp{{ number_format((float) $summary['total_sisa'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Jumlah Tagihan</th>
                <th>Total Tagihan</th>
                <th>Total Pembayaran</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row->tahun }} / {{ strtoupper($row->semester) }}</td>
                <td>{{ $row->jumlah_tagihan }}</td>
                <td>Rp{{ number_format((float) $row->total_tagihan, 0, ',', '.') }}</td>
                <td>Rp{{ number_format((float) $row->total_pembayaran, 0, ',', '.') }}</td>
                <td>Rp{{ number_format((float) $row->total_sisa, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data laporan keuangan.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>

