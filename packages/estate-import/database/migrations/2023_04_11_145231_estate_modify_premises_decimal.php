<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_premises';
    private array $types = [
        [
            'new' => [16, 2],
            'old' => [10, 2],
            'fields' => ['price', 'price_total', 'price_sale', 'price_meter', 'price_rent']
        ],
        [
            'new' => [6, 2],
            'old' => [10, 2],
            'fields' => ['area_total', 'area_living', 'area_kitchen']
        ]
    ];

    public function up(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            foreach ($this->types as $type) {
                foreach ($type['fields'] as $field) {
                    $table->decimal($field, $type['new'][0], $type['new'][1], true)->default(0)->change();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            foreach ($this->types as $type) {
                foreach ($type['fields'] as $field) {
                    $table->decimal($field, $type['old'][0], $type['old'][1], true)->default(0)->change();
                }
            }
        });
    }
};
