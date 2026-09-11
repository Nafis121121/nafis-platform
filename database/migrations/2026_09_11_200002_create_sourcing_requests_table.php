<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sourcing_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->string('status', 30)->default('pending')->index();
            $table->string('title');
            $table->text('technical_specifications')->nullable();
            $table->text('required_standards')->nullable();
            $table->unsignedInteger('estimated_quantity')->nullable();
            $table->decimal('target_price', 20, 4)->nullable();
            $table->string('target_currency', 3)->default('USD');
            $table->json('attachments')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sourcing_requests');
    }
};
