<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\{Quotation,Order};
use App\Enums\{QuotationStatus,OrderStatus};

class PortalOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('استعلام‌های فعال', Quotation::query()
                ->where('user_id', auth()->id())
                ->whereNotIn('status', [QuotationStatus::REJECTED, QuotationStatus::EXPIRED, QuotationStatus::CONVERTED_TO_ORDER])
                ->count()),
            Stat::make('سفارش‌های در حال پردازش', Order::query()
                ->where('user_id', auth()->id())
                ->whereNotIn('status', [OrderStatus::COMPLETED, OrderStatus::CANCELLED])
                ->count()),
            Stat::make('بدهی معوق', number_format((float) Order::query()
                ->where('user_id', auth()->id())
                ->sum('balance_irr')) . ' ریال'),
        ];
    }
}
