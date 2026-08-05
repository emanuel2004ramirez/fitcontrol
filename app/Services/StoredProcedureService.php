<?php

namespace App\Services;

use App\Support\Auditing\AuditTrail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

abstract class StoredProcedureService
{
    protected function paginateProcedures(string $countProcedure, string $listProcedure, array $parameters, int $perPage, int $page, array $query = []): LengthAwarePaginator
    {
        $page = max(1, $page);
        $perPage = max(1, min(200, $perPage));
        $total = (int) ($this->selectOne($countProcedure, $parameters)?->total ?? 0);
        $items = $this->select($listProcedure, [...$parameters, $perPage, ($page - 1) * $perPage]);

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $query,
        ]);
    }

    /** @return array<int, object> */
    protected function select(string $procedure, array $parameters = []): array
    {
        $result = DB::select($this->call($procedure, $parameters), $parameters);
        app(AuditTrail::class)->recordProcedure($procedure, $parameters, $result);

        return $result;
    }

    protected function selectOne(string $procedure, array $parameters = []): ?object
    {
        $result = DB::selectOne($this->call($procedure, $parameters), $parameters);
        app(AuditTrail::class)->recordProcedure($procedure, $parameters, $result);

        return $result;
    }

    protected function statement(string $procedure, array $parameters = []): bool
    {
        $result = DB::statement($this->call($procedure, $parameters), $parameters);
        app(AuditTrail::class)->recordProcedure($procedure, $parameters, $result);

        return $result;
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
