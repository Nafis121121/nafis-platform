<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// همگام‌سازی خودکار نرخ ارز (فقط زمانی که حالت نرخ روی "خودکار" باشد اجرا می‌شود)
Schedule::command('exchange-rates:sync')->hourly()->withoutOverlapping();
