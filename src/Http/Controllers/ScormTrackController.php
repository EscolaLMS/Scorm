<?php

namespace EscolaLms\Scorm\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Scorm\Http\Controllers\Swagger\ScormTrackControllerContract;
use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Peopleaps\Scorm\Model\ScormScoModel;

class ScormTrackController extends EscolaLmsBaseController implements ScormTrackControllerContract
{
    /** @var ScormTrackServiceContract */
    private ScormTrackServiceContract $scormTrackService;

    public function __construct(ScormTrackServiceContract $scormTrackService)
    {
        $this->scormTrackService = $scormTrackService;
    }

    public function set(Request $request, ScormScoModel $scormSco): JsonResponse
    {
        // TODO
        return $this->sendSuccess();
    }

    public function get(Request $request, ScormScoModel $scormSco): JsonResponse
    {
        // TODO
        return new JsonResponse();
    }

    public function commit(Request $request): JsonResponse
    {
        // TODO
        return new JsonResponse();
    }
}
