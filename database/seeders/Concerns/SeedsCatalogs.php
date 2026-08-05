<?php

namespace Database\Seeders\Concerns;

use App\Services\CatalogoService;

trait SeedsCatalogs
{
    protected function seedCatalog(string $catalog, array $rows): void
    {
        $service = app(CatalogoService::class);
        $existing = collect($service->listar($catalog, limite: 500))->keyBy('codigo');

        foreach ($rows as $row) {
            $record = $existing->get($row['codigo']);
            $record === null
                ? $service->crear($catalog, $row)
                : $service->actualizar($catalog, (int) $record->id, $row);
        }
    }
}
