<?php

namespace App\Providers;

use App\Models\ContentBlock;
use App\Models\MenuItem;
use App\Models\MediaAsset;
use App\Models\SiteMenu;
use App\Models\SitePage;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SourcingRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\WholesaleRule;
use App\Models\CustomerInteraction;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\AiProductDraft;
use App\Models\SiteSetting;
use App\Observers\ContentBlockObserver;
use App\Observers\MenuItemObserver;
use App\Observers\MediaAssetObserver;
use App\Observers\SiteMenuObserver;
use App\Observers\SitePageObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability, mixed $arguments): ?bool {
            if (! $user) {
                return null;
            }

            if ($user->isRole('super_admin')) {
                return true;
            }

            $modelArgument = is_array($arguments) ? ($arguments[0] ?? null) : $arguments;
            $model = is_string($modelArgument)
                ? $modelArgument
                : (is_object($modelArgument) ? $modelArgument::class : null);
            $cms = [
                SitePage::class, ContentBlock::class, SiteMenu::class, MenuItem::class,
                MediaAsset::class, SiteSetting::class,
            ];
            $catalog = [Category::class, Brand::class, Product::class, ProductVariant::class];
            $sales = [
                Quotation::class, QuotationItem::class, SourcingRequest::class,
                Supplier::class, SupplierProduct::class, Order::class, OrderItem::class,
                OrderPayment::class, WholesaleRule::class, CustomerInteraction::class,
                AiProductDraft::class,
            ];
            $logistics = [
                Warehouse::class, InventoryStock::class, InventoryMovement::class,
                Shipment::class, ShipmentItem::class,
            ];

            return match (true) {
                $user->isRole('content_manager') => in_array($model, $cms, true),
                $user->isRole('warehouse_staff') => in_array($model, $logistics, true),
                $user->isRole('sales_manager') => in_array($model, array_merge($catalog, $sales), true),
                default => false,
            };
        });

        SitePage::observe(SitePageObserver::class);
        ContentBlock::observe(ContentBlockObserver::class);
        SiteMenu::observe(SiteMenuObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        MediaAsset::observe(MediaAssetObserver::class);
    }
}
