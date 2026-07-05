@props([
    'title' => 'Dashboard',
    'breadcrumbs' => null
])

<div class="page-title-area">
    <h4>{{ $title }}</h4>
    <div>
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @elseif($breadcrumbs)
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                        <li class="active">{{ $breadcrumb['label'] }}</li>
                    @else
                        <li><a href="{{ $breadcrumb['url'] ?? '#' }}">{{ $breadcrumb['label'] }}</a></li>
                    @endif
                @endforeach
            </ol>
        @endif
    </div>
</div>