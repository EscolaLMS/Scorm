<?php

namespace EscolaLms\Scorm;

use EscolaLms\Scorm\Services\Contracts\ScormServiceContract;
use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use EscolaLms\Scorm\Services\ScormService;

use EscolaLms\Scorm\Services\ScormTrackService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * SWAGGER_VERSION
 */


class EscolaLmsScormServiceProvider extends ServiceProvider
{
    public $singletons = [
        ScormServiceContract::class => ScormService::class,
        ScormTrackServiceContract::class => ScormTrackService::class,
    ];

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'scorm');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/scorm'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
