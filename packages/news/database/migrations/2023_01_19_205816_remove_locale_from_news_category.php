<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $localeColumnName = 'locale';
    private string $tableName = 'news_categories';

    public function up(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->localeColumnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropUnique([$this->localeColumnName, 'slug']);
            $table->dropColumn([$this->localeColumnName]);
            $table->unique(['slug']);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->localeColumnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->char($this->localeColumnName, 2)->default('ru')->after('user_id')->index();
            $table->unique([$this->localeColumnName, 'slug']);
        });
    }
};
