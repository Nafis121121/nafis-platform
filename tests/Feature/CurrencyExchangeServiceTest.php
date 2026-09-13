<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Services\CurrencyExchangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyExchangeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_rates_reads_manual_values_from_site_settings_by_default(): void
    {
        $rates = app(CurrencyExchangeService::class)->getRates();

        $this->assertSame(125000.0, $rates['CNY']);
        $this->assertSame(245000.0, $rates['AED']);
        $this->assertSame(900000.0, $rates['USD']);
        $this->assertSame('manual', $rates['mode']);
    }

    public function test_manual_mode_takes_priority_and_skips_provider_sync(): void
    {
        config(['services.currency_exchange.url' => 'https://provider.test/rates']);
        Http::fake([
            'provider.test/*' => Http::response(['cny' => 999999, 'aed' => 999999, 'usd' => 999999]),
        ]);

        $service = app(CurrencyExchangeService::class);
        $rates = $service->refreshFromProvider();

        Http::assertNothingSent();
        $this->assertSame(900000.0, $rates['USD']);
        $this->assertSame(125000.0, $rates['CNY']);
    }

    public function test_auto_mode_fetches_and_persists_rates_from_provider(): void
    {
        config(['services.currency_exchange.url' => 'https://provider.test/rates']);
        Http::fake([
            'provider.test/*' => Http::response(['cny' => 131000, 'aed' => 251000, 'usd' => 915000]),
        ]);

        $setting = SiteSetting::current();
        $pricing = $setting->pricing;
        $pricing['rate_mode'] = 'auto';
        $setting->update(['pricing' => $pricing]);

        $rates = app(CurrencyExchangeService::class)->refreshFromProvider();

        Http::assertSent(fn ($request) => $request->url() === 'https://provider.test/rates');
        $this->assertSame(131000.0, $rates['CNY']);
        $this->assertSame(251000.0, $rates['AED']);
        $this->assertSame(915000.0, $rates['USD']);
        $this->assertSame('auto', $rates['source']);
        $this->assertNotNull($rates['updated_at']);

        // Persisted to site_settings so the pricing engine and header ticker stay in sync.
        $this->assertEquals(131000, (float) SiteSetting::current()->pricing['exchange_rate_cny']);
    }

    public function test_auto_mode_keeps_previous_rates_when_provider_request_fails(): void
    {
        config(['services.currency_exchange.url' => 'https://provider.test/rates']);
        Http::fake([
            'provider.test/*' => Http::response([], 500),
        ]);

        $setting = SiteSetting::current();
        $pricing = $setting->pricing;
        $pricing['rate_mode'] = 'auto';
        $setting->update(['pricing' => $pricing]);

        $rates = app(CurrencyExchangeService::class)->refreshFromProvider();

        $this->assertSame(900000.0, $rates['USD']);
        $this->assertSame(125000.0, $rates['CNY']);
    }

    public function test_set_manual_rate_persists_value_and_forces_manual_mode(): void
    {
        $setting = SiteSetting::current();
        $pricing = $setting->pricing;
        $pricing['rate_mode'] = 'auto';
        $setting->update(['pricing' => $pricing]);

        app(CurrencyExchangeService::class)->setManualRate('USD', 950000);

        $rates = app(CurrencyExchangeService::class)->getRates();
        $this->assertSame(950000.0, $rates['USD']);
        $this->assertSame('manual', $rates['mode']);
        $this->assertTrue(app(CurrencyExchangeService::class)->isManualMode());
    }

    public function test_pricing_engine_uses_current_site_setting_rate_for_quotation_recalculation(): void
    {
        app(CurrencyExchangeService::class)->setManualRate('USD', 700000);

        $customer = \App\Models\User::factory()->create();
        $category = \App\Models\Category::create(['name_fa' => 'الکترونیک', 'slug' => 'electronics-cx', 'is_active' => true]);
        $product = \App\Models\Product::create([
            'category_id' => $category->id,
            'name_fa' => 'محصول تستی نرخ ارز',
            'slug' => 'currency-test-product',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);

        $quotation = \App\Models\Quotation::create([
            'user_id' => $customer->id,
            'base_currency' => 'USD',
            'exchange_rate' => 0,
            'margin_percentage' => 10,
        ]);
        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'محصول تستی نرخ ارز',
            'quantity' => 1,
            'unit_cost_currency' => 10,
        ]);

        app(\App\Services\QuotationPricingService::class)->recalculate($quotation);
        $quotation->refresh();

        $this->assertEquals('700000.0000', $quotation->exchange_rate);
        // 10 * 700,000 * 1.1 = 7,700,000
        $this->assertEquals('7700000', $quotation->items->first()->unit_price_irr);
    }

    public function test_currency_ticker_is_visible_on_admin_and_portal_panels(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'super_admin']);
        $customer = \App\Models\User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('CNY')->assertSee('AED')->assertSee('USD');
        $this->actingAs($customer)->get('/portal')->assertOk()->assertSee('CNY')->assertSee('AED')->assertSee('USD');
    }

    public function test_manage_site_settings_page_exposes_manual_auto_rate_toggle(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->get('/admin/manage-site-settings')
            ->assertOk()
            ->assertSee('دریافت خودکار نرخ ارز')
            ->assertSee('دریافت فوری نرخ ارز');
    }
}
