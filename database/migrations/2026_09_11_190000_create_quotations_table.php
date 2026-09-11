<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index();
            $table->string('base_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 20, 4)->default(1);
            $table->decimal('subtotal_base_currency', 20, 4)->default(0);
            $table->decimal('subtotal_irr', 20, 0)->default(0);
            $table->decimal('shipping_cost_base_currency', 20, 4)->default(0);
            $table->decimal('shipping_weight_kg', 12, 3)->default(0);
            $table->decimal('shipping_volume_cbm', 12, 4)->default(0);
            $table->decimal('shipping_rate_per_kg', 20, 4)->default(0);
            $table->decimal('shipping_rate_per_cbm', 20, 4)->default(0);
            $table->decimal('customs_duty_irr', 20, 0)->default(0);
            $table->decimal('inspection_fee_base_currency', 20, 4)->default(0);
            $table->decimal('handling_fee_irr', 20, 0)->default(0);
            $table->decimal('margin_percentage', 8, 4)->default(0);
            $table->decimal('total_profit_irr', 20, 0)->default(0);
            $table->decimal('tax_irr', 20, 0)->default(0);
            $table->decimal('final_total_irr', 20, 0)->default(0);
            $table->text('payment_terms')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
