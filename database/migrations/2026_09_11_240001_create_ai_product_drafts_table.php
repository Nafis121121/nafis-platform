<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_product_drafts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->text('source_url');
            $table->json('raw_payload')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('name')->nullable();
            $table->string('name_en')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('sku')->nullable();
            $table->string('model_number')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->text('short_desc')->nullable();
            $table->longText('long_desc')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_slug')->nullable();
            $table->text('seo_desc')->nullable();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_product_drafts');
    }
};
