<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'form_log';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->index();
            $table->longText('data');
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
