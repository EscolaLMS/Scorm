<?php

namespace EscolaLms\Pages\Tests\Api;

use EscolaLms\Pages\Models\Page;
use EscolaLms\Pages\Repository\PageRepository;
use EscolaLms\Pages\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PagesDeleteTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(int $id): string
    {
        return sprintf('/api/admin/pages/%s', $id);
    }

    public function testAdminCanDeleteExistingPage()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();
        $response = $this->actingAs($this->user, 'api')->delete($this->uri($page->id));
        $response->assertOk();
        $this->assertEquals(0, Page::factory()->make()->newQuery()->where('slug', $page->slug)->count());
    }

    public function testAdminCannotDeleteMissingPage()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->makeOne();
        $page->id = 999999;

        $response = $this->actingAs($this->user, 'api')->delete($this->uri($page->id));



        $response->assertStatus(404);
    }

    public function testGuestCannotDeleteExistingPage()
    {
        $page = Page::factory()->createOne();
        $response = $this->json('delete', $this->uri($page->id));
        $response->assertUnauthorized();
    }
}
