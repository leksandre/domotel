<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $siteColumnName = 'site_id';
    private string $redirectTypeName = 'redirect_type';
    private string $tableName = 'pages';

    public function up(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            if (Schema::hasColumn($this->tableName, $this->redirectTypeName)) {
                $table->dropIndex([$this->redirectTypeName]);
            }

            $table->dropIndex(['slug']);
            $table->dropIndex(['active']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['type']);

            $table->unsignedBigInteger($this->siteColumnName)->after('parent_id')->default(0);
            $table->index([$this->siteColumnName, 'type', 'path', 'active']);
            $table->unique([$this->siteColumnName, 'parent_id', 'slug']);

            if (Schema::hasColumn($this->tableName, 'path_full')) {
                $table->dropColumn('path_full');
            }
        });
    }

    public function down(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->index('slug');
            $table->index($this->redirectTypeName);
            $table->index('active');
            $table->index('parent_id');
            $table->index('type');
            $table->dropIndex([$this->siteColumnName, 'type', 'path', 'active']);
            $table->dropUnique([$this->siteColumnName, 'parent_id', 'slug']);
        });
    }
};
