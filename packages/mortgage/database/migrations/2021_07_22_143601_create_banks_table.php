<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Mortgage\Models\Bank;

return new class extends Migration
{
    private string $tableName = 'mortgage_banks';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Bank::PRIORITY_DEFAULT)->index('priority');
            $table->unsignedBigInteger('logo_id')->nullable()->default(Bank::DEFAULT_INT_VALUE);

            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
