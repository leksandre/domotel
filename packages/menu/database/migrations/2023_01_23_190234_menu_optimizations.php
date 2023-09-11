<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['menu_id']);
            $table->dropIndex(['active']);
            $table->dropIndex(['page_id']);
            $table->dropIndex(['page_component_id']);
            $table->dropIndex(['priority']);

            $table->index(['page_id', 'page_component_id']);
            $table->index(['menu_id', 'active', 'priority']);
            $table->boolean('marked')->unsigned()->default(false)->after('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['page_id', 'page_component_id']);
            $table->dropIndex(['menu_id', 'active', 'priority']);

            $table->index(['parent_id']);
            $table->index(['menu_id']);
            $table->index(['active']);
            $table->index(['page_id']);
            $table->index(['component_id']);
            $table->index(['priority']);

            $table->boolean('marked')->default(false)->after('active')->change();
        });
    }
};
