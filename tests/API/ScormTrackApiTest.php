<?php

namespace Tests\Feature;

use EscolaLms\Scorm\Tests\ScormTestTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Scorm\Tests\TestCase;

class ScormTrackApiTest extends TestCase
{
    use DatabaseTransactions, ScormTestTrait;

    public function scormDataProvider(): array
    {
        return [];
    }

    public function test_set_track_scorm()
    {
    }

    public function test_get_track_scorm()
    {
    }

    public function test_commit_scorm()
    {
    }
}
