<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_premises_features';
    private string $columnName = 'icon_id';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger($this->columnName)->nullable()->default(0)->after('active');
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
