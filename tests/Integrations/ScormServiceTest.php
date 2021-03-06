<?php

use EscolaLms\Scorm\Services\Contracts\ScormServiceContract;
use EscolaLms\Scorm\Tests\ScormTestTrait;
use EscolaLms\Scorm\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class ScormServiceTest extends TestCase
{
    use ScormTestTrait;

    /** @var ScormServiceContract $service */
    private ScormServiceContract $scormService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scormService = app(ScormServiceContract::class);
    }

    public function test_zip_scorm(): void
    {
        $scorm = $this->scormService->uploadScormArchive($this->getUploadScormFile('RuntimeBasicCalls_SCORM20043rdEdition.zip'));
        $path = $this->scormService->zipScorm($scorm['model']->id);
        Storage::assertExists($path);
    }
}
