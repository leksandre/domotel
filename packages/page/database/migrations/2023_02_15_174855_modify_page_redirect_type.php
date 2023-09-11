<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\Enums\RedirectType;

return new class extends Migration
{
    private string $columnName = 'redirect_type';
    private string $tableName = 'pages';

    public function up(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedSmallInteger($this->columnName)->default(RedirectType::Disabled->value)->change();
        });
    }

    public function down(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedTinyInteger($this->columnName)->default(RedirectType::Disabled->value)->change();
        });
    }
};
