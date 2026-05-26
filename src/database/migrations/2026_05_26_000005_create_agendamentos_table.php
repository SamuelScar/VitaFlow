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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carteira_doacao_id')->constrained('carteiras_doacao')->restrictOnDelete();
            $table->foreignId('campanha_id')->constrained('campanhas')->restrictOnDelete();
            $table->dateTime('data_hora');
            $table->enum('status', ['agendado', 'cancelado', 'realizado', 'faltou'])->default('agendado');
            $table->timestamps();

            $table->unique(['carteira_doacao_id', 'campanha_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
