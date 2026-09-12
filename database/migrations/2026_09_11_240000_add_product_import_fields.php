<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('status', 20)->default('active')->index();
            $table->string('catalog_visibility', 20)->default('visible')->index();
            $table->string('seo_title')->nullable();
            $table->string('seo_slug')->nullable()->unique();
            $table->text('seo_desc')->nullable();
            $table->string('image_alt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropUnique(['seo_slug']);
            $table->dropColumn(['status', 'catalog_visibility', 'seo_title', 'seo_slug', 'seo_desc', 'image_alt']);
        });
    }
};
