<?php

namespace EscolaLms\Pages\Tests\Api;

use EscolaLms\Pages\Models\Page;
use EscolaLms\Pages\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PagesReadTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(string $slug): string
    {
        return sprintf('/api/pages/%s', $slug);
    }

    public function testCanReadExisting()
    {
        $page = Page::factory()->createOne();

        $response = $this->getJson($this->uri($page->slug));
        $response->assertOk();
        $response->assertJsonFragment(collect($page->getAttributes())->except('id', 'slug')->toArray());
    }

    public function testCannotFindMissing()
    {
        $response = $this->getJson($this->uri('non-existing-page'));
        $response->assertNotFound();
    }
}
