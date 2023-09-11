<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropIndex(['slug']);
        });

        Schema::table('form_emails', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['form_id']);
        });

        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['form_id']);

            $table->index(['form_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropIndex(['form_id', 'active']);

            $table->index(['active']);
            $table->index(['form_id']);
        });

        Schema::table('form_emails', function (Blueprint $table) {
            $table->index(['email']);
            $table->index(['form_id']);
        });

        Schema::table('form_fields', function (Blueprint $table) {
            $table->index(['active']);
            $table->index(['form_id']);

            $table->dropIndex(['form_id', 'active']);
        });
    }
};
