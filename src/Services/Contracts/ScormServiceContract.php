<?php


namespace EscolaLms\Scorm\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface ScormServiceContract
{
    public function uploadScormArchive(UploadedFile $file);
    public function removeRecursion($data);
    public function parseScormArchive(UploadedFile $file);
    public function deleteScormData($model);
    public function getScos($scormId);
    public function getScoByUuid($scoUuid);
    public function getUserResult($scoId, $userId);
    public function createScoTracking($scoUuid, $userId = null);
    public function findScoTrackingId($scoUuid, $scoTrackingUuid);
    public function checkUserIsCompletedScorm($scormId, $userId);
    public function updateScoTracking($scoUuid, $userId, $data);
}
