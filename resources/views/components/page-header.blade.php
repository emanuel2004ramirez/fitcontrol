@props(['title', 'subtitle' => null])
<div class="page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div><h1>{{ $title }}</h1>@if($subtitle)<p>{{ $subtitle }}</p>@endif</div>
    @if(trim($slot))<div class="page-actions d-flex gap-2">{{ $slot }}</div>@endif
</div>
