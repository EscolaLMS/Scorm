<?php

namespace EscolaLms\Scorm\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Scorm\Http\Controllers\Swagger\ScormTrackControllerContract;
use EscolaLms\Scorm\Http\Requests\GetScormTrackRequest;
use EscolaLms\Scorm\Http\Requests\SetScormTrackRequest;
use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScormTrackController extends EscolaLmsBaseController implements ScormTrackControllerContract
{
    /** @var ScormTrackServiceContract */
    private ScormTrackServiceContract $scormTrackService;

    public function __construct(ScormTrackServiceContract $scormTrackService)
    {
        $this->scormTrackService = $scormTrackService;
    }

    public function set(SetScormTrackRequest $request, string $uuid): JsonResponse
    {
        $this->scormTrackService->updateScoTracking(
            $uuid,
            $request->user()->getKey(),
            $request->input('cmi')
        );

        return $this->sendSuccess();
    }

    public function get(GetScormTrackRequest $request, int $scoId, string $key): JsonResponse
    {
        $data = $this->scormTrackService->getUserResultSpecifiedValue($key, $scoId, $request->user()->getKey());
        return new JsonResponse($data);
    }
}
