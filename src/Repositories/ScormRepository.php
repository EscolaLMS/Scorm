<?php
namespace EscolaLms\Scorm\Repositories;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Scorm\Repositories\Contracts\ScormRepositoryContract;
use Illuminate\Support\Facades\DB;
use PDO;
use Peopleaps\Scorm\Model\ScormModel;
use Illuminate\Database\Eloquent\Builder;

class ScormRepository extends BaseRepository implements ScormRepositoryContract
{
    public function getFieldsSearchable(): array
    {
        return [];
    }

    public function model(): string
    {
        return ScormModel::class;
    }

    public function listQuery(?array $columns = ['*'], ?array $search = []): Builder
    {
        return $this->model
            ->newQuery()
            ->with(['scos' => fn($query) => $query
                ->select(['*'])
                ->where('block', '=', 0)
                ->when(isset($search['search']), fn($query) => $query
                    ->where('title', $this->likeOperator(), '%' . $search['search'] . '%')
                )
            ])
            ->select($columns)
            ->when(isset($search['search']), fn($query) => $query
                ->whereHas('scos', fn($query) => $query->where('title', $this->likeOperator(), '%' . $search['search'] . '%'))
            )
            ->when(isset($search['user_id']), fn($query) => $query->where('user_id', $search['user_id']));
    }

    private function likeOperator(): string
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql' ? 'ILIKE' : 'LIKE';
    }
}
