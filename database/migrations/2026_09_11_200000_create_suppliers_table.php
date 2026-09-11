<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('country');
            $table->string('city')->nullable();
            $table->text('factory_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('wechat_id')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('rating', 1)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('default_currency', 3)->default('USD');
            $table->text('default_payment_terms')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
