<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'form_emails';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->index();
            $table->string('email')->index();
            $table->timestamps();

            $table->unique(['form_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
