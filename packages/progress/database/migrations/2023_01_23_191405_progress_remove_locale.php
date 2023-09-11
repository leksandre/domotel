<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $columnName = 'locale';
    private array $tables = ['progress_albums', 'progress_cameras', 'progress_groups'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, $this->columnName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropColumn([$this->columnName]);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, $this->columnName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->char($this->columnName, 2)->default('ru')->after('user_id')->index();
                $table->index([$this->columnName]);
            });
        }
    }
};
