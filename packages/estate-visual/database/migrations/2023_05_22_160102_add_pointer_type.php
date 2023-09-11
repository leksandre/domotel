<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kelnik\EstateVisual\Models\Enums\PointerType;

return new class extends Migration {
    private string $tableName = 'estate_visual_step_element_angle_pointers';
    private string $typeColumnName = 'type';
    private string $dataColumnName = 'data';
    private string $textColumnName = 'text';

    public function up(): void
    {
        if (!Schema::hasColumn($this->tableName, $this->typeColumnName)) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->unsignedTinyInteger($this->typeColumnName)->default(PointerType::Text->value)->after(
                    'angle_id'
                );
            });
        }

        if (Schema::hasColumn($this->tableName, $this->dataColumnName)) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->mediumText($this->textColumnName)->change();
        });
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn($this->textColumnName, $this->dataColumnName);
        });

        if ($this->isEmpty()) {
            return;
        }

        // update([
        //   $this->dataColumnName => DB::raw('CONCAT(\'{"text":"\', `' . $this->dataColumnName . '`, \'"}\')')
        // ])

        // Engine independent update
        //
        DB::table($this->tableName)->get()->each(function (stdClass $pointer) {
            DB::table($this->tableName)->where('id', $pointer->id)->update([
                $this->dataColumnName => json_encode(['text' => $pointer->{$this->dataColumnName}])
            ]);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->tableName, $this->typeColumnName)) {
            Schema::dropColumns($this->tableName, [$this->typeColumnName]);
        }

        if (!Schema::hasColumn($this->tableName, $this->dataColumnName)) {
            return;
        }

        if (!$this->isEmpty()) {
            // update([
            //   $this->dataColumnName => DB::raw('JSON_UNQUOTE(JSON_EXTRACT(`' . $this->dataColumnName . '`, \'$.text\'))')
            // ])

            DB::table($this->tableName)->get()->each(function (stdClass $pointer) {
                DB::table($this->tableName)->where('id', $pointer->id)->update([
                    $this->dataColumnName => Arr::get(
                        json_decode($pointer->{$this->dataColumnName}, true),
                        'text'
                    )
                ]);
            });
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->renameColumn($this->dataColumnName, $this->textColumnName);
        });
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string($this->textColumnName)->change();
        });
    }

    private function isEmpty(): bool
    {
        return DB::table($this->tableName)->limit(1)->get()->count() < 1;
    }
};
