<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\ComplexType;

return new class extends Migration
{
    private string $tableName = 'estate_complex_types';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('priority')->default(ComplexType::PRIORITY_DEFAULT)->index();
            $table->string('title')->nullable()->index();
            $table->string('title_short')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
