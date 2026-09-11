<?php

namespace Tests\Feature;

use App\Services\CmsService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPageRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_page_renders_successfully_with_cms_content(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('نفیس تجارت');
        $response->assertSee('تأمین هوشمند کالا از بازارهای جهانی');
        $response->assertSee('چین 🇨🇳 ← دبی 🇦🇪 ← ایران 🇮🇷');
        $response->assertSee('ثبت درخواست تأمین');
        $response->assertSee('واردات و تأمین کالاهای دیجیتال');
        $response->assertSee('فرآیند تأمین در ۵ گام');
    }

    public function test_services_page_renders_successfully(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('خدمات بازرگانی و واردات');
        $response->assertSee('سورسینگ و شناسایی تأمین‌کننده');
        $response->assertSee('بازرسی و کنترل کیفیت');
        $response->assertSee('ترخیص و امور گمرکی');
    }

    public function test_supply_request_button_redirects_to_sourcing_form(): void
    {
        $response = $this->get('/requests/new');

        $response->assertRedirect('/sourcing');
    }

    public function test_all_seven_system_pages_return_status_200(): void
    {
        $pages = ['about', 'services', 'industries', 'process', 'b2b', 'contact'];

        foreach ($pages as $slug) {
            $response = $this->get("/{$slug}");
            $response->assertStatus(200);
        }
    }

    public function test_homepage_exposes_customer_portal_login(): void
    {
        $this->get('/')->assertOk()->assertSee('/portal/login');
    }

    public function test_non_existent_page_returns_404(): void
    {
        $response = $this->get('/page-does-not-exist-12345');

        $response->assertStatus(404);
    }

    public function test_cms_chrome_api_endpoint(): void
    {
        $response = $this->getJson('/api/cms/chrome');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'enabled' => true,
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'enabled',
                    'settings' => ['brand', 'theme', 'contact', 'seo'],
                    'menus' => ['header', 'footer_services', 'footer_industries', 'footer_legal', 'mobile'],
                ],
            ]);
    }

    public function test_cms_page_api_endpoint(): void
    {
        $response = $this->getJson('/api/cms/pages/home');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => 'home',
                    'title_fa' => 'صفحه اصلی',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'slug',
                    'title_fa',
                    'blocks' => [
                        '*' => ['id', 'block_key', 'type', 'position', 'data'],
                    ],
                ],
            ]);

        $this->assertCount(7, $response->json('data.blocks'));
    }

    public function test_cms_service_caching_and_clear_cache(): void
    {
        /** @var CmsService $cmsService */
        $cmsService = app(CmsService::class);

        $chrome1 = $cmsService->getSiteChrome();
        $chrome2 = $cmsService->getSiteChrome();
        $this->assertEquals($chrome1, $chrome2);

        $page1 = $cmsService->getPageContent('home');
        $page2 = $cmsService->getPageContent('home');
        $this->assertEquals($page1->id, $page2->id);

        $cmsService->clearCache();
        $chrome3 = $cmsService->getSiteChrome();
        $this->assertEquals($chrome1['settings']['id'], $chrome3['settings']['id']);
    }
}