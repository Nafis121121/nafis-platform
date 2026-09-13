<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use BackedEnum;

class QuotationPricingService
{
    public function __construct(
        protected CurrencyExchangeService $exchangeService
    ) {}

    public function recalculate(Quotation $quotation): Quotation
    {
        $quotation->loadMissing('items');

        $baseCurrency = $this->resolveCurrencyCode($quotation->base_currency);
        $shippingCurrency = $this->resolveCurrencyCode($quotation->shipping_currency);

        $baseRate = (float) $quotation->exchange_rate;
        if ($baseRate <= 0) {
            $baseRate = (float) $this->exchangeService->rateFor($baseCurrency);
        }

        // ۱. محاسبه مجموع ارزش خرید خام اقلام
        $subtotalForeign = 0;
        foreach ($quotation->items as $item) {
            $item->total_cost_currency = round((float) $item->quantity * (float) $item->unit_cost_currency, 4);
            $subtotalForeign += (float) $item->total_cost_currency;
        }

        $itemsIrr = round($subtotalForeign * $baseRate);

        // ۲. هزینه حمل بین‌الملل
        $shippingCostCurrency = round((float) $quotation->shipping_weight_kg * (float) $quotation->shipping_rate_per_kg, 4);

        if ($baseCurrency === $shippingCurrency) {
            $shippingRate = $baseRate;
            $totalForeignCurrency = round($subtotalForeign + $shippingCostCurrency, 4);
        } else {
            $shippingRate = (float) $this->exchangeService->rateFor($shippingCurrency);
            $totalForeignCurrency = 0;
        }

        $shippingIrr = round($shippingCostCurrency * $shippingRate);
        $foreignEquivalentIrr = $itemsIrr + $shippingIrr;

        // ۳. هزینه‌های ترخیص و داخلی
        $customs = round((float) $quotation->shipping_weight_kg * (float) $quotation->customs_rate_per_kg_irr);
        $directCostsIrr = $customs
            + (float) $quotation->inland_shipping_irr
            + (float) $quotation->unforeseen_cost_irr;

        // ۴. سود و مبلغ نهایی کل
        $profit = $quotation->profit_type === 'fixed'
            ? round((float) $quotation->profit_fixed_irr)
            : round(($foreignEquivalentIrr + $directCostsIrr) * ((float) $quotation->margin_percentage / 100));

        $finalTotalIrr = round($foreignEquivalentIrr + $directCostsIrr + $profit);

        // ۵. سرشکن کردن مبلغ نهایی بر روی اقلام (قیمت فروش واقعی هر قلم برای مشتری)
        foreach ($quotation->items as $item) {
            $shareRatio = $subtotalForeign > 0 
                ? ((float) $item->total_cost_currency / $subtotalForeign) 
                : (1 / max(1, $quotation->items->count()));

            $itemTotalSaleIrr = round($finalTotalIrr * $shareRatio);
            $qty = max(1, (float) $item->quantity);

            $item->total_price_irr = $itemTotalSaleIrr;
            $item->unit_price_irr = round($itemTotalSaleIrr / $qty);
            $item->save();
        }

        // ذخیره فاکتور
        $quotation->subtotal_base_currency = round($subtotalForeign, 4);
        $quotation->subtotal_irr = $itemsIrr;
        $quotation->total_foreign_currency = $totalForeignCurrency;
        $quotation->shipping_cost_base_currency = $shippingCostCurrency;
        $quotation->customs_duty_irr = $customs;
        $quotation->total_profit_irr = $profit;
        $quotation->final_total_irr = $finalTotalIrr;

        if (\Illuminate\Support\Facades\Schema::hasColumn('quotations', 'shipping_cost_irr')) {
            $quotation->shipping_cost_irr = $shippingIrr;
        }

        $quotation->save();

        return $quotation->refresh()->load('items');
    }

    public function recalculateItem(QuotationItem $item): QuotationItem
    {
        return $item;
    }

    private function resolveCurrencyCode(mixed $currency): string
    {
        if ($currency instanceof BackedEnum) {
            return strtoupper((string) $currency->value);
        }

        return strtoupper((string) ($currency ?? 'CNY'));
    }
}