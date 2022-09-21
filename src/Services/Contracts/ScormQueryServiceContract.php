<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ScormQueryServiceContract
{
    public function get($per_page = 15, array $columns = ['*'], ?array $search = []);
    public function paginated($per_page = 15, array $columns = ['*'], ?array $search = []): LengthAwarePaginator;
    public function all(array $columns = ['*'], ?array $search = []): Collection;
    public function allScos(array $columns = ['*']): Collection;
}
