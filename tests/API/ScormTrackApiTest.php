<?php

namespace Tests\Feature;

use EscolaLms\Scorm\Tests\ScormTestTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Scorm\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;

class ScormTrackApiTest extends TestCase
{
    use DatabaseTransactions, ScormTestTrait, WithFaker;

    public function scormDataProvider(): array
    {
        return [
            'SCORM_12' => [
                'fileName' => 'RuntimeBasicCalls_SCORM12.zip',
                'payload' => [
                    'cmi' => [
                        'cmi.core.lesson_status' => 'passed',
                        'cmi.core.lesson_location' => '3',
                    ]
                ]
            ],
            'SCORM_2004' => [
                'fileName' => 'RuntimeBasicCalls_SCORM20043rdEdition.zip',
                'payload' => [
                    'cmi' => [
                        'cmi.success_status' => 'passed',
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
            'lesson_status' => 'passed',
            'lesson_location' => '3',
            'progression' => '100',
            'sco_id' => ScormScoModel::where('uuid', $scos->uuid)->first()->getKey(),
            'user_id' => $this->user->id
        ]);
    }

    public function scormGetTrackDataProvider(): array
    {
        return [
            'SCORM_12' => [
                'version' => Scorm::SCORM_12,
                'param' => [
                    'key' => 'cmi.core.lesson_location',
                    'value' => '3'
                ]
            ],
            'SCORM_2004' => [
                'version' => Scorm::SCORM_2004,
                'param' => [
                    'key' => 'cmi.location',
                    'value' => '3',
                ],
            ],
            'SCORM_12_INVALID' => [
                'version' => Scorm::SCORM_12,
                'param' => [
                    'key' => 'cmi.invalid_key',
                    'value' => null,
                ]
            ],
            'SCORM_2004_INVALID' => [
                'version' => Scorm::SCORM_2004,
                'param' => [
                    'key' => 'cmi.invalid_key',
                    'value' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider scormGetTrackDataProvider
     */
    public function test_get_track_scorm($version, $param)
    {
        $scorm = new ScormModel;
        $scorm->uuid = $this->faker->uuid;
        $scorm->version = $version;
        $scorm->save();

        $scormSco = new ScormScoModel;
        $scormSco->scorm_id = $scorm->getKey();
        $scormSco->uuid = $this->faker->uuid;
        $scormSco->save();

        $this->actingAs($this->user, 'api')
            ->json('POST', '/api/scorm/track/' . $scormSco->uuid, [
                'cmi' => [
                    $param['key'] => $param['value']
                ]
            ])
            ->assertStatus(200);

        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/scorm/track/' . $scormSco->getKey() . '/' . $param['key']);

        $response
            ->assertStatus(200)
            ->assertJsonFragment($param['value'] ? [$param['value']] : []);
    }

    public function test_get_track_not_existing_scorm()
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/scorm/track/' . 0 . '/' . 'cmi.location');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([]);
    }
}
