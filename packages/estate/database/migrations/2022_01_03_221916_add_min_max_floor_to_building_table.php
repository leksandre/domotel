<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_buildings';
    private array $columns = ['floor_max', 'floor_min'];

    public function up(): void
    {
        foreach ($this->columns as $column) {
            if (Schema::hasColumn($this->tableName, $column)) {
                continue;
            }

            Schema::table($this->tableName, function (Blueprint $table) use ($column) {
                $table->smallInteger($column)->default(0)->after('complex_plan_image_id');
            });
        }
    }


    public function down(): void
    {
        foreach ($this->columns as $column) {
            if (!Schema::hasColumn($this->tableName, $column)) {
                continue;
            }

            Schema::table($this->tableName, function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
};
