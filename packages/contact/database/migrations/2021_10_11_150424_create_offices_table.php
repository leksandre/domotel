<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Contact\Models\Office;

return new class extends Migration
{
    private string $tableName = 'contacts_offices';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Office::PRIORITY_DEFAULT)->index();
            $table->char('locale', 2)->default('ru')->index();
            $table->unsignedBigInteger('image_id')->nullable()->default(null);
            $table->string('title')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('route_link')->nullable();
            $table->string('coords', 100)->nullable();
            $table->json('schedule')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
