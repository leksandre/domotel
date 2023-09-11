<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->char('locale', 2)->default('ru')->index();
            $table->string('module', 50)->nullable()->default(null)->index();
            $table->string('name')->nullable()->default(null)->index();
            $table->mediumText('value')->nullable()->default(null);
            $table->primary(['locale', 'module', 'name']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
