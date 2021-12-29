<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use EscolaLms\Scorm\Http\Requests\GetScormTrackRequest;
use EscolaLms\Scorm\Http\Requests\SetScormTrackRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TODO swagger
 */
interface ScormTrackControllerContract
{
    public function set(SetScormTrackRequest $request, string $uuid): JsonResponse;

    public function get(GetScormTrackRequest $request, int $scoId, string $key): JsonResponse;
}
