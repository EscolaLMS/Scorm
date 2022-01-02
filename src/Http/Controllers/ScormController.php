<?php

namespace EscolaLms\Scorm\Http\Controllers;

use EscolaLms\Scorm\Http\Controllers\Swagger\ScormControllerContract;
use EscolaLms\Scorm\Http\Requests\ScormDeleteRequest;
use EscolaLms\Scorm\Http\Requests\ScormReadRequest;
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
    private ScormServiceContract $service;

    public function __construct(ScormServiceContract $service)
    {
        $this->service = $service;
    }

    public function upload(ScormCreateRequest $request): JsonResponse
    {
        $file = $request->file('zip');

        try {
            $data = $this->service->uploadScormArchive($file);
            $data = $this->service->removeRecursion($data);
        } catch (Exception $error) {
            return $this->sendError($error->getMessage());
        }
        return $this->sendResponse($data, "Scorm Package uploaded successfully");
    }

    public function parse(ScormCreateRequest $request): JsonResponse
    {
        $file = $request->file('zip');

        try {
            $data = $this->service->parseScormArchive($file);
            $data = $this->service->removeRecursion($data);
        } catch (Exception $error) {
            $this->sendError($error->getMessage());
        }
        return $this->sendResponse($data, "Scorm Package uploaded successfully");
    }

    public function show(string $uuid, ScormReadRequest $request): View
    {
        $data = $this->service->getScoViewDataByUuid($uuid);
        return view('scorm::player', ['data' => $data]);
    }

    public function index(ScormListRequest $request): JsonResponse
    {
        $list = $this->service->listModels($request->get('per_page'));
        return $this->sendResponse($list, "Scorm list fetched successfully");
    }

    public function delete(ScormDeleteRequest $request, ScormModel $scormModel): JsonResponse
    {
        $this->service->deleteScormData($scormModel);
        return $this->sendSuccess("Scorm Package deleted successfully");
    }
}
