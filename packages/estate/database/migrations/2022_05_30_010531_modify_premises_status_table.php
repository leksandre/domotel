<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_premises_statuses';
    private array $columns;

    public function __construct()
    {
        $this->columns = [
            'premises_card_available' =>
                fn(Blueprint $table) => $table->boolean('premises_card_available')
                    ->default(false)
                    ->after('priority')
                    ->index(),
            'hide_price' =>
                fn(Blueprint $table) => $table->boolean('hide_price')
                    ->default(false)
                    ->after('premises_card_available')
                    ->index(),
            'take_stat' =>
                fn(Blueprint $table) => $table->boolean('take_stat')
                    ->default(false)
                    ->after('hide_price')
                    ->index(),
            'icon_id' =>
                fn(Blueprint $table) => $table->unsignedBigInteger('icon_id')
                    ->nullable()
                    ->after('priority'),
            'additional_text' =>
                fn(Blueprint $table) => $table->string('additional_text')
                    ->nullable()
                    ->after('title')
        ];
    }

    public function up(): void
    {
        foreach ($this->columns as $columnName => $modifier) {
            if (Schema::hasColumn($this->tableName, $columnName)) {
                continue;
            }

            Schema::table($this->tableName, $modifier);
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $columnName => $modifier) {
            if (!Schema::hasColumn($this->tableName, $columnName)) {
                continue;
            }

            Schema::table($this->tableName, fn(Blueprint $table) => $table->dropColumn($columnName));
        }
    }
};
