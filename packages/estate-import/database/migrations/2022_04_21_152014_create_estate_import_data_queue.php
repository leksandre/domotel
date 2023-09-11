<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_import_data_queue';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('history_id')->nullable()->default(0)->index();
            $table->boolean('done')->default(false)->index();
            $table->addColumn(
                'tinyInteger',
                'event',
                [
                    'default' => 0,
                    'length' => 2,
                    'unsigned' => true
                ]
            )->index();
            $table->string('model')->nullable()->index();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
