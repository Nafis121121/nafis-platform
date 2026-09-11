<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->unique('singleton', 'site_settings_singleton_unique');
        });

        Schema::table('site_menus', function (Blueprint $table) {
            $table->unique('location', 'site_menus_location_unique');
        });

        Schema::table('content_blocks', function (Blueprint $table) {
            $table->unique(['page_id', 'block_key'], 'content_blocks_page_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('content_blocks', fn (Blueprint $table) => $table->dropUnique('content_blocks_page_key_unique'));
        Schema::table('site_menus', fn (Blueprint $table) => $table->dropUnique('site_menus_location_unique'));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropUnique('site_settings_singleton_unique'));
    }
};
