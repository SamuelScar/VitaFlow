<?php

use App\Support\TipoSanguineo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            $table->json('tipos_sanguineos_alvo')->nullable();
        });

        DB::table('campanhas')
            ->whereNotNull('tipo_sanguineo_alvo')
            ->orderBy('id')
            ->get(['id', 'tipo_sanguineo_alvo'])
            ->each(function (object $campanha): void {
                DB::table('campanhas')
                    ->where('id', $campanha->id)
                    ->update([
                        'tipos_sanguineos_alvo' => json_encode([$campanha->tipo_sanguineo_alvo]),
                    ]);
            });

        Schema::table('campanhas', function (Blueprint $table) {
            $table->dropColumn('tipo_sanguineo_alvo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            $table->enum('tipo_sanguineo_alvo', TipoSanguineo::values())->nullable();
        });

        DB::table('campanhas')
            ->whereNotNull('tipos_sanguineos_alvo')
            ->orderBy('id')
            ->get(['id', 'tipos_sanguineos_alvo'])
            ->each(function (object $campanha): void {
                $tiposSanguineos = json_decode((string) $campanha->tipos_sanguineos_alvo, true);
                $tipoSanguineo = is_array($tiposSanguineos) ? ($tiposSanguineos[0] ?? null) : null;

                if ($tipoSanguineo === null) {
                    return;
                }

                DB::table('campanhas')
                    ->where('id', $campanha->id)
                    ->update([
                        'tipo_sanguineo_alvo' => $tipoSanguineo,
                    ]);
            });

        Schema::table('campanhas', function (Blueprint $table) {
            $table->dropColumn('tipos_sanguineos_alvo');
        });
    }
};
