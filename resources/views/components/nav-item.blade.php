@props(['label', 'icon', 'href' => '#', 'active' => false, 'disabled' => false, 'badge' => null])
<a href="{{ $disabled ? '#' : $href }}" @class(['sidebar-link', 'active' => $active, 'disabled' => $disabled]) @if($disabled) aria-disabled="true" tabindex="-1" @endif>
    <i class="bi {{ $icon }}"></i><span>{{ $label }}</span>
    @if ($badge)<span class="badge rounded-pill text-bg-primary ms-auto">{{ $badge }}</span>@endif
    @if ($disabled)<small class="ms-auto">Pronto</small>@endif
</a>
