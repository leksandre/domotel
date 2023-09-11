<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents_categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['active']);
            $table->dropIndex(['group_id']);
            $table->dropIndex(['priority']);

            $table->unique(['group_id', 'slug']);
            $table->index(['active', 'priority']);
        });

        Schema::table('documents_elements', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['priority']);

            $table->index(['category_id', 'active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('documents_categories', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'slug']);
            $table->dropIndex(['active', 'priority']);

            $table->index(['slug']);
            $table->index(['active']);
            $table->index(['group_id']);
            $table->index(['priority']);
        });

        Schema::table('documents_elements', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'active', 'priority']);

            $table->index(['active']);
            $table->index(['category_id']);
            $table->index(['priority']);
        });
    }
};
