<?php

namespace Tests\Feature;

use App\Enums\BlockType;
use App\Enums\ContentStatus;
use App\Enums\MenuLocation;
use App\Models\ContentBlock;
use App\Models\MediaAsset;
use App\Models\MenuItem;
use App\Models\SiteMenu;
use App\Models\SitePage;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsDatabaseModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_setting_singleton_and_json_casts(): void
    {
        $settings = SiteSetting::current();

        $this->assertNotNull($settings->id);
        $this->assertTrue($settings->singleton);
        $this->assertTrue($settings->cms_enabled);
        $this->assertIsArray($settings->brand);
        $this->assertEquals('نفیس تجارت', $settings->brand['name']);
        $this->assertIsArray($settings->theme);
        $this->assertIsArray($settings->contact);
        $this->assertIsArray($settings->seo);
        $this->assertIsArray($settings->social);
    }

    public function test_site_page_and_relations(): void
    {
        $media = MediaAsset::create([
            'bucket' => 'site-media',
            'title' => 'OG Image',
            'url' => 'https://example.com/og.jpg',
        ]);

        $page = SitePage::create([
            'slug' => 'about',
            'title_fa' => 'درباره ما',
            'title_en' => 'About Us',
            'status' => ContentStatus::PUBLISHED,
            'is_system' => true,
            'og_image_id' => $media->id,
        ]);

        $this->assertNotNull($page->id);
        $this->assertEquals(ContentStatus::PUBLISHED, $page->status);
        $this->assertEquals($media->id, $page->ogImage->id);
        $this->assertCount(1, $media->pages);
    }

    public function test_content_block_lifecycle_and_publish(): void
    {
        $page = SitePage::create([
            'slug' => 'home',
            'title_fa' => 'صفحه اصلی',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $block = $page->blocks()->create([
            'type' => BlockType::HERO,
            'block_key' => 'home_hero',
            'draft_data' => [
                'title' => 'عنوان هیرو',
                'subtitle' => 'توضیحات هیرو',
            ],
            'published_data' => null,
            'status' => ContentStatus::DRAFT,
            'position' => 1,
        ]);

        $this->assertEquals(ContentStatus::DRAFT, $block->status);
        $this->assertEquals(BlockType::HERO, $block->type);
        $this->assertEquals('هیرو', $block->type->label());
        $this->assertNull($block->published_data);

        // Test publish method
        $block->publish();

        $block->refresh();
        $this->assertEquals(ContentStatus::PUBLISHED, $block->status);
        $this->assertNotNull($block->published_at);
        $this->assertEquals('عنوان هیرو', $block->published_data['title']);

        // Test scopePublishedAndActive
        $activeBlocks = ContentBlock::publishedAndActive()->get();
        $this->assertCount(1, $activeBlocks);
        $this->assertEquals($block->id, $activeBlocks->first()->id);
    }

    public function test_site_menu_and_nested_menu_items(): void
    {
        $page = SitePage::create([
            'slug' => 'services',
            'title_fa' => 'خدمات',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $menu = SiteMenu::create([
            'name' => 'منوی اصلی',
            'location' => MenuLocation::HEADER,
            'status' => ContentStatus::PUBLISHED,
        ]);

        $parentItem = $menu->items()->create([
            'label_fa' => 'خدمات ما',
            'page_id' => $page->id,
            'position' => 1,
            'visible' => true,
        ]);

        $childItem = $menu->items()->create([
            'parent_id' => $parentItem->id,
            'label_fa' => 'سورسینگ و تأمین',
            'href' => '/services/sourcing',
            'position' => 1,
            'visible' => true,
        ]);

        $this->assertCount(2, $menu->items);
        $this->assertCount(1, $menu->rootItems);
        $this->assertEquals($parentItem->id, $childItem->parent->id);
        $this->assertCount(1, $parentItem->children);
        $this->assertEquals('/services', $parentItem->computed_url);
        $this->assertEquals('/services/sourcing', $childItem->computed_url);
    }

    public function test_database_seeder_populates_full_cms_ecosystem(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Settings
        $settings = SiteSetting::first();
        $this->assertNotNull($settings);
        $this->assertTrue($settings->cms_enabled);
        $this->assertEquals('نفیس تجارت', $settings->brand['name']);

        // 2. Pages: exactly 7 pages
        $expectedSlugs = ['home', 'about', 'services', 'industries', 'process', 'b2b', 'contact'];
        $pages = SitePage::all();
        $this->assertCount(7, $pages);
        foreach ($expectedSlugs as $slug) {
            $page = $pages->firstWhere('slug', $slug);
            $this->assertNotNull($page, "Page [{$slug}] must exist");
            $this->assertEquals(ContentStatus::PUBLISHED, $page->status);
            $this->assertTrue($page->is_system);
            $this->assertGreaterThan(0, $page->blocks()->count(), "Page [{$slug}] must have content blocks");
        }

        // 3. Content Blocks: check specific pages
        $homePage = SitePage::where('slug', 'home')->first();
        $this->assertCount(7, $homePage->blocks);
        $homeHero = $homePage->blocks->firstWhere('block_key', 'home_hero');
        $this->assertNotNull($homeHero);
        $this->assertEquals(BlockType::HERO, $homeHero->type);
        $this->assertEquals('نفیس تجارت', $homeHero->published_data['highlight']);

        // 4. Menus: exactly 5 locations
        $this->assertCount(5, SiteMenu::all());
        $headerMenu = SiteMenu::where('location', MenuLocation::HEADER->value)->first();
        $this->assertNotNull($headerMenu);
        $this->assertGreaterThan(0, $headerMenu->items()->count());
        $this->assertGreaterThan(0, $headerMenu->rootItems()->count());

        $footerServices = SiteMenu::where('location', MenuLocation::FOOTER_SERVICES->value)->first();
        $this->assertCount(4, $footerServices->items);

        $footerIndustries = SiteMenu::where('location', MenuLocation::FOOTER_INDUSTRIES->value)->first();
        $this->assertCount(5, $footerIndustries->items);

        $footerLegal = SiteMenu::where('location', MenuLocation::FOOTER_LEGAL->value)->first();
        $this->assertCount(4, $footerLegal->items);

        $mobileMenu = SiteMenu::where('location', MenuLocation::MOBILE->value)->first();
        $this->assertCount(8, $mobileMenu->items);
    }
}