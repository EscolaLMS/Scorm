<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;

interface ScormServiceContract
{
    public function uploadScormArchive(UploadedFile $file): array;
    public function removeRecursion(array $data): array;
    public function parseScormArchive(UploadedFile $file);
    public function deleteScormData(ScormModel $model): void;
    public function getScos($scormId): ScormScoModel;
    public function getScoByUuid($scoUuid): ScormScoModel;
    public function getScoViewDataByUuid(string $scoUuid, ?int $userId = null, ?string $token = null): ScormScoModel;
    public function listModelsPaginated($per_page = 15, array $columns = ['*']): LengthAwarePaginator;
    public function listModels(array $columns = ['*']): Collection;
    public function listScoModels(array $columns = ['*']): Collection;
    public function zipScorm(int $id): string;
}
