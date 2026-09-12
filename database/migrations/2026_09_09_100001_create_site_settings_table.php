<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('singleton')->default(true);
            $table->boolean('cms_enabled')->default(true);
            $table->json('brand');
            $table->json('theme');
            $table->json('contact');
            $table->json('seo');
            $table->json('social');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
