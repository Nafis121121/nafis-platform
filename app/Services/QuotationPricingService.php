<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;

class QuotationPricingService
{
    public function recalculate(Quotation $quotation): Quotation
    {
        $quotation->loadMissing('items');

        $subtotalForeign = 0;
        $rate = (float) $quotation->exchange_rate;

        foreach ($quotation->items as $item) {
            $item->total_cost_currency = round((float) $item->quantity * (float) $item->unit_cost_currency, 4);
            $item->unit_price_irr = round((float) $item->unit_cost_currency * $rate);
            $item->total_price_irr = round((float) $item->quantity * (float) $item->unit_price_irr);
            $item->save();

            $subtotalForeign += (float) $item->total_cost_currency;
        }

        $shipping = round((float) $quotation->shipping_weight_kg * (float) $quotation->shipping_rate_per_kg, 4);
        $totalForeign = round($subtotalForeign + $shipping, 4);
        $foreignEquivalentIrr = round($totalForeign * $rate);
        $customs = round((float) $quotation->shipping_weight_kg * (float) $quotation->customs_rate_per_kg_irr);
        $directCostsIrr = $customs
            + (float) $quotation->inland_shipping_irr
            + (float) $quotation->unforeseen_cost_irr;
        $profit = $quotation->profit_type === 'fixed'
            ? round((float) $quotation->profit_fixed_irr)
            : round(($foreignEquivalentIrr + $directCostsIrr) * ((float) $quotation->margin_percentage / 100));

        $quotation->subtotal_base_currency = round($subtotalForeign, 4);
        $quotation->subtotal_irr = round($subtotalForeign * $rate);
        $quotation->total_foreign_currency = $totalForeign;
        $quotation->shipping_cost_base_currency = $shipping;
        $quotation->customs_duty_irr = $customs;
        $quotation->total_profit_irr = $profit;
        $quotation->final_total_irr = round($foreignEquivalentIrr + $directCostsIrr + $profit);
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
