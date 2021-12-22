<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface ScormTrackServiceContract
{
    public function getUserResult($scoId, $userId);
    public function createScoTracking($scoUuid, $userId = null);
    public function findScoTrackingId($scoUuid, $scoTrackingUuid);
    public function checkUserIsCompletedScorm($scormId, $userId);
    public function updateScoTracking($scoUuid, $userId, $data);
}
