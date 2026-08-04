@props(['title' => 'Aún no hay información', 'message' => 'Los datos aparecerán aquí cuando estén disponibles.', 'icon' => 'bi-inbox'])
<div class="empty-state"><span><i class="bi {{ $icon }}"></i></span><h3>{{ $title }}</h3><p>{{ $message }}</p>{{ $slot }}</div>
