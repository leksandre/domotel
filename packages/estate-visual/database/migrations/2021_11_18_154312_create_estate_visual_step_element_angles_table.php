<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_visual_step_element_angles';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('element_id')->default(0)->index();
            $table->unsignedBigInteger('image_id')->nullable()->default(0);
            $table->addColumn(
                'smallInteger',
                'degree',
                [
                    'default' => 0,
                    'length' => 3,
                    'unsigned' => true
                ]
            )->index();
            $table->string('shift', 20)->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
