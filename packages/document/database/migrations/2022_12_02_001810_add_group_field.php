<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['documents_categories'];
    private string $columnName = 'group_id';

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, $this->columnName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger($this->columnName)->nullable()->after('user_id')->index();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, $this->columnName)) {
                Schema::dropColumns($tableName, [$this->columnName]);
            }
        }
    }
};
