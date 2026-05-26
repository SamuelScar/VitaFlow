<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->unique()->constrained('agendamentos')->restrictOnDelete();
            $table->dateTime('data_coleta');
            $table->unsignedSmallInteger('quantidade_ml')->nullable();
            $table->enum('status', ['confirmada', 'recusada'])->default('confirmada');
            $table->text('motivo_recusa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacoes');
    }
};
