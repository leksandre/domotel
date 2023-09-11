<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'contacts_offices';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, 'region')) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('city')->nullable(true)->default(null)->after('title');
            $table->string('region')->nullable(true)->default(null)->after('title');
        });

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn('address', 'street');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn($this->tableName, 'region')) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn(['region', 'city']);
        });

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn('street', 'address');
        });
    }
};
