<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('route_path')->nullable();
            $table->string('title_fa');
            $table->string('title_en')->nullable();
            $table->text('summary_fa')->nullable();
            $table->text('summary_en')->nullable();
            $table->string('status')->default('published');
            $table->boolean('is_system')->default(false);
            $table->integer('position')->default(0);
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->boolean('noindex')->default(false);
            $table->foreignUuid('og_image_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_pages');
    }
};
