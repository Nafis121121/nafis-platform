<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Handles CNY / AED / USD -> IRR exchange rates.
 *
 * Rates can be synced automatically from an external provider (via HTTP + a
 * scheduled Artisan command) or maintained manually by staff from the
 * "تنظیمات سایت" panel. Manual values always take priority: when
 * `pricing.rate_mode` is "manual" the automatic sync is skipped entirely so
 * staff-entered figures are never overwritten.
 */
class CurrencyExchangeService
{
    public const CURRENCIES = ['CNY', 'AED', 'USD'];

    public const MODE_MANUAL = 'manual';
    public const MODE_AUTO = 'auto';

    /**
     * Current rates + metadata, always read fresh from site_settings so the
     * header ticker and pricing engine stay in sync.
     */
    public function getRates(): array
    {
        $pricing = SiteSetting::current()->pricing ?? [];

        return [
            'CNY' => (float) ($pricing['exchange_rate_cny'] ?? 0),
            'AED' => (float) ($pricing['exchange_rate_aed'] ?? 0),
            'USD' => (float) ($pricing['exchange_rate_usd'] ?? 0),
            'mode' => $pricing['rate_mode'] ?? self::MODE_MANUAL,
            'source' => $pricing['rate_source'] ?? null,
            'updated_at' => $pricing['rate_last_synced_at'] ?? null,
        ];
    }

    public function isManualMode(): bool
    {
        $pricing = SiteSetting::current()->pricing ?? [];

        return ($pricing['rate_mode'] ?? self::MODE_MANUAL) === self::MODE_MANUAL;
    }

    public function rateFor(string $currency): float
    {
        $pricing = SiteSetting::current()->pricing ?? [];

        return (float) match (strtoupper($currency)) {
            'CNY' => $pricing['exchange_rate_cny'] ?? 0,
            'AED' => $pricing['exchange_rate_aed'] ?? 0,
            'USD' => $pricing['exchange_rate_usd'] ?? 0,
            default => 0,
        };
    }

    /**
     * Persist a manually entered rate and switch the settings to manual
     * mode so a later automatic sync cannot silently override it.
     */
    public function setManualRate(string $currency, float $rate): void
    {
        $setting = SiteSetting::current();
        $pricing = $setting->pricing ?? [];
        $pricing['exchange_rate_' . strtolower($currency)] = $rate;
        $pricing['rate_mode'] = self::MODE_MANUAL;
        $pricing['rate_source'] = 'manual';
        $pricing['rate_last_synced_at'] = now()->toIso8601String();

        $setting->update(['pricing' => $pricing]);
    }

    /**
     * Fetch current CNY/AED/USD -> IRR rates from the configured provider and
     * persist them, but only when auto mode is enabled. Never throws: on any
     * failure the previous rates are kept and a warning is logged.
     */
    public function refreshFromProvider(): array
    {
        $setting = SiteSetting::current();
        $pricing = $setting->pricing ?? [];

        if (($pricing['rate_mode'] ?? self::MODE_MANUAL) !== self::MODE_AUTO) {
            return $this->getRates();
        }

        $endpoint = config('services.currency_exchange.url');

        if (empty($endpoint)) {
            Log::warning('CurrencyExchangeService: no provider URL configured, skipping sync.');

            return $this->getRates();
        }

        try {
            $response = Http::timeout((int) config('services.currency_exchange.timeout', 10))
                ->acceptJson()
                ->get($endpoint);

            if (!$response->successful()) {
                throw new \RuntimeException('Currency provider responded with HTTP ' . $response->status());
            }

            $fetched = $this->extractRates($response->json() ?? []);

            if (empty($fetched)) {
                throw new \RuntimeException('Currency provider response did not contain any usable rates.');
            }

            foreach ($fetched as $currency => $rate) {
                if ($rate > 0) {
                    $pricing['exchange_rate_' . strtolower($currency)] = round($rate);
                }
            }

            $pricing['rate_source'] = 'auto';
            $pricing['rate_last_synced_at'] = now()->toIso8601String();

            $setting->update(['pricing' => $pricing]);
        } catch (Throwable $e) {
            Log::warning('CurrencyExchangeService: failed to refresh exchange rates.', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->getRates();
    }

    /**
     * Normalizes a handful of plausible provider response shapes into a flat
     * ['CNY' => float, 'AED' => float, 'USD' => float] map.
     */
    private function extractRates(array $payload): array
    {
        $rates = [];

        foreach (self::CURRENCIES as $currency) {
            $value = data_get($payload, strtolower($currency))
                ?? data_get($payload, $currency)
                ?? data_get($payload, "rates.{$currency}")
                ?? data_get($payload, "rates.{$currency}.value")
                ?? data_get($payload, "rates.{$currency}.sell");

            if ($value !== null && is_numeric($value)) {
                $rates[$currency] = (float) $value;
            }
        }

        return $rates;
    }
}
