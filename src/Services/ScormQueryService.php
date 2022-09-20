<?php

namespace EscolaLms\Scorm\Services;

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

    public function get($per_page = 15, array $columns = ['*'], ?array $search = [])
    {
        return $per_page === null || $per_page === 0
            ? ['data' => $this->all($columns, $search)]
            : $this->paginated(intval($per_page), $columns, $search);
    }

    public function paginated($per_page = 15, array $columns = ['*'], ?array $search = []): LengthAwarePaginator
    {
        return $this->scormRepository
            ->listQuery($columns, $search)
            ->paginate(intval($per_page));
    }

    public function all(array $columns = ['*'], ?array $search = []): Collection
    {
        return $this->scormRepository
            ->listQuery($columns, $search)
            ->get();
    }

    public function allScos(array $columns = ['*']): Collection
    {
        return ScormScoModel::query()
            ->select($columns)
            ->get();
    }
}
