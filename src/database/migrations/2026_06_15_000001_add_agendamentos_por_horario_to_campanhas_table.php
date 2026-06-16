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
            if (! Schema::hasColumn('campanhas', 'agendamentos_por_horario')) {
                $table->unsignedSmallInteger('agendamentos_por_horario')->default(4)->after('meta_bolsas');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campanhas', function (Blueprint $table): void {
            if (Schema::hasColumn('campanhas', 'agendamentos_por_horario')) {
                $table->dropColumn('agendamentos_por_horario');
            }

        });
    }
};
