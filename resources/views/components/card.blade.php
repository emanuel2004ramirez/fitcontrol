@props(['title' => null, 'subtitle' => null, 'class' => ''])
<section {{ $attributes->merge(['class' => "card app-card {$class}"]) }}>
    @if($title || isset($actions))<div class="card-header"><div>@if($title)<h2>{{ $title }}</h2>@endif @if($subtitle)<p>{{ $subtitle }}</p>@endif</div>@isset($actions)<div>{{ $actions }}</div>@endisset</div>@endif
    <div class="card-body">{{ $slot }}</div>
</section>
