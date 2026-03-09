@props([
    'title',
    'description' => null,
    'addLabel' => null,
    'addTarget' => null,
])

<section class="grid grid-cols-12 gap-4 md:gap-6">
    <article class="card-panel col-span-12">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>
                @if($description)
                    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
                @endif
            </div>
            @if($addLabel && $addTarget)
                <button type="button" class="btn-primary" data-modal-open="{{ $addTarget }}">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span class="ml-2">{{ $addLabel }}</span>
                </button>
            @endif
        </div>

        @if (isset($toolbar))
            <div class="mt-4">
                {{ $toolbar }}
            </div>
        @endif

        <div class="mt-4">
            {{ $slot }}
        </div>
    </article>
</section>
