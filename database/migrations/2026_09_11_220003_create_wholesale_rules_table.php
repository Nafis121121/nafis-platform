<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('wholesale_rules', function(Blueprint $t) {
    $t->id(); $t->foreignUuid('product_id')->nullable()->constrained('products')->cascadeOnDelete(); $t->foreignUuid('category_id')->nullable()->constrained('categories')->cascadeOnDelete(); $t->unsignedInteger('min_quantity'); $t->unsignedInteger('max_quantity')->nullable(); $t->decimal('discount_percentage',8,4)->default(0); $t->date('starts_at')->nullable(); $t->date('ends_at')->nullable(); $t->boolean('is_active')->default(true); $t->timestamps();
}); } public function down(): void { Schema::dropIfExists('wholesale_rules'); } };
