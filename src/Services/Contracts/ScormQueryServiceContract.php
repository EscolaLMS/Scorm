<?php


namespace EscolaLms\Scorm\Services\Contracts;

use EscolaLms\Core\Dtos\OrderDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ScormQueryServiceContract
{
    public function get($per_page = 15, array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null);
    public function paginated($per_page = 15, array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null): LengthAwarePaginator;
    public function all(array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null): Collection;
    public function allScos(array $columns = ['*']): Collection;
}
