<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name_fa')->nullable();
            $table->string('name_en')->nullable();
            $table->json('option_values')->nullable();
            $table->string('price_currency', 3)->default('USD');
            $table->decimal('price', 15, 2)->nullable();
            $table->unsignedInteger('moq')->nullable();
            $table->decimal('weight_kg', 10, 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
