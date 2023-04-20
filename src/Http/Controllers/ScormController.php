<?php

namespace EscolaLms\Scorm\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Scorm\Http\Controllers\Swagger\ScormControllerContract;
use EscolaLms\Scorm\Http\Requests\ScormDeleteRequest;
use EscolaLms\Scorm\Services\Contracts\ScormQueryServiceContract;
use EscolaLms\Scorm\Services\Contracts\ScormServiceContract;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use Exception;
use EscolaLms\Scorm\Http\Requests\ScormCreateRequest;
use EscolaLms\Scorm\Http\Requests\ScormListRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Peopleaps\Scorm\Model\ScormModel;

class ScormController extends EscolaLmsBaseController implements ScormControllerContract
{
    private ScormServiceContract $scormService;

    private ScormQueryServiceContract $scormQueryService;

    public function __construct(
        ScormServiceContract $scormService,
        ScormQueryServiceContract $scormQueryService
    )
    {
        $this->scormService = $scormService;
        $this->scormQueryService = $scormQueryService;
    }

    public function upload(ScormCreateRequest $request): JsonResponse
    {
        $file = $request->file('zip');

        try {
            $data = $this->scormService->uploadScormArchive($file);
            $data = $this->scormService->removeRecursion($data);
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), 422);
        }

        return $this->sendResponse($data, "Scorm Package uploaded successfully");
    }

    public function parse(ScormCreateRequest $request): JsonResponse
    {
        $file = $request->file('zip');

        try {
            $data = $this->scormService->parseScormArchive($file);
            $data = $this->scormService->removeRecursion($data);
        } catch (Exception $error) {
            $this->sendError($error->getMessage(), 422);
        }

        return $this->sendResponse($data, "Scorm Package uploaded successfully");
    }

    public function show(string $uuid, Request $request): View
    {
        $data = $this->scormService->getScoViewDataByUuid(
            $uuid,
            $request->user() ? $request->user()->getKey() : null,
            $request->bearerToken()
        );

        return view('scorm::player', ['data' => $data]);
    }

    public function index(ScormListRequest $request): JsonResponse
    {
        $list = $this->scormQueryService->get($request->pageParams(), ['*'], $request->searchParams(), OrderDto::instantiateFromRequest($request));
        return $this->sendResponse($list, "Scorm list fetched successfully");
    }

    public function getScos(ScormListRequest $request): JsonResponse
    {
        $columns = [
            "id",
            "scorm_id",
            "uuid",
            "entry_url",
            "identifier",
            "title",
            "sco_parameters"
        ];

        $list = $this->scormQueryService->allScos($columns);
        return $this->sendResponse($list, "Scos list fetched successfully");
    }

    public function delete(ScormDeleteRequest $request, ScormModel $scormModel): JsonResponse
    {
        $this->scormService->deleteScormData($scormModel);
        return $this->sendSuccess("Scorm Package deleted successfully");
    }
}
