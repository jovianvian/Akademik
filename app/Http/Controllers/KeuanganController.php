<?php

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use App\Support\FinancialImpactService;
use App\Support\PdfDocumentMeta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    public function tagihan()
    {
        $tagihan = DB::table('tagihan_ukt as t')
            ->join('mahasiswa as m', 'm.id', '=', 't.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
            ->leftJoin('pembayaran as p', 'p.tagihan_id', '=', 't.id')
            ->select(
                't.id',
                'm.nim',
                'm.nama',
                'ta.tahun',
                'ta.semester',
                't.jumlah',
                't.status',
                DB::raw('COALESCE(SUM(p.jumlah_bayar), 0) as total_bayar')
            )
            ->groupBy('t.id', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 't.jumlah', 't.status')
            ->orderByDesc('t.id')
            ->get();

        $monitoring = [
            'open' => $tagihan->where('status', 'open')->count(),
            'partial' => $tagihan->where('status', 'partial')->count(),
            'paid' => $tagihan->where('status', 'paid')->count(),
            'disputed' => $tagihan->where('status', 'disputed')->count(),
            'void' => $tagihan->where('status', 'void')->count(),
        ];

        return view('keuangan.tagihan', [
            'title' => 'Kelola Tagihan UKT',
            'items' => $tagihan,
            'mahasiswa' => DB::table('mahasiswa')->orderBy('nim')->get(),
            'tahunAkademik' => DB::table('tahun_akademik')->orderByDesc('status_aktif')->orderByDesc('id')->get(),
            'monitoring' => $monitoring,
        ]);
    }

    public function storeTagihan(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'integer', 'exists:mahasiswa,id'],
            'tahun_akademik_id' => ['required', 'integer', 'exists:tahun_akademik,id'],
            'jumlah' => ['required', 'numeric', 'min:1'],
        ]);

        $exists = DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['jumlah' => 'Tagihan untuk mahasiswa dan tahun akademik ini sudah ada.'])->withInput();
        }

        DB::table('tagihan_ukt')->insert([
            ...$validated,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tagihanId = (int) DB::table('tagihan_ukt')
            ->where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
            ->value('id');

        AuditLogger::log(
            aksi: 'Buat tagihan UKT',
            modul: 'keuangan',
            entityType: 'tagihan_ukt',
            entityId: $tagihanId,
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Tagihan UKT berhasil dibuat.');
    }

    public function updateStatusTagihan(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,partial,paid,disputed,void'],
        ]);

        $before = DB::table('tagihan_ukt')->where('id', $id)->value('status');
        DB::table('tagihan_ukt')->where('id', $id)->update([
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);
        FinancialImpactService::applyTagihanStatusImpact($id, $validated['status'], auth()->id(), 'Perubahan status tagihan via admin keuangan.');

        AuditLogger::log(
            aksi: 'Ubah status tagihan UKT',
            modul: 'keuangan',
            entityType: 'tagihan_ukt',
            entityId: $id,
            konteks: ['before' => $before, 'after' => $validated['status']],
            request: $request
        );

        return back()->with('success', 'Status tagihan berhasil diperbarui.');
    }

    public function pembayaran(Request $request)
    {
        $tahunAkademikId = $request->query('tahun_akademik_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sortBy = $request->query('sort_by', 'tanggal_bayar');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortMap = [
            'tanggal_bayar' => 'p.tanggal_bayar',
            'jumlah_bayar' => 'p.jumlah_bayar',
            'nim' => 'm.nim',
            'nama' => 'm.nama',
        ];
        $sortColumn = $sortMap[$sortBy] ?? $sortMap['tanggal_bayar'];

        $query = DB::table('pembayaran as p')
            ->join('tagihan_ukt as t', 't.id', '=', 'p.tagihan_id')
            ->join('mahasiswa as m', 'm.id', '=', 't.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
            ->select('p.*', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 't.jumlah as jumlah_tagihan');

        if ($tahunAkademikId) {
            $query->where('t.tahun_akademik_id', $tahunAkademikId);
        }
        if ($dateFrom) {
            $query->whereDate('p.tanggal_bayar', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('p.tanggal_bayar', '<=', $dateTo);
        }

        $pembayaran = $query
            ->orderBy($sortColumn, $sortDir)
            ->orderByDesc('p.id')
            ->paginate(15)
            ->withQueryString();

        $tagihanMenunggu = DB::table('tagihan_ukt as t')
            ->join('mahasiswa as m', 'm.id', '=', 't.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
            ->whereIn('t.status', ['open', 'partial'])
            ->select('t.id', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 't.jumlah')
            ->orderByDesc('t.id')
            ->get();

        return view('keuangan.pembayaran', [
            'title' => 'Validasi Pembayaran',
            'items' => $pembayaran,
            'tagihanMenunggu' => $tagihanMenunggu,
            'tahunAkademikList' => DB::table('tahun_akademik')->orderByDesc('id')->get(),
            'selectedTahunAkademikId' => $tahunAkademikId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }

    public function storePembayaran(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => ['required', 'integer', 'exists:tagihan_ukt,id'],
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'metode_bayar' => ['required', 'in:transfer,cash,va,qris'],
            'bukti_bayar' => ['nullable', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $tagihan = DB::table('tagihan_ukt')
                    ->where('id', $validated['tagihan_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $tagihan) {
                    throw new \RuntimeException('Tagihan tidak ditemukan.');
                }

                $existsDuplicate = DB::table('pembayaran')
                    ->where('tagihan_id', $validated['tagihan_id'])
                    ->whereDate('tanggal_bayar', $validated['tanggal_bayar'])
                    ->where('jumlah_bayar', $validated['jumlah_bayar'])
                    ->where('metode_bayar', $validated['metode_bayar'])
                    ->lockForUpdate()
                    ->exists();

                if ($existsDuplicate) {
                    throw new \RuntimeException('Pembayaran duplikat terdeteksi pada tagihan yang sama.');
                }

                $existingPayments = DB::table('pembayaran')
                    ->where('tagihan_id', $validated['tagihan_id'])
                    ->lockForUpdate()
                    ->select('jumlah_bayar')
                    ->get();
                $totalSebelum = (float) $existingPayments->sum('jumlah_bayar');
                $totalSesudah = $totalSebelum + (float) $validated['jumlah_bayar'];
                $isReconciliationError = $totalSesudah > (float) $tagihan->jumlah;

                DB::table('pembayaran')->insert([
                    'tagihan_id' => $validated['tagihan_id'],
                    'tanggal_bayar' => $validated['tanggal_bayar'],
                    'jumlah_bayar' => $validated['jumlah_bayar'],
                    'metode_bayar' => $validated['metode_bayar'],
                    'bukti_bayar' => $validated['bukti_bayar'] ?? null,
                    'is_reconciliation_error' => $isReconciliationError,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalBayar = DB::table('pembayaran')
                    ->where('tagihan_id', $validated['tagihan_id'])
                    ->lockForUpdate()
                    ->sum('jumlah_bayar');
                $statusBaru = $totalBayar >= $tagihan->jumlah ? 'paid' : 'partial';

                DB::table('tagihan_ukt')->where('id', $validated['tagihan_id'])->update([
                    'status' => $statusBaru,
                    'updated_at' => now(),
                ]);

                FinancialImpactService::applyTagihanStatusImpact(
                    (int) $validated['tagihan_id'],
                    $statusBaru,
                    auth()->id(),
                    'Rekalkulasi status tagihan setelah input pembayaran.'
                );
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['jumlah_bayar' => $e->getMessage()])->withInput();
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                return back()->withErrors(['jumlah_bayar' => 'Pembayaran duplikat terdeteksi oleh sistem.'])->withInput();
            }
            throw $e;
        }

        AuditLogger::log(
            aksi: 'Input pembayaran UKT',
            modul: 'keuangan',
            entityType: 'tagihan_ukt',
            entityId: $validated['tagihan_id'],
            konteks: $validated,
            request: $request
        );

        return back()->with('success', 'Pembayaran berhasil dicatat dan status tagihan diperbarui.');
    }

    public function exportPembayaranCsv(Request $request)
    {
        $tahunAkademikId = $request->query('tahun_akademik_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = DB::table('pembayaran as p')
            ->join('tagihan_ukt as t', 't.id', '=', 'p.tagihan_id')
            ->join('mahasiswa as m', 'm.id', '=', 't.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
            ->select('p.tanggal_bayar', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 'p.jumlah_bayar', 'p.metode_bayar', 'p.bukti_bayar', 'p.is_reconciliation_error')
            ->orderByDesc('p.tanggal_bayar');

        if ($tahunAkademikId) {
            $query->where('t.tahun_akademik_id', $tahunAkademikId);
        }
        if ($dateFrom) {
            $query->whereDate('p.tanggal_bayar', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('p.tanggal_bayar', '<=', $dateTo);
        }

        $rows = $query->orderByDesc('p.id')->get();

        $filename = 'pembayaran-ukt-'.now()->format('YmdHis').'.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal Bayar', 'NIM', 'Nama', 'Tahun', 'Semester', 'Jumlah Bayar', 'Metode', 'Bukti', 'Rekonsiliasi Error']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->tanggal_bayar, $row->nim, $row->nama, $row->tahun, $row->semester, $row->jumlah_bayar, $row->metode_bayar, $row->bukti_bayar, $row->is_reconciliation_error ? 'YA' : 'TIDAK']);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPembayaranPdf(Request $request)
    {
        $tahunAkademikId = $request->query('tahun_akademik_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = DB::table('pembayaran as p')
            ->join('tagihan_ukt as t', 't.id', '=', 'p.tagihan_id')
            ->join('mahasiswa as m', 'm.id', '=', 't.mahasiswa_id')
            ->join('tahun_akademik as ta', 'ta.id', '=', 't.tahun_akademik_id')
            ->select('p.tanggal_bayar', 'm.nim', 'm.nama', 'ta.tahun', 'ta.semester', 'p.jumlah_bayar', 'p.metode_bayar', 'p.bukti_bayar', 'p.is_reconciliation_error');

        if ($tahunAkademikId) {
            $query->where('t.tahun_akademik_id', $tahunAkademikId);
        }
        if ($dateFrom) {
            $query->whereDate('p.tanggal_bayar', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('p.tanggal_bayar', '<=', $dateTo);
        }

        $rows = $query->orderByDesc('p.tanggal_bayar')->orderByDesc('p.id')->get();

        $pdf = Pdf::loadView('pdf.pembayaran', [
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'docMeta' => PdfDocumentMeta::build('pembayaran'),
            'filters' => [
                'tahun_akademik_id' => $tahunAkademikId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('pembayaran-ukt-'.now()->format('YmdHis').'.pdf');
    }
}
