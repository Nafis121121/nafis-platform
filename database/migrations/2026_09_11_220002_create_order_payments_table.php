<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('order_payments', function(Blueprint $t) {
    $t->uuid('id')->primary(); $t->string('reference_code',30)->unique(); $t->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete(); $t->string('type',20); $t->string('method',30); $t->decimal('amount_irr',20,0); $t->decimal('amount_currency',20,4)->nullable(); $t->decimal('exchange_rate',20,4)->nullable(); $t->string('receipt_number')->nullable(); $t->string('proof_path')->nullable(); $t->string('status',30)->default('pending_verification'); $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamp('verified_at')->nullable(); $t->text('notes')->nullable(); $t->timestamps();
}); } public function down(): void { Schema::dropIfExists('order_payments'); } };
