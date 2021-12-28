<?php

namespace EscolaLms\Scorm\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;

trait ScormTestTrait
{
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
        $filepath = realpath(__DIR__ . '/../../database/mocks/' . $fileName);
        $storagePath = storage_path($fileName);

        copy($filepath, $storagePath);

        return new UploadedFile($storagePath, $fileName, 'application/zip', null, true);
    }
}
