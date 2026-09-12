<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('supplier_sku')->nullable();
            $table->unsignedInteger('moq')->default(1);
            $table->decimal('cost_price', 20, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->unsignedInteger('lead_time_days')->nullable();
            $table->text('product_url')->nullable();
            $table->timestamps();
            $table->unique(['supplier_id', 'product_id', 'product_variant_id'], 'supplier_product_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
