<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use EscolaLms\Scorm\Http\Requests\ScormCreateRequest;
use EscolaLms\Scorm\Http\Requests\ScormListRequest;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

interface ScormControllerContract
{
    /**
     * @OA\Post(
     *     path="/api/admin/scorm/upload",
     *     summary="Convert ZIP Scorm Package into Escola LMS Scorm storage",
     *     tags={"SCORM"},
     *     security={
     *         {"passport": {}},
     *     },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="zip",
     *                      type="string",
     *                      format="binary"
     *                  )
     *              )
     *          )
     *      ),
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
     * @param ScormCreateRequest $request
     * @return JsonResponse
     */
    public function upload(ScormCreateRequest $request): JsonResponse;





    /**
     * @OA\Post(
     *     path="/api/admin/scorm/parse",
     *     summary="Parse ZIP Scorm to see if it's valid",
     *     tags={"SCORM"},
     *     security={
     *         {"passport": {}},
     *     },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="zip",
     *                      type="string",
     *                      format="binary"
     *                  )
     *              )
     *          )
     *      ),     
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
     * @param ScormCreateRequest $request
     * @return JsonResponse
     */
    public function parse(ScormCreateRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/scorm/play/{uuid}",
     *     summary="Read a page identified by a given slug identifier",
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
     * @param Request $request
     * @return Response
     */
    public function show(string $uuid, Request $request):View;

    /**
     * @OA\Get(
     *     path="/api/admin/scorm/",
     *     summary="Read a page identified by a given slug identifier",
     *     tags={"SCORM"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="per_page",
     *         in="query",
     *         name="per_page",
     *         @OA\Schema(
     *             type="number"
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
     * @param Request $request
     * @return ScormListRequest
     */
    public function index(ScormListRequest $request):JsonResponse;
}
