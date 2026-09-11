<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('order_items', function(Blueprint $t) {
    $t->uuid('id')->primary(); $t->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete(); $t->foreignUuid('product_id')->constrained('products')->restrictOnDelete(); $t->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete(); $t->string('item_title'); $t->text('technical_description')->nullable(); $t->string('part_number')->nullable(); $t->unsignedInteger('quantity'); $t->decimal('unit_price_currency',20,4)->default(0); $t->decimal('unit_price_irr',20,0)->default(0); $t->decimal('total_irr',20,0)->default(0); $t->timestamps();
}); } public function down(): void { Schema::dropIfExists('order_items'); } };
