<?php

namespace EscolaLms\Pages\Tests\Api;

use EscolaLms\Pages\Models\Page;
use EscolaLms\Pages\Repository\PageRepository;
use EscolaLms\Pages\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PagesCreateTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(string $slug): string
    {
        return sprintf('/api/admin/pages%s', $slug);
    }

    public function testAdminCanCreatePage()
    {
        $this->authenticateAsAdmin();
        $page = Page::factory()->makeOne(['active'=>false]);
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/pages',
            $page->toArray()
        );

        $response->assertOk();

        $response2 = $this->getJson(
            '/api/pages/'.$page->slug,
        );

        $response2->assertStatus(403);

        $response3 = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/pages/'.$page->id,
        );

        $response3->assertOk();
    }

    public function testAdminCannotCreatePageWithoutTitle()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->makeOne();
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/pages',
            collect($page->getAttributes())->except('id', 'slug', 'title')->toArray()
        );
        $response->assertStatus(422);
        $response = $this->getJson(
            '/api/pages/'.$page->slug,
        );

        $response->assertNotFound();
    }

    public function testAdminCannotCreatePageWithoutContent()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->makeOne();
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/pages',
            collect($page->getAttributes())->except('id', 'slug', 'content')->toArray()
        );
        $response->assertStatus(422);
        //TODO: make sure the page doesn't exists
    }

    public function testAdminCannotCreateDuplicatePage()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();
        $duplicate = Page::factory()->makeOne($page->getAttributes());
        $response = $this->actingAs($this->user, 'api')->postJson('/api/admin/pages', $page->toArray());
        $response->assertStatus(422);
    }

    public function testGuestCannotCreatePage()
    {
        $page = Page::factory()->makeOne();
        $response = $this->postJson(
            '/api/admin/pages',
            collect($page->getAttributes())->except('id', 'slug')->toArray()
        );
        $response->assertUnauthorized();
    }
}
