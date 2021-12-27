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
        Route::post('/{scormSco}', [ScormTrackController::class, 'set']); // TODO not implemented
        Route::get('/{scormSco}', [ScormTrackController::class, 'get']); // TODO not implemented
        Route::post('/commit', [ScormTrackController::class, 'commit']); // TODO not implemented
    });
});
