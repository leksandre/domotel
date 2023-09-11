<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $localeColumnName = 'locale';
    private string $tableName = 'pages';

    public function up(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            if (Schema::hasColumn($this->tableName, $this->localeColumnName)) {
                $table->dropUnique([$this->localeColumnName, 'path']);
                $table->dropUnique(['parent_id', $this->localeColumnName, 'slug']);
                $table->dropColumn([$this->localeColumnName]);
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->localeColumnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->char($this->localeColumnName, 2)->default('ru')->after('priority')->index();
            $table->unique([$this->localeColumnName, 'path']);
            $table->unique(['parent_id', $this->localeColumnName, 'slug']);
        });
    }
};
