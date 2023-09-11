<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $columnName = 'meta';
    private string $tableName = 'pages';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->json($this->columnName)->nullable()->default(null)->after('redirect_url');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::dropColumns($this->tableName, $this->columnName);
    }
};
