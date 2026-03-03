<?php

namespace App\Http\Controllers;

use App\Support\PdfDocumentMeta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $modul = $request->query('modul');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $q = $request->query('q');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortMap = [
            'created_at' => 'a.created_at',
            'modul' => 'a.modul',
            'aksi' => 'a.aksi',
            'user' => 'u.name',
        ];
        $sortColumn = $sortMap[$sortBy] ?? $sortMap['created_at'];
        $query = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select('a.*', 'u.name as user_name', 'u.email as user_email');

        if ($modul) {
            $query->where('a.modul', $modul);
        }
        if ($dateFrom) {
            $query->whereDate('a.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('a.created_at', '<=', $dateTo);
        }
        if ($q) {
            $query->where(function ($inner) use ($q) {
                $inner->where('a.aksi', 'like', "%{$q}%")
                    ->orWhere('a.entity_type', 'like', "%{$q}%")
                    ->orWhere('u.name', 'like', "%{$q}%")
                    ->orWhere('u.email', 'like', "%{$q}%");
            });
        }

        return view('super-admin.audit-logs', [
            'title' => 'Audit Log Sistem',
            'items' => $query
                ->orderBy($sortColumn, $sortDir)
                ->orderByDesc('a.id')
                ->paginate(25)
                ->withQueryString(),
            'selectedModul' => $modul,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'q' => $q,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'modulList' => DB::table('audit_logs')->select('modul')->distinct()->orderBy('modul')->pluck('modul'),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $modul = $request->query('modul');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $q = $request->query('q');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortMap = [
            'created_at' => 'a.created_at',
            'modul' => 'a.modul',
            'aksi' => 'a.aksi',
            'user' => 'u.name',
        ];
        $sortColumn = $sortMap[$sortBy] ?? $sortMap['created_at'];
        $query = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select('a.created_at', 'u.name as user_name', 'u.email as user_email', 'a.modul', 'a.aksi', 'a.entity_type', 'a.entity_id', 'a.konteks');

        if ($modul) {
            $query->where('a.modul', $modul);
        }
        if ($dateFrom) {
            $query->whereDate('a.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('a.created_at', '<=', $dateTo);
        }
        if ($q) {
            $query->where(function ($inner) use ($q) {
                $inner->where('a.aksi', 'like', "%{$q}%")
                    ->orWhere('a.entity_type', 'like', "%{$q}%")
                    ->orWhere('u.name', 'like', "%{$q}%")
                    ->orWhere('u.email', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderBy($sortColumn, $sortDir)->orderByDesc('a.id')->get();
        $filename = 'audit-logs-'.($modul ?: 'semua').'-'.now()->format('YmdHis').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Waktu', 'User', 'Email', 'Modul', 'Aksi', 'Entity Type', 'Entity ID', 'Konteks']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->created_at,
                    $row->user_name,
                    $row->user_email,
                    $row->modul,
                    $row->aksi,
                    $row->entity_type,
                    $row->entity_id,
                    $row->konteks,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request)
    {
        $modul = $request->query('modul');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $q = $request->query('q');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortMap = [
            'created_at' => 'a.created_at',
            'modul' => 'a.modul',
            'aksi' => 'a.aksi',
            'user' => 'u.name',
        ];
        $sortColumn = $sortMap[$sortBy] ?? $sortMap['created_at'];

        $query = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select('a.created_at', 'u.name as user_name', 'u.email as user_email', 'a.modul', 'a.aksi', 'a.entity_type', 'a.entity_id', 'a.konteks');

        if ($modul) {
            $query->where('a.modul', $modul);
        }
        if ($dateFrom) {
            $query->whereDate('a.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('a.created_at', '<=', $dateTo);
        }
        if ($q) {
            $query->where(function ($inner) use ($q) {
                $inner->where('a.aksi', 'like', "%{$q}%")
                    ->orWhere('a.entity_type', 'like', "%{$q}%")
                    ->orWhere('u.name', 'like', "%{$q}%")
                    ->orWhere('u.email', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderBy($sortColumn, $sortDir)->orderByDesc('a.id')->limit(1000)->get();

        $pdf = Pdf::loadView('pdf.audit-logs', [
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'docMeta' => PdfDocumentMeta::build('audit_logs'),
            'filters' => [
                'modul' => $modul,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'q' => $q,
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('audit-logs-'.($modul ?: 'semua').'-'.now()->format('YmdHis').'.pdf');
    }
}
