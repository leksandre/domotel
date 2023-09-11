<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'menu_items';
    private string $columnName = 'marked';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->boolean($this->columnName)->default(false)->after('active');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            Schema::dropColumns($this->tableName, $this->columnName);
        }
    }
};
