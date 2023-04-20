<?php

namespace EscolaLms\Scorm\Services;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Scorm\Repositories\Contracts\ScormRepositoryContract;
use EscolaLms\Scorm\Services\Contracts\ScormQueryServiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Peopleaps\Scorm\Model\ScormScoModel;

class ScormQueryService implements ScormQueryServiceContract
{
    private ScormRepositoryContract $scormRepository;

    public function __construct(ScormRepositoryContract $scormRepository)
    {
        $this->scormRepository = $scormRepository;
    }

    public function get($per_page = 15, array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null)
    {
        return $per_page === null || $per_page === 0
            ? ['data' => $this->all($columns, $search, $orderDto)]
            : $this->paginated(intval($per_page), $columns, $search, $orderDto);
    }

    public function paginated($per_page = 15, array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null): LengthAwarePaginator
    {
        return $this->scormRepository
            ->listQuery($columns, $search)
            ->orderBy($orderDto?->getOrderBy() ?? 'id', $orderDto?->getOrder() ?? 'desc')
            ->paginate(intval($per_page));
    }

    public function all(array $columns = ['*'], ?array $search = [], ?OrderDto $orderDto = null): Collection
    {
        return $this->scormRepository
            ->listQuery($columns, $search)
            ->orderBy($orderDto?->getOrderBy() ?? 'id', $orderDto?->getOrder() ?? 'desc')
            ->get();
    }

    public function allScos(array $columns = ['*']): Collection
    {
        return ScormScoModel::query()
            ->select($columns)
            ->get();
    }
}
