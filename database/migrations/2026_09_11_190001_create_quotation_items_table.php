<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('item_title');
            $table->text('technical_description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_cost_currency', 20, 4)->default(0);
            $table->decimal('unit_price_irr', 20, 0)->default(0);
            $table->decimal('total_cost_currency', 20, 4)->default(0);
            $table->decimal('total_price_irr', 20, 0)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
