<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'site_hosts';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->string('value');
            $table->timestamps();
            $table->unique(['site_id', 'value'], 'site_host');
            $table->unique(['value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
