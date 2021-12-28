<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TODO swagger
 */
interface ScormTrackControllerContract
{
    public function set(Request $request, string $uuid): JsonResponse;

    public function get(Request $request, string $uuid): JsonResponse;
}
