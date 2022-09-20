<?php

namespace EscolaLms\Scorm\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;

trait ScormTestTrait
{
    use WithFaker;

    protected function uploadScorm($fileName = '1.zip'): TestResponse
    {
        $zipFile = $this->getUploadScormFile($fileName);

        return $this->actingAs($this->user, 'api')->json('POST', '/api/admin/scorm/upload', [
            'zip' => $zipFile,
        ]);
    }

    protected function getUploadScormFile($fileName = '1.zip'): UploadedFile
    {
        // packages/scorm/database/seeders/mocks/employee-health-and-wellness-sample-course-scorm12-Z_legM6C.zip
        $filepath = realpath(__DIR__ . '/../database/mocks/' . $fileName);
        $storagePath = storage_path($fileName);

        copy($filepath, $storagePath);

        return new UploadedFile($storagePath, $fileName, 'application/zip', null, true);
    }

    protected function createManyScorm(int $count): Collection
    {
        return Collection::times($count, fn() => $this->createScorm());
    }

    protected function createScorm(): ScormModel
    {
        $versions = [Scorm::SCORM_12, Scorm::SCORM_2004];
        $uuid = $this->faker->uuid;

        $scorm = new ScormModel();
        $scorm->version = $this->faker->randomElement($versions);
        $scorm->hash_name = $uuid;
        $scorm->origin_file = $uuid . '.zip';
        $scorm->origin_file_mime = 'application/zip';
        $scorm->uuid = $uuid;
        $scorm->save();

        return $scorm;
    }

    protected function createManyScos(int $count): Collection
    {
        return Collection::times($count, fn() => $this->createScormSco());
    }

    protected function createScormSco(): ScormScoModel
    {
        $scormSco = new ScormScoModel();
        $scormSco->uuid = $this->faker->uuid;
        $scormSco->scorm_id = $this->createScorm()->getKey();
        $scormSco->entry_url = $this->faker->url;
        $scormSco->identifier = $this->faker->word;
        $scormSco->title = $this->faker->words(3, true);
        $scormSco->visible = 1;
        $scormSco->block = 0;
        $scormSco->save();

        return $scormSco;
    }
}
