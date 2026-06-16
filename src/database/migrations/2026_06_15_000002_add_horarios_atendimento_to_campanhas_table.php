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
        Schema::table('campanhas', function (Blueprint $table): void {
            if (! Schema::hasColumn('campanhas', 'horario_inicio')) {
                $table->time('horario_inicio')->default('08:00')->after('agendamentos_por_horario');
            }

            if (! Schema::hasColumn('campanhas', 'horario_fim')) {
                $table->time('horario_fim')->default('17:00')->after('horario_inicio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campanhas', function (Blueprint $table): void {
            if (Schema::hasColumn('campanhas', 'horario_inicio')) {
                $table->dropColumn('horario_inicio');
            }

            if (Schema::hasColumn('campanhas', 'horario_fim')) {
                $table->dropColumn('horario_fim');
            }
        });
    }
};
