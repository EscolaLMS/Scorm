<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use EscolaLms\Scorm\Tests\TestCase;
use Illuminate\Testing\TestResponse;

class ScormAdminApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_content_upload()
    {
        $response = $this->uploadScorm();
        $data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals($data->data->scormData->scos[0]->title, "Employee Health and Wellness (Sample Course)");
    }

    public function test_content_upload_invalid_data()
    {
        $this->actingAs($this->user, 'api')
            ->json('POST', '/api/admin/scorm/upload', [
                'zip' => UploadedFile::fake()->create('file.zip', 100, 'application/zip'),
            ])->assertJson([
                'success' => false,
                'message' => "invalid_scorm_archive_message"
            ]);
    }

    public function test_content_upload_invalid_data_format()
    {
        $this->actingAs($this->user, 'api')
            ->json('POST', '/api/admin/scorm/upload', [
                'zip' => UploadedFile::fake()->create('file.svg', 100, 'application/svg'),
            ])->assertJson([
                'message' => 'The given data was invalid.',
            ]);
    }

    public function test_content_parse()
    {
        $zipFile = $this->getUploadScormFile();
        $response = $this->actingAs($this->user, 'api')->json('POST', '/api/admin/scorm/parse', [
            'zip' => $zipFile,
        ]);

        $data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals($data->data->scos[0]->title, "Employee Health and Wellness (Sample Course)");
    }

    public function test_get_model_list()
    {
        $response = $this->uploadScorm();
        $data = $response->getData();

        $response = $this->actingAs($this->user, 'api')->get('/api/admin/scorm');
        $list = $response->getData();

        $found = array_filter($list->data->data, function ($item) use ($data) {
            if ($item->uuid === $data->data->model->uuid) {
                return true;
            }
            return false;
        });

        $this->assertCount(1, $found);
    }

    public function test_player_view()
    {
        $response = $this->uploadScorm();
        $data = $response->getData();

        $response = $this->actingAs($this->user, 'api')->get('/api/scorm/play/' . $data->data->scormData->scos[0]->uuid);
        $response->assertStatus(200);
    }

    private function uploadScorm(): TestResponse
    {
        $zipFile = $this->getUploadScormFile();

        return $this->actingAs($this->user, 'api')->json('POST', '/api/admin/scorm/upload', [
            'zip' => $zipFile,
        ]);
    }

    private function getUploadScormFile(): UploadedFile
    {
        // packages/scorm/database/seeders/mocks/employee-health-and-wellness-sample-course-scorm12-Z_legM6C.zip
        $filename = '1.zip';
        $filepath = realpath(__DIR__ . '/../../database/mocks/' . $filename);
        $storage_path = storage_path($filename);

        copy($filepath, $storage_path);

        return new UploadedFile($storage_path, $filename, 'application/zip', null, true);
    }
}
