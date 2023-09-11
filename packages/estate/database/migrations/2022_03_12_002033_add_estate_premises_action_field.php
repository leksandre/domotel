<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_premises';
    private string $columnName = 'action';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->boolean($this->columnName)->index()->after('active')->default(false);
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn($this->columnName);
        });
    }
};
