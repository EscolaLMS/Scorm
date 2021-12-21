<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface ScormServiceContract
{
    public function uploadScormArchive(UploadedFile $file);
    public function removeRecursion($data);
    public function parseScormArchive(UploadedFile $file);
    public function deleteScormData($model);
    public function getScos($scormId);
    public function getScoByUuid($scoUuid);
    public function getScoViewDataByUuid($scoUuid): array;
    public function listModels($per_page = 15, array $columns = ['*']): LengthAwarePaginator;
}
