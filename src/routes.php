<?php

use EscolaLms\Scorm\Http\Controllers\ScormController;

use EscolaLms\Scorm\Http\Controllers\ScormTrackController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/admin/scorm', 'middleware' => ['auth:api', SubstituteBindings::class]], function () {
    Route::post('/upload', [ScormController::class, "upload"]);
    Route::post('/parse', [ScormController::class, "parse"]);
    Route::delete('/{scormModel}', [ScormController::class, "delete"]);
    Route::get('/', [ScormController::class, "index"]);
    Route::get('/scos', [ScormController::class, "getScos"]);
});

Route::group(['prefix' => 'api/scorm'], function () {
    Route::get('/play/{uuid}', [ScormController::class, "show"]);

    Route::group(['prefix' => '/track', 'middleware' => ['auth:api', SubstituteBindings::class]], function () {
        Route::post('/{uuid}', [ScormTrackController::class, 'set']);
        Route::get('/{scoId}/{key}', [ScormTrackController::class, 'get']);
    });
});
