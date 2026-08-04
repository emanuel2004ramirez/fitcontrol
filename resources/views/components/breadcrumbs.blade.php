@props(['items' => []])
@php
    $resolvedItems = $items ?: [['label' => 'Inicio', 'url' => route('dashboard')], ['label' => trim($__env->yieldContent('title', 'Dashboard'))]];
@endphp
<nav aria-label="Breadcrumb" class="mb-3">
    <ol class="breadcrumb app-breadcrumb mb-0">
        @foreach ($resolvedItems as $item)
            <li @class(['breadcrumb-item', 'active' => $loop->last]) @if($loop->last) aria-current="page" @endif>
                @if (! $loop->last && isset($item['url']))<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@else{{ $item['label'] }}@endif
            </li>
        @endforeach
    </ol>
</nav>
