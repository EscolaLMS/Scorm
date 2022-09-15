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

    public function test_zip_scorm_when_zip_not_exists(): void
    {
        $scorm = $this->scormService->uploadScormArchive($this->getUploadScormFile('RuntimeBasicCalls_SCORM20043rdEdition.zip'));
        $scormModel = $scorm['model'];
        $scormPath = 'scorm' . DIRECTORY_SEPARATOR . $scormModel->version . DIRECTORY_SEPARATOR . $scormModel->hash_name;
        $scormFilePath =  $scormPath . DIRECTORY_SEPARATOR . $scormModel->origin_file;

        Storage::assertExists($scormFilePath);
        Storage::delete($scormFilePath);
        Storage::assertMissing($scormFilePath);

        $path = $this->scormService->zipScorm($scorm['model']->id);
        Storage::assertExists($path);
    }

    public function test_scorm_player_with_passed_user(): void
    {
        $user = config('auth.providers.users.model')::factory()->create();
        $token = $user->createToken("Token")->accessToken;
        $scormSco = $this->createScormSco();

        $result = $this->scormService->getScoViewDataByUuid($scormSco->uuid, $user->getKey(), $token)->toArray();

        $this->assertArrayHasKey('player', $result);
        $this->assertTrue($result['player']->autoCommit);
        $this->assertNotFalse($result['player']->lmsCommitUrl);
        $this->assertNotNull($result['player']->xhrHeaders['Authorization']);
        $this->assertTrue($result['player']->autoProgress);
    }

    public function test_scorm_player_preview_mode(): void
    {
        $scormSco = $this->createScormSco();

        $result = $this->scormService->getScoViewDataByUuid($scormSco->uuid)->toArray();

        $this->assertArrayHasKey('player', $result);
        $this->assertFalse($result['player']->autoCommit);
        $this->assertFalse($result['player']->lmsCommitUrl);
        $this->assertNull($result['player']->xhrHeaders['Authorization']);
        $this->assertFalse($result['player']->autoProgress);
    }
}
