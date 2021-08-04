<?php

namespace EscolaLms\Scorm\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;
use Peopleaps\Scorm\Model\ScormScoTrackingModel;
use Illuminate\Support\Facades\Storage;
use EscolaLms\Scorm\Http\Controllers\Contracts\ScormControllerContract;
use EscolaLms\Scorm\Http\Services\Contracts\ScormServiceContract;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use Exception;
use EscolaLms\Scorm\Http\Requests\ScormCreateRequest;
use Illuminate\Http\JsonResponse;

class ScormController extends EscolaLmsBaseController implements ScormControllerContract
{
    private ScormServiceContract $service;

    public function __construct(ScormServiceContract $service)
    {
        $this->service = new ScormServiceContract();
    }

    public function upload(ScormCreateRequest $request):JsonResponse
    {
        $file = $request->file('zip');

        try {
            $data = $this->service->uploadScormArchive($file);
            $data = $this->service->removeRecursion($data);
        } catch (Exception $error) {
            $this->sendError($error->getMessage());
        }
        return $this->sendResponse($data, "Scorm Package uploaded successfully");
    }

    public function parse(ScormCreateRequest $request):JsonResponse
    {
        $file = $request->file('zip');
        $data = $this->service->parseScormArchive($file);
        $data = $this->service->removeRecursion($data);
        return response()->json($data);
    }

    public function show(string $uuid, Request $request):Response
    {
        $data = $this->service->getScoByUuid($uuid);
        $data['entry_url_absolute'] = Storage::url('scorm/'.$data->scorm->version.'/'.$data->scorm->uuid.'/'.$data->entry_url);

        $data['player'] = (object) [
            'lmsCommitUrl' => '/api/lms',
            'logLevel'=> 1,
            'autoProgress' => true,
            'cmi' => [] // cmi is user progress
        ];
        return view('scorm', ['data' => $data]);
    }
}
