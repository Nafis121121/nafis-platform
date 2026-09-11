<?php

namespace App\Services;

use App\Enums\QuotationStatus;
use App\Enums\SourcingRequestStatus;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SourcingRequest;
use Illuminate\Support\Facades\DB;

class SourcingService
{
    public function changeStatus(SourcingRequest $request, SourcingRequestStatus $status, ?string $note = null): SourcingRequest
    {
        return DB::transaction(function () use ($request, $status, $note): SourcingRequest {
            $request->status = $status;
            if ($note !== null) {
                $request->follow_up_notes = $note;
            }
            $request->save();

            if ($note !== null) {
                $request->statusHistories()->latest('id')->first()?->update(['note' => $note]);
            }

            return $request->refresh();
        });
    }

    public function createProduct(SourcingRequest $request, array $attributes = []): Product
    {
        $product = Product::create(array_merge([
            'name_fa' => $request->title,
            'slug' => str($request->title)->slug('-') . '-' . str()->lower(str()->random(4)),
            'description_fa' => $request->technical_specifications,
            'base_currency' => $request->target_currency,
            'base_price' => $request->target_price,
            'moq' => $request->estimated_quantity ?: 1,
            'is_active' => false,
        ], $attributes));

        $this->changeStatus($request, SourcingRequestStatus::SUPPLIER_FOUND);

        return $product;
    }

    public function createQuotation(SourcingRequest $request): Quotation
    {
        $quotation = Quotation::create([
            'user_id' => $request->user_id,
            'assigned_to' => $request->assigned_to,
            'status' => QuotationStatus::DRAFT,
            'base_currency' => $request->target_currency,
            'customer_notes' => "ایجادشده از درخواست سورسینگ {$request->reference_code}",
        ]);

        $request->quotation()->associate($quotation);
        $request->status = SourcingRequestStatus::QUOTED;
        $request->save();

        return $quotation;
    }
}
