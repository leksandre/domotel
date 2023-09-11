<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $elIndex = 'category_active_date_priority';

    public function up(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->dropIndex(['slug']);
        });

        Schema::table('news_elements', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('preview_image')->change();
            $table->unsignedBigInteger('body_image')->change();

            $table->dropIndex(['slug']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['active']);
            $table->dropIndex(['active_date_start']);
            $table->dropIndex(['active_date_finish']);

            $table->index(
                ['category_id', 'active', 'active_date_start', 'active_date_finish', 'priority'],
                $this->elIndex
            );
        });
    }

    public function down(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->index(['slug']);
        });

        Schema::table('news_elements', function (Blueprint $table) {
            $table->index(['slug']);
            $table->index(['category_id']);
            $table->index(['active']);
            $table->index(['active_date_start']);
            $table->index(['active_date_finish']);

            $table->dropIndex($this->elIndex);
        });
    }
};
