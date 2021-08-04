<?php

use EscolaLms\Scorm\Http\Controllers\ScormController;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/admin/scorm', 'middleware' => ['auth:api']], function () {
    Route::post('/upload', [ScormController::class, "upload"]);
    Route::post('/parse', [ScormController::class, "parse"]);
});

Route::group(['prefix' => 'api/scorm'], function () {
    Route::get('/play/{uuid}', [ScormController::class, "show"]);
});
