@props([
    'id' => null,
    'skeletonCols' => 5,
])

<div class="table-wrap relative">
    <div class="table-skeleton absolute inset-0 z-10" data-table-skeleton>
        <div class="table-skeleton-line"></div>
        <div class="table-skeleton-line"></div>
        <div class="table-skeleton-line"></div>
        <div class="table-skeleton-line"></div>
    </div>
    <table class="table-base" @if($id) id="{{ $id }}" @endif>
        {{ $slot }}
    </table>
</div>
