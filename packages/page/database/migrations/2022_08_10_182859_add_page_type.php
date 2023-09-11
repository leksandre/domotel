<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\Enums\Type;

return new class extends Migration
{
    private string $tableName = 'pages';
    private string $columnName = 'type';

    public function up(): void
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedTinyInteger($this->columnName)
                ->after('active')
                ->default(Type::Simple->value)
                ->index();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn($this->columnName);
            $table->dropIndex($this->columnName);
        });
    }
};
