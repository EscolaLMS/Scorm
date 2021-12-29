<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use EscolaLms\Scorm\Http\Requests\GetScormTrackRequest;
use EscolaLms\Scorm\Http\Requests\ScormCreateRequest;
use EscolaLms\Scorm\Http\Requests\SetScormTrackRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ScormTrackControllerContract
{
    /**
     * @OA\Post(
     *     path="/api/scorm/track/{uuid}",
     *     summary="Track Scorm user progress",
     *     tags={"SCORM"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique uuid scorm identifier",
     *         in="path",
     *         name="uuid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="scorm data",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param SetScormTrackRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function set(SetScormTrackRequest $request, string $uuid): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/scorm/track/{scoId}/{key}",
     *     summary="Get user progress by scorm sco id and scorm data key",
     *     tags={"SCORM"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique scorm identifier",
     *         in="path",
     *         name="sco_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Unique scorm data key",
     *         in="path",
     *         name="key",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param GetScormTrackRequest $request
     * @param int $scoId
     * @param string $key
     * @return JsonResponse
     */
    public function get(GetScormTrackRequest $request, int $scoId, string $key): JsonResponse;
}
