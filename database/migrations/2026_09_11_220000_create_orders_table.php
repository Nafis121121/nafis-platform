<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('orders', function(Blueprint $t) {
    $t->uuid('id')->primary(); $t->string('reference_code',30)->unique(); $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); $t->foreignUuid('quotation_id')->nullable()->unique()->constrained('quotations')->nullOnDelete();
    $t->string('status',30)->default('pending_payment')->index(); $t->string('base_currency',3)->default('USD'); $t->decimal('exchange_rate',20,4)->default(1);
    $t->decimal('subtotal_irr',20,0)->default(0); $t->decimal('discount_irr',20,0)->default(0); $t->decimal('tax_irr',20,0)->default(0); $t->decimal('late_penalty_irr',20,0)->default(0); $t->decimal('total_irr',20,0)->default(0); $t->decimal('required_deposit_irr',20,0)->default(0); $t->decimal('paid_irr',20,0)->default(0); $t->decimal('balance_irr',20,0)->default(0);
    $t->string('payment_status',20)->default('unpaid')->index(); $t->text('delivery_address')->nullable(); $t->string('incoterms',10)->nullable(); $t->text('shipping_terms')->nullable(); $t->text('customer_notes')->nullable(); $t->text('internal_notes')->nullable(); $t->timestamps();
}); } public function down(): void { Schema::dropIfExists('orders'); } };
