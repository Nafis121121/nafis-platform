<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tracking_code', 30)->unique();
            $table->string('method', 20)->default('sea');
            $table->string('freight_forwarder')->nullable();
            $table->string('bill_of_lading')->nullable();
            $table->string('container_tracking_number')->nullable();
            $table->foreignUuid('origin_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignUuid('destination_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('status', 30)->default('draft')->index();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->date('ata')->nullable();
            $table->decimal('freight_cost', 20, 4)->default(0);
            $table->decimal('insurance_cost', 20, 4)->default(0);
            $table->decimal('customs_cost', 20, 4)->default(0);
            $table->string('cost_currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
