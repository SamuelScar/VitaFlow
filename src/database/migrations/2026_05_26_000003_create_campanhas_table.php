<?php

use App\Support\TipoSanguineo;
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
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criada_por_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('local_coleta_id')->constrained('locais_coleta')->restrictOnDelete();
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo_sanguineo_alvo', TipoSanguineo::values())->nullable();
            $table->unsignedInteger('meta_bolsas');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->enum('status', ['ativa', 'encerrada', 'cancelada'])->default('ativa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
