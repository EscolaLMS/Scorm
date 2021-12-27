<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Peopleaps\Scorm\Model\ScormScoModel;

interface ScormTrackControllerContract
{
    public function set(Request $request, ScormScoModel $scormSco): JsonResponse;

    public function get(Request $request, ScormScoModel $scormSco): JsonResponse;

    public function commit(Request $request): JsonResponse;
}
