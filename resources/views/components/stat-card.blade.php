@props(['label', 'value' => '—', 'icon', 'tone' => 'primary', 'trend' => null])
<div class="card stat-card h-100"><div class="card-body"><div class="stat-icon stat-{{ $tone }}"><i class="bi {{ $icon }}"></i></div><div class="stat-content"><span>{{ $label }}</span><strong>{{ $value }}</strong>@if($trend)<small><i class="bi bi-arrow-up-right"></i>{{ $trend }}</small>@endif</div></div></div>
