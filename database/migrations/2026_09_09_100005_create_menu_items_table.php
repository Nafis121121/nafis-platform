<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_id')->constrained('site_menus')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->foreignUuid('page_id')->nullable()->constrained('site_pages')->nullOnDelete();
            $table->string('label_fa');
            $table->string('label_en')->nullable();
            $table->string('href')->nullable();
            $table->string('icon')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('visible')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
