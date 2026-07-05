@props(['title' => '', 'id' => null])

<div class="card-admin" @if($id) id="{{ $id }}" @endif {{ $attributes }}>
    <div class="card-admin-header">
        <div class="flex items-center gap-3">
            <div class="w-1 h-6 bg-amber-500 rounded-full"></div>
            <span class="font-semibold text-navy-900">{{ $title }}</span>
            {{ $badge ?? '' }}
        </div>
    </div>
    <div class="card-admin-body">
        {{ $slot }}
    </div>
</div>
