<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $columnName = 'locale';
    private string $tableName = 'forms';

    public function up(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            if (Schema::hasColumn($this->tableName, 'slug')) {
                $table->dropUnique([$this->columnName, 'slug']);
                $table->unique(['slug']);
            }
            $table->dropColumn([$this->columnName]);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->char($this->columnName, 2)->default('ru')->after('user_id')->index();
            $table->index([$this->columnName]);
            $table->unique([$this->columnName, 'slug']);
        });
    }
};
