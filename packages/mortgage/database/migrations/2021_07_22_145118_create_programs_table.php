<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Mortgage\Models\Program;

return new class extends Migration
{
    private string $tableName = 'mortgage_programs';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_id')->default(Program::DEFAULT_INT_VALUE)->nullable()->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Program::PRIORITY_DEFAULT)->index();

            $table->unsignedTinyInteger('min_time')->default(Program::MIN_INT_VALUE);
            $table->unsignedTinyInteger('max_time')->default(Program::MIN_INT_VALUE);
            $table->unsignedDecimal('min_payment_percent', 5, 2)->default(Program::MIN_FLOAT_VALUE);
            $table->unsignedDecimal('max_payment_percent', 5, 2)->default(Program::MIN_FLOAT_VALUE);
            $table->unsignedDecimal('rate', 5, 2)->default(Program::MIN_FLOAT_VALUE);

            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();
            $table->string('comment')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
