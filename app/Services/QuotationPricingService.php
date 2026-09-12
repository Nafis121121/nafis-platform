<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;

class QuotationPricingService
{
    public function recalculate(Quotation $quotation): Quotation
    {
        $quotation->loadMissing('items');

        $subtotalBase = 0;
        $subtotalIrr = 0;
        $margin = (float) $quotation->margin_percentage;
        $rate = (float) $quotation->exchange_rate;

        foreach ($quotation->items as $item) {
            $item->total_cost_currency = round((float) $item->quantity * (float) $item->unit_cost_currency, 4);
            $costIrr = round((float) $item->total_cost_currency * $rate);
            $item->unit_price_irr = round((float) $item->unit_cost_currency * $rate * (1 + ($margin / 100)));
            $item->total_price_irr = round((float) $item->quantity * (float) $item->unit_price_irr);
            $item->save();

            $subtotalBase += (float) $item->total_cost_currency;
            $subtotalIrr += $costIrr;
        }

        $shipping = (float) $quotation->shipping_cost_base_currency;
        $shipping += (float) $quotation->shipping_weight_kg * (float) $quotation->shipping_rate_per_kg;
        $shipping += (float) $quotation->shipping_volume_cbm * (float) $quotation->shipping_rate_per_cbm;
        $inspection = (float) $quotation->inspection_fee_base_currency;
        $costsIrr = round(($shipping + $inspection) * $rate)
            + (float) $quotation->customs_duty_irr
            + (float) $quotation->handling_fee_irr;
        $profit = round(($subtotalIrr + $costsIrr) * ($margin / 100));
        $tax = (float) $quotation->tax_irr;

        $quotation->subtotal_base_currency = round($subtotalBase, 4);
        $quotation->subtotal_irr = round($subtotalIrr);
        $quotation->shipping_cost_base_currency = round($shipping, 4);
        $quotation->total_profit_irr = $profit;
        $quotation->final_total_irr = round($subtotalIrr + $costsIrr + $profit + $tax);
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
