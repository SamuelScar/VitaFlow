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
        Schema::create('carteiras_doacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('cpf', 14)->unique();
            $table->string('telefone', 20);
            $table->date('data_nascimento');
            $table->enum('tipo_sanguineo', TipoSanguineo::values());
            $table->decimal('peso', 5, 2);
            $table->string('cidade');
            $table->enum('status', ['ativa', 'bloqueada', 'inativa'])->default('ativa');
            $table->date('emitida_em');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carteiras_doacao');
    }
};
