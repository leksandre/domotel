<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $localeColumnName = 'locale';
    private string $tableName = 'news_elements';

    public function up(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {

            if (Schema::hasColumn($this->tableName, $this->localeColumnName)) {
                $table->dropUnique(['category_id', $this->localeColumnName, 'slug']);
                $table->dropColumn([$this->localeColumnName]);
                $table->unique(['category_id', 'slug']);
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->localeColumnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropUnique(['category_id', 'slug']);
            $table->char($this->localeColumnName, 2)->default('ru')->after('user_id')->index();
            $table->unique([$this->localeColumnName, 'slug']);
        });
    }
};
