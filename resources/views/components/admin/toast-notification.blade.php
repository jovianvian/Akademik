@php
    $errorMessage = null;
    if ($errors->any()) {
        $errorMessage = $errors->first();
    } elseif (session('error')) {
        $errorMessage = session('error');
    }
@endphp

<div class="toast-stack">
    @if(session('success'))
        <div class="toast-item toast-success" data-toast>
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errorMessage)
        <div class="toast-item toast-error" data-toast>
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif
</div>
