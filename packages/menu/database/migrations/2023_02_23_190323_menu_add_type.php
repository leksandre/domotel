<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Menu\Models\Enums\Type;

return new class extends Migration
{
    private string $tableName = 'menu';
    private string $columnName = 'type';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedTinyInteger($this->columnName)
                ->default(Type::Tree->value)
                ->after('active');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            Schema::dropColumns($this->tableName, $this->columnName);
        }
    }
};
