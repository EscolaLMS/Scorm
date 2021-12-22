<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Peopleaps\Scorm\Model\ScormScoModel;

interface ScormServiceContract
{
    public function uploadScormArchive(UploadedFile $file): array;
    public function removeRecursion($data);
    public function parseScormArchive(UploadedFile $file);
    public function deleteScormData($model);
    public function getScos($scormId);
    public function getScoByUuid($scoUuid);
    public function getScoViewDataByUuid($scoUuid): ScormScoModel;
    public function listModels($per_page = 15, array $columns = ['*']): LengthAwarePaginator;
}
