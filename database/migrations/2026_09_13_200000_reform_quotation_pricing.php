<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->string('shipping_currency', 3)->default('CNY')->after('base_currency');
            $table->decimal('total_foreign_currency', 20, 4)->default(0)->after('subtotal_irr');
            $table->decimal('customs_rate_per_kg_irr', 20, 0)->default(6950000)->after('customs_duty_irr');
            $table->decimal('inland_shipping_irr', 20, 0)->default(0)->after('customs_rate_per_kg_irr');
            $table->decimal('unforeseen_cost_irr', 20, 0)->default(0)->after('inland_shipping_irr');
            $table->string('profit_type', 20)->default('percentage')->after('margin_percentage');
            $table->decimal('profit_fixed_irr', 20, 0)->default(0)->after('profit_type');
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->decimal('shipping_rate_per_kg', 20, 4)->default(55)->change();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropColumn([
                'shipping_currency',
                'total_foreign_currency',
                'customs_rate_per_kg_irr',
                'inland_shipping_irr',
                'unforeseen_cost_irr',
                'profit_type',
                'profit_fixed_irr',
            ]);
        });
    }
};
