<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SiteSetting;

class QuotationPricingService
{
    public function recalculate(Quotation $quotation): Quotation
    {
        $quotation->loadMissing('items');

        $pricing = SiteSetting::current()->pricing ?? [];

        $rate = (float) $quotation->exchange_rate;
        if ($rate <= 0) {
            $currency = $quotation->base_currency?->value ?? (string) $quotation->base_currency;
            $rate = match ($currency) {
                'CNY' => (float) ($pricing['exchange_rate_cny'] ?? 1),
                'AED' => (float) ($pricing['exchange_rate_aed'] ?? 1),
                'USD' => (float) ($pricing['exchange_rate_usd'] ?? 1),
                default => 1,
            };
            $quotation->exchange_rate = $rate;
        }

        $margin = (float) $quotation->margin_percentage;
        if ($margin <= 0 && !empty($pricing['default_margin_percentage'])) {
            $margin = (float) $pricing['default_margin_percentage'];
            $quotation->margin_percentage = $margin;
        }

        $subtotalBaseCurrency = 0; // جمع خرید ارزی همه اقلام (گام ۱)
        $itemsCostIrr = 0;        // جمع بهای تمام‌شده اقلام به ریال، بدون مارجین (صرفاً برای گزارش سود)
        $itemsSaleIrr = 0;        // جمع مبلغ ریالی نهایی اقلام شامل مارجین (گام ۲ و ۳)

        foreach ($quotation->items as $item) {
            // گام ۱: جمع خرید ارزی هر سطر = تعداد × قیمت خرید ارزی
            $item->total_cost_currency = round((float) $item->quantity * (float) $item->unit_cost_currency, 4);

            // گام ۲: تبدیل به ریال با نرخ تسعیر ارز انتخابی همین سند
            $costIrr = round((float) $item->total_cost_currency * $rate);

            // گام ۳: افزودن حاشیه سود بازرگانی به مبلغ خرید (فقط وقتی قیمت فروش به‌صورت دستی ثبت نشده)
            if ((float) $item->unit_price_irr <= 0 && (float) $item->unit_cost_currency > 0) {
                $item->unit_price_irr = round((float) $item->unit_cost_currency * $rate * (1 + ($margin / 100)));
            }
            $item->total_price_irr = round((float) $item->quantity * (float) $item->unit_price_irr);
            $item->save();

            $subtotalBaseCurrency += (float) $item->total_cost_currency;
            $itemsCostIrr += $costIrr;
            $itemsSaleIrr += (float) $item->total_price_irr;
        }

        // گام ۴: هزینه حمل، بازرسی، گمرک و ترخیص جداگانه محاسبه و به جمع ریالی اقلام اضافه می‌شود
        // (ارقام ارزی فقط یک‌بار با نرخ تسعیر تبدیل می‌شوند؛ ارقام ریالی مستقیماً جمع می‌شوند)
        $shipping = (float) $quotation->shipping_cost_base_currency;
        $shipping += (float) $quotation->shipping_weight_kg * (float) $quotation->shipping_rate_per_kg;
        $shipping += (float) $quotation->shipping_volume_cbm * (float) $quotation->shipping_rate_per_cbm;
        $inspection = (float) $quotation->inspection_fee_base_currency;
        $servicesIrr = round(($shipping + $inspection) * $rate)
            + (float) $quotation->customs_duty_irr
            + (float) $quotation->handling_fee_irr;
        $tax = (float) $quotation->tax_irr;

        $quotation->subtotal_base_currency = round($subtotalBaseCurrency, 4);
        $quotation->subtotal_irr = round($itemsSaleIrr);
        $quotation->shipping_cost_base_currency = round($shipping, 4);
        $quotation->total_profit_irr = round($itemsSaleIrr - $itemsCostIrr);

        // گام ۵: جمع نهایی = مجموع مبالغ ریالی نهایی اقلام + خدمات + مالیات؛ بدون ضرب مجدد نرخ تسعیر روی کل سند
        $quotation->final_total_irr = round($itemsSaleIrr + $servicesIrr + $tax);
        $quotation->save();

        return $quotation->refresh()->load('items');
    }

    public function recalculateItem(QuotationItem $item): QuotationItem
    {
        $quotation = $item->quotation;
        $item->total_cost_currency = round((float) $item->quantity * (float) $item->unit_cost_currency, 4);
        $item->total_price_irr = round((float) $item->quantity * (float) $item->unit_price_irr);
        $item->save();

        return $item;
    }
}
