<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'form_fields';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->index();
            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('params')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
