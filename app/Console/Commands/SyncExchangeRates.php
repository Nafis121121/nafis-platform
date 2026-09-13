<?php

namespace App\Console\Commands;

use App\Services\CurrencyExchangeService;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange-rates:sync';

    protected $description = 'دریافت خودکار نرخ حواله یوان، درهم و دلار و به‌روزرسانی نرخ‌های بازرگانی (در صورت فعال بودن حالت خودکار)';

    public function handle(CurrencyExchangeService $service): int
    {
        if ($service->isManualMode()) {
            $this->info('حالت نرخ ارز روی دستی تنظیم شده است؛ همگام‌سازی خودکار انجام نشد.');

            return self::SUCCESS;
        }

        $rates = $service->refreshFromProvider();

        $this->info(sprintf(
            'نرخ‌های ارز به‌روزرسانی شد: CNY=%s, AED=%s, USD=%s',
            number_format($rates['CNY']),
            number_format($rates['AED']),
            number_format($rates['USD']),
        ));

        return self::SUCCESS;
    }
}
