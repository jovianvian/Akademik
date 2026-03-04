# Edge Case Review (End-to-End)

## 1) UKT `void` / `disputed` setelah KRS final

### Expected flow
- Admin keuangan ubah status tagihan ke `disputed` atau `void`.
- `FinancialImpactService` mendeteksi KRS periode tersebut sudah `final`.
- Sistem mutasi eligibility ke `suspended_pending_decision` via `AcademicStatusMutationService`.
- Sistem membuat entri `academic_decisions` dengan context:
  - `ukt_disputed_after_krs_final` atau
  - `ukt_void_after_krs_final`

### Impact
- Academic write (KRS/evaluasi/nilai terkait eligibility) terblokir sesuai policy.
- Audit trail tetap lengkap (status log + audit log + decision log).

## 2) Semester close dan restore eligibility

### Expected flow
- Jika semester tidak aktif (`tahun_akademik.status_aktif = false`), transisi dari suspended ke eligible ditolak.
- Guardrail ada di `AcademicEligibilityService::assertTransitionPolicy`.

### Impact
- Restore eligibility dipaksa terjadi di semester aktif untuk menghindari mutasi histori semester tutup.

## 3) Payment recovery (`paid`) setelah exception finansial

### Expected flow
- Saat status tagihan kembali `paid`:
  - Jika semester aktif dan status period `suspended_pending_decision`, sistem auto restore ke `eligible`.
  - Jika semester tidak aktif, sistem tidak restore otomatis, hanya mencatat `academic_decisions` (decision `override`).

### Impact
- Kasus recovery tetap terlacak, tapi tidak merusak governance semester.

## 4) Restore eligibility manual

### Expected flow
- Admin akademik melakukan perubahan status manual.
- Controller tidak boleh update `mahasiswa.status_akademik` langsung.
- Semua mutasi via `AcademicStatusMutationService`.

### Impact
- "No direct status mutation" terjaga.
- Status period, legacy status, log status, dan audit log konsisten.

