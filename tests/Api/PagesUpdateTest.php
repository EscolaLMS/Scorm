<?php

namespace EscolaLms\Pages\Tests\Api;

use EscolaLms\Pages\Models\Page;
use EscolaLms\Pages\Repository\PageRepository;
use EscolaLms\Pages\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PagesUpdateTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(int $id): string
    {
        return sprintf('/api/admin/pages/%s', $id);
    }

    public function testAdminCanUpdateExistingPage()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();
        $pageNew = Page::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($page->id),
            [
                'title' => $pageNew->title,
                'content' => $pageNew->content,
            ]
        );
        $response->assertOk();
        $page->refresh();

        $this->assertEquals($pageNew->title, $page->title);
        $this->assertEquals($pageNew->content, $page->content);
    }

    public function testAdminCanUpdateExistingPageWithMissingTitle()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();
        $pageNew = Page::factory()->makeOne();
        $oldTitle = $page->title;
        $oldContent = $page->content;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($page->id),
            [
                'content' => $pageNew->content,
            ]
        );
        $response->assertStatus(200);
        $page->refresh();

        $this->assertEquals($oldTitle, $page->title);
        $this->assertEquals($pageNew->content, $page->content);
    }

    public function testAdminCanUpdateExistingPageWithMissingContent()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->createOne();
        $pageNew = Page::factory()->makeOne();
        $oldTitle = $page->title;
        $oldContent = $page->content;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($page->id),
            [
                'title' => $pageNew->title,
            ]
        );
        $response->assertStatus(200);
        $page->refresh();

        $this->assertEquals($pageNew->title, $page->title);
        $this->assertEquals($oldContent, $page->content);
    }

    public function testAdminCannotUpdateMissingPage()
    {
        $this->authenticateAsAdmin();

        $page = Page::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri(99999999),
            [
                'title' => $page->title,
                'content' => $page->content,
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(0, $page->newQuery()->where('slug', $page->slug)->count());
    }

    public function testGuestCannotUpdateExistingPage()
    {
        $page = Page::factory()->createOne();
        $pageNew = Page::factory()->makeOne();

        $oldTitle = $page->title;
        $oldContent = $page->content;

        $response = $this->patchJson(
            $this->uri($page->id),
            [
                'title' => $pageNew->title,
                'content' => $pageNew->content,
            ]
        );
        $response->assertUnauthorized();
        $page->refresh();

        $this->assertEquals($oldTitle, $page->title);
        $this->assertEquals($oldContent, $page->content);
    }
}
