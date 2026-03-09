<div id="confirmDeleteModal" class="admin-modal hidden" data-modal>
    <div class="admin-modal-card max-w-md">
        <div class="admin-modal-header">
            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
            <button type="button" class="btn-secondary" data-modal-close>Tutup</button>
        </div>
        <p class="mt-4 text-sm text-gray-600" id="confirmDeleteText">
            Apakah Anda yakin ingin menghapus data ini?
        </p>
        <form method="POST" id="confirmDeleteForm" class="mt-4 flex justify-end gap-2">
            @csrf
            @method('DELETE')
            <button type="button" class="btn-secondary" data-modal-close>Batal</button>
            <button type="submit" class="btn-compact-danger">Hapus</button>
        </form>
    </div>
</div>
