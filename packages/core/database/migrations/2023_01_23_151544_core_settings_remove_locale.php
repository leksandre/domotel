<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $columnName = 'locale';
    private string $tableName = 'settings';

    public function up(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropPrimary([$this->columnName, 'module', 'name']);
            $table->dropColumn([$this->columnName]);
            $table->dropIndex(['module']);
            $table->dropIndex(['name']);

            $table->primary(['module', 'name']);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropPrimary(['module', 'name']);

            $table->char($this->columnName, 2);
            $table->unique([$this->columnName, 'module', 'name']);
            $table->index(['module']);
            $table->index(['name']);
        });
    }
};
