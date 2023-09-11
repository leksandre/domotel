<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\EstateImport\Models\Enums\HistoryState;

return new class extends Migration
{
    private string $tableName = 'estate_import_history';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->enum(
                'state',
                [
                    HistoryState::New->value,
                    HistoryState::PreProcess->value,
                    HistoryState::Ready->value,
                    HistoryState::Process->value,
                    HistoryState::Done->value,
                    HistoryState::Error->value
                ]
            )->index();
            $table->char('hash', 128)->nullable()->index();
            $table->string('batch_id')->nullable();
            $table->string('pre_processor')->nullable();
            $table->json('pre_processor_data')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
