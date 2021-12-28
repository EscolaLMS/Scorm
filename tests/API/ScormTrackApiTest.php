<?php

namespace Tests\Feature;

use EscolaLms\Scorm\Tests\ScormTestTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Scorm\Tests\TestCase;
use Peopleaps\Scorm\Model\ScormScoModel;

class ScormTrackApiTest extends TestCase
{
    use DatabaseTransactions, ScormTestTrait;

    public function scormDataProvider(): array
    {
        return [
            'SCORM_12' => [
                'fileName' => 'RuntimeBasicCalls_SCORM12.zip',
                'payload' => [
                    'cmi' => [
                        'cmi.core.lesson_status' => 'completed',
                        'cmi.core.lesson_location' => '3',
                    ]
                ]
            ],
            'SCORM_2004' => [
                'fileName' => 'RuntimeBasicCalls_SCORM20043rdEdition.zip',
                'payload' => [
                    'cmi' => [
                        'cmi.completion_status' => 'completed',
                        'cmi.location' => '3',
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider scormDataProvider
     */
    public function test_set_track_scorm($fileName, $payload)
    {
        $this->authenticateAsAdmin();
        $response = $this->uploadScorm($fileName);
        $data = $response->getData();
        $scos = $data->data->scormData->scos[0];

        $this->actingAs($this->user, 'api')
            ->json('POST', '/api/scorm/track/' . $scos->uuid, $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('scorm_sco_tracking', [
            'lesson_status' => 'completed',
            'lesson_location' => '3',
            'progression' => '100',
            'sco_id' => ScormScoModel::where('uuid', $scos->uuid)->first()->getKey(),
            'user_id' => $this->user->id
        ]);
    }

    public function test_get_track_scorm()
    {
    }
}
