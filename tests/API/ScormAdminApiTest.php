<?php

namespace Tests\Feature;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Scorm\Tests\ScormTestTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use EscolaLms\Scorm\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;

class ScormAdminApiTest extends TestCase
{
    use DatabaseTransactions, ScormTestTrait, WithFaker, CreatesUsers;

    public function test_content_upload(): void
    {
        $response = $this->uploadScorm();
        $data = $response->getData();

        $response->assertStatus(200);
        $this->assertEquals($data->data->scormData->scos[0]->title, "Employee Health and Wellness (Sample Course)");
        $this->assertEquals($this->user->getKey(), $data->data->model->user_id);
    }

    public function test_content_upload_invalid_data(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/admin/scorm/upload', [
                'zip' => UploadedFile::fake()->create('file.zip', 100, 'application/zip'),
            ]);

        $response->assertUnprocessable();
        $response->assertJson([
            'success' => false,
            'message' => 'invalid_scorm_archive_message'
        ]);
    }

    public function test_content_upload_invalid_data_format(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('POST', '/api/admin/scorm/upload', [
                'zip' => UploadedFile::fake()->create('file.svg', 100, 'application/svg'),
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['zip' => 'The zip must be a file of type: zip.']);
    }

    public function test_content_parse(): void
    {
        $zipFile = $this->getUploadScormFile();
        $response = $this->actingAs($this->user, 'api')->json('POST', '/api/admin/scorm/parse', [
            'zip' => $zipFile,
        ]);

        $data = $response->getData();

        $response->assertOk();
        $this->assertEquals($data->data->scos[0]->title, "Employee Health and Wellness (Sample Course)");
    }

    public function test_delete_scorm(): void
    {
        $response = $this->uploadScorm();
        $data = $response->getData();
        $scormData = $data->data->scormData;
        $model = $data->data->model;
        $path = 'scorm' . DIRECTORY_SEPARATOR . $scormData->version . DIRECTORY_SEPARATOR . $scormData->hashName;

        $response = $this->actingAs($this->user, 'api')->json('DELETE', '/api/admin/scorm/' . $model->id);

        $response->assertOk();
        $this->assertFalse(Storage::disk(config('scorm.disk'))->exists($path));
        $this->assertDatabaseMissing('scorm', [
            'id' => $model->id,
            'uuid' => $model->uuid,
        ]);
        $this->assertDatabaseMissing('scorm_sco', [
            'uuid' => $scormData->scos[0]->uuid,
        ]);
    }

    public function test_delete_owned_scorm(): void
    {
        $tutor = $this->makeInstructor();
        $scorm = $this->createScorm();

        $this->actingAs($tutor, 'api')
            ->deleteJson('/api/admin/scorm/' . $scorm->getKey())
            ->assertForbidden();

        $scorm->user_id = $tutor->getKey();
        $scorm->save();

        $this->actingAs($tutor, 'api')
            ->deleteJson('/api/admin/scorm/' . $scorm->getKey())
            ->assertOk();

        $this->assertDatabaseMissing('scorm', [
            'id' => $scorm->id,
        ]);
    }

    public function test_get_model_list_paginated(): void
    {
        $this->createManyScorm(10);

        $this->actingAs($this->user, 'api')->get('/api/admin/scorm?per_page=5')
            ->assertOk()
            ->assertJsonCount(5, 'data.data');
    }

    public function test_get_owned_scorm_list_paginated(): void
    {
        $this->createManyScorm(10);
        $owner = $this->makeInstructor();
        $this->createManyScorm(5)->each(function (ScormModel $scorm) use ($owner) {
            $scorm->user_id = $owner->getKey();
            $scorm->save();
        });

        $this->actingAs($owner, 'api')->get('/api/admin/scorm?per_page=15')
            ->assertOk()
            ->assertJsonCount(5, 'data.data')
            ->assertJsonPath('data.total', 5);
    }

    public function test_get_model_list_unpaginated(): void
    {
        $this->createManyScorm(30);

        $this->actingAs($this->user, 'api')->get('/api/admin/scorm?per_page=0')
            ->assertOk()
            ->assertJsonCount(30, 'data.data');
    }

    public function test_get_model_list_with_sorts(): void
    {
        $scormOne = $this->createScorm();
        $scormOne->version = Scorm::SCORM_12;
        $scormOne->save();

        $scormTwo = $this->createScorm();
        $scormTwo->version = Scorm::SCORM_2004;
        $scormTwo->save();


        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/scorm', [
            'order_by' => 'version',
            'order' => 'ASC',
        ]);

        $this->assertTrue($response->getData()->data->data[0]->version === Scorm::SCORM_12);
        $this->assertTrue($response->getData()->data->data[1]->version === Scorm::SCORM_2004);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/scorm', [
            'order_by' => 'version',
            'order' => 'DESC',
        ]);

        $this->assertTrue($response->getData()->data->data[0]->version === Scorm::SCORM_2004);
        $this->assertTrue($response->getData()->data->data[1]->version === Scorm::SCORM_12);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/scorm', [
            'order_by' => 'id',
            'order' => 'ASC',
        ]);

        $this->assertTrue($response->getData()->data->data[0]->id === $scormOne->getKey());
        $this->assertTrue($response->getData()->data->data[1]->id === $scormTwo->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/scorm', [
            'order_by' => 'id',
            'order' => 'DESC',
        ]);

        $this->assertTrue($response->getData()->data->data[0]->id === $scormTwo->getKey());
        $this->assertTrue($response->getData()->data->data[1]->id === $scormOne->getKey());

    }

    public function test_search_model_list(): void
    {
        $scormSco = $this->createManyScos(10)[3];

        $response = $this->actingAs($this->user, 'api')->get('/api/admin/scorm?per_page=5&search=' . $scormSco->title)
            ->assertOk()
            ->assertJsonCount(1, 'data.data.0.scos')
            ->assertJsonCount(1, 'data.data');

        $data = $response->getData()->data->data;
        $this->assertEquals($data[0]->scos[0]->title, $scormSco->title);
        $this->assertEquals($data[0]->scos[0]->uuid, $scormSco->uuid);
    }

    public function test_get_model_list(): void
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

    public function test_get_scos_list()
    {
        $scormSco = new ScormScoModel;
        $scormSco->uuid = $this->faker->uuid;
        $scormSco->save();

        $scormSco = new ScormScoModel;
        $scormSco->uuid = $this->faker->uuid;
        $scormSco->save();

        $this->actingAs($this->user, 'api')->get('/api/admin/scorm/scos')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'scorm_id',
                    'uuid',
                    'entry_url',
                    'identifier',
                    'title',
                    'sco_parameters',
                ]]
            ]);

    }

    public function test_player_view(): void
    {
        $response = $this->uploadScorm();
        $data = $response->getData();

        $response = $this->actingAs($this->user, 'api')->get('/api/scorm/play/' . $data->data->scormData->scos[0]->uuid);
        $response->assertOk();
    }
}
