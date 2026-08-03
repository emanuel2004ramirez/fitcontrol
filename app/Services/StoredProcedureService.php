<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

abstract class StoredProcedureService
{
    /** @return array<int, object> */
    protected function select(string $procedure, array $parameters = []): array
    {
        return DB::select($this->call($procedure, $parameters), $parameters);
    }

    protected function selectOne(string $procedure, array $parameters = []): ?object
    {
        return DB::selectOne($this->call($procedure, $parameters), $parameters);
    }

    protected function statement(string $procedure, array $parameters = []): bool
    {
        return DB::statement($this->call($procedure, $parameters), $parameters);
    }

    private function call(string $procedure, array $parameters): string
    {
        if (! preg_match('/^sp_[a-z0-9_]+$/', $procedure)) {
            throw new InvalidArgumentException('Nombre de procedimiento inválido.');
        }

        $placeholders = implode(', ', array_fill(0, count($parameters), '?'));

        return "CALL {$procedure}({$placeholders})";
    }
}
