@foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $type)
    @if (session($key))
        <div class="alert alert-{{ $type }} alert-dismissible fade show app-alert" role="alert">
            <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'exclamation-octagon' : 'info-circle') }} me-2"></i>
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
@endforeach

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
        <div class="d-flex"><i class="bi bi-exclamation-octagon me-2"></i><div><strong>Revisa la información ingresada.</strong><ul class="mb-0 mt-1 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
