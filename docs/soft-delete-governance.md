# Soft Delete Governance & Recovery Policy

Dokumen ini menjadi acuan operasional master data yang memakai `deleted_at` (soft delete).

## Scope Tabel Master

- `fakultas`
- `program_studi`
- `mata_kuliah`
- `jadwal`
- `jabatan_dosen`

## Prinsip Governance

1. Delete master data adalah **logical delete** (`deleted_at`, `deleted_by`), bukan hard delete.
2. Semua relasi historis akademik tetap valid walau master data di-soft-delete.
3. Recovery hanya boleh oleh `super_admin` via menu recovery.
4. Setiap delete dan restore wajib tercatat di `audit_logs`.
5. Data hasil finalisasi (KRS final/KHS) tidak boleh dimutasi diam-diam lewat delete/restore master.

## Operational Rules

1. **Delete**
- User non-super-admin melakukan soft delete dari modul master.
- Sistem mengisi `deleted_at`, `deleted_by`.
- Sistem menulis audit trail tindakan delete.

2. **Read Query**
- Halaman operasional hanya baca data aktif (`whereNull(deleted_at)`).
- Halaman recovery khusus menampilkan data terhapus (`whereNotNull(deleted_at)`).

3. **Restore**
- Hanya super admin dapat restore.
- Restore mengosongkan `deleted_at` dan `deleted_by`.
- Sistem menulis audit trail restore.

4. **Guardrail**
- Hindari restore massal tanpa verifikasi dampak relasi.
- Jika terjadi konflik bisnis setelah restore (contoh bentrok jadwal), lakukan perbaikan data lanjutan via modul terkait.

## Recovery Checklist

1. Verifikasi alasan restore.
2. Verifikasi entitas tidak duplikat dengan data aktif lain.
3. Jalankan restore dari menu `super-admin/master-recovery`.
4. Cek audit log untuk memastikan aktor, waktu, dan entitas tercatat.
5. Validasi ulang modul yang terdampak (jadwal/KRS/report).

