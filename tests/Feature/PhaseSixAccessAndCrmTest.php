<?php

namespace Tests\Feature;

use App\Enums\CustomerInteractionType;
use App\Enums\QuotationStatus;
use App\Filament\Portal\Resources\PortalOrders\PortalOrderResource;
use App\Filament\Portal\Resources\PortalQuotations\PortalQuotationResource;
use App\Filament\Portal\Resources\PortalSourcingRequests\PortalSourcingRequestResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\SitePages\SitePageResource;
use App\Models\CustomerInteraction;
use App\Models\Quotation;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseSixAccessAndCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_only_query_own_portal_records(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);

        Quotation::create(['user_id' => $customer->id, 'status' => QuotationStatus::APPROVED, 'base_currency' => 'USD']);
        Quotation::create(['user_id' => $other->id, 'status' => QuotationStatus::APPROVED, 'base_currency' => 'USD']);

        $this->actingAs($customer);

        $this->assertSame(1, PortalQuotationResource::getEloquentQuery()->count());
        $this->assertTrue($customer->canAccessPanel(Panel::make()->id('portal')));
        $this->assertFalse($customer->canAccessPanel(Panel::make()->id('admin')));
        $this->assertSame(0, PortalOrderResource::getEloquentQuery()->count());
        $this->assertTrue(PortalQuotationResource::canViewAny());
        $this->assertTrue(PortalOrderResource::canViewAny());
        $this->assertTrue(PortalSourcingRequestResource::canViewAny());
    }

    public function test_customer_interaction_records_customer_and_staff(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $staff = User::factory()->create(['role' => 'sales_manager']);

        $interaction = CustomerInteraction::create([
            'customer_id' => $customer->id,
            'staff_id' => $staff->id,
            'type' => CustomerInteractionType::CALL,
            'summary' => 'پیگیری پیش‌فاکتور',
            'business_outcome' => 'ارسال نسخه اصلاح‌شده',
        ]);

        $this->assertTrue($interaction->customer->is($customer));
        $this->assertTrue($interaction->staff->is($staff));
        $this->assertDatabaseHas('customer_interactions', ['id' => $interaction->id, 'type' => 'call']);
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->assertFalse($customer->canAccessPanel(Panel::make()->id('admin')));
        $this->assertTrue(User::factory()->create(['role' => 'super_admin'])->canAccessPanel(Panel::make()->id('admin')));

        // HTTP request test: customer receives 403 when trying to access admin panel
        $response = $this->actingAs($customer)->get('/admin');
        $response->assertForbidden();

        // Customer can access portal
        $portalResponse = $this->actingAs($customer)->get('/portal');
        $portalResponse->assertSuccessful();

        // Super admin can access admin panel
        $adminUser = User::factory()->create(['role' => 'super_admin']);
        $adminResponse = $this->actingAs($adminUser)->get('/admin');
        $adminResponse->assertSuccessful();
    }

    public function test_resource_visibility_is_limited_by_staff_role(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $contentManager = User::factory()->create(['role' => 'content_manager']);
        $salesManager = User::factory()->create(['role' => 'sales_manager']);

        $this->actingAs($customer);
        $this->assertFalse(ProductResource::canViewAny());
        $this->assertFalse(SitePageResource::canViewAny());

        $this->actingAs($contentManager);
        $this->assertFalse(ProductResource::canViewAny());
        $this->assertTrue(SitePageResource::canViewAny());

        $this->actingAs($salesManager);
        $this->assertTrue(ProductResource::canViewAny());
        $this->assertFalse(SitePageResource::canViewAny());
    }
}
