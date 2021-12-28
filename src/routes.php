<?php

use EscolaLms\Scorm\Http\Controllers\ScormController;

use EscolaLms\Scorm\Http\Controllers\ScormTrackController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/admin/scorm', 'middleware' => ['auth:api', 'bindings']], function () {
    Route::post('/upload', [ScormController::class, "upload"]);
    Route::post('/parse', [ScormController::class, "parse"]);
    Route::delete('/{scormModel}', [ScormController::class, "delete"]);
    Route::get('/', [ScormController::class, "index"]);
});

Route::group(['prefix' => 'api/scorm'], function () {
    Route::get('/play/{uuid}', [ScormController::class, "show"]);

    Route::group(['prefix' => '/track', 'middleware' => ['auth:api', 'bindings']], function () {
        Route::post('/{uuid}', [ScormTrackController::class, 'set']);
        Route::get('/{uuid}', [ScormTrackController::class, 'get']);
        Route::post('/commit/{uuid}', [ScormTrackController::class, 'commit']);
    });
});
