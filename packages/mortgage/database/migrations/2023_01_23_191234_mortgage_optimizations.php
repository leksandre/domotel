<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mortgage_banks', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex('priority');

            $table->index(['active', 'priority']);
        });

        Schema::table('mortgage_programs', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['bank_id']);
            $table->dropIndex(['priority']);

            $table->index(['bank_id', 'active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('mortgage_banks', function (Blueprint $table) {
            $table->index(['active']);
            $table->index('priority');

            $table->dropIndex(['active', 'priority']);
        });

        Schema::table('mortgage_programs', function (Blueprint $table) {
            $table->index(['active']);
            $table->index(['bank_id']);
            $table->index(['priority']);

            $table->dropIndex(['bank_id', 'active', 'priority']);
        });
    }
};
