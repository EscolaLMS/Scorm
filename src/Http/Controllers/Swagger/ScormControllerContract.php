<?php

namespace EscolaLms\Scorm\Http\Controllers\Swagger;

use EscolaLms\Pages\Http\Requests\PageDeleteRequest;
use EscolaLms\Pages\Http\Requests\PageCreateRequest;
use EscolaLms\Pages\Http\Requests\PageListingRequest;
use EscolaLms\Pages\Http\Requests\PageUpdateRequest;
use EscolaLms\Pages\Http\Requests\PageReadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

interface ScormControllerContract
{
    /**
     * @OA\Get(
     *     path="/api/pages",
     *     summary="Lists available pages",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="list of available pages",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of pages identified by a slug value",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/Page"
     *                )
     *            )
     *         )
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
     * @OA\Get(
     *     path="/api/admin/scorm/parse",
     *     summary="Read a page identified by a given slug identifier",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable page identifier",
     *         in="path",
     *         name="slug",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/Page")
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
     *     path="/api/admin/scorm/parse",
     *     summary="Read a page identified by a given slug identifier",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable page identifier",
     *         in="path",
     *         name="slug",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/Page")
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
    public function show(Request $request): Response;
}
