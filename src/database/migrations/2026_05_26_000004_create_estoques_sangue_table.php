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
        Schema::create('estoques_sangue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_coleta_id')->constrained('locais_coleta')->cascadeOnDelete();
            $table->enum('tipo_sanguineo', TipoSanguineo::values());
            $table->unsignedInteger('quantidade_ml')->default(0);
            $table->unsignedInteger('bolsas_disponiveis')->default(0);
            $table->unsignedInteger('estoque_minimo_ml')->default(0);
            $table->timestamps();

            $table->unique(['local_coleta_id', 'tipo_sanguineo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoques_sangue');
    }
};
