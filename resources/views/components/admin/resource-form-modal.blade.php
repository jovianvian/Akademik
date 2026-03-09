@props([
    'id',
    'title',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Simpan',
    'size' => 'max-w-2xl',
])

<div id="{{ $id }}" class="admin-modal hidden" data-modal>
    <div class="admin-modal-card {{ $size }}">
        <div class="admin-modal-header">
            <h3 class="text-lg font-semibold text-gray-900" data-modal-title>{{ $title }}</h3>
            <button type="button" class="btn-secondary" data-modal-close>Tutup</button>
        </div>
        <form
            action="{{ $action }}"
            method="POST"
            class="mt-4 space-y-3"
            data-modal-form
            data-default-action="{{ $action }}"
            data-default-method="{{ strtoupper($method) }}"
            data-default-title="{{ $title }}"
            data-default-submit="{{ $submitLabel }}"
        >
            @csrf
            <input type="hidden" name="_method" value="{{ strtoupper($method) }}" data-modal-method>
            <input type="hidden" name="_modal" value="{{ $id }}">
            {{ $slot }}
            <div class="flex justify-end">
                <button type="submit" class="btn-primary" data-modal-submit>
                    <span data-modal-submit-label>{{ $submitLabel }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
