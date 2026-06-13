<?php

use App\Support\TipoSanguineo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bolsas_sangue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doacao_id')->unique()->constrained('doacoes')->restrictOnDelete();
            $table->foreignId('local_coleta_id')->constrained('locais_coleta')->restrictOnDelete();
            $table->enum('tipo_sanguineo', TipoSanguineo::values());
            $table->unsignedSmallInteger('quantidade_ml');
            $table->dateTime('data_coleta');
            $table->dateTime('validade_em');
            $table->enum('status', ['disponivel', 'utilizada', 'vencida', 'descartada', 'transferida'])
                ->default('disponivel');
            $table->timestamps();

            $table->index(['local_coleta_id', 'tipo_sanguineo', 'status']);
            $table->index('validade_em');
        });

        DB::table('doacoes')
            ->join('agendamentos', 'agendamentos.id', '=', 'doacoes.agendamento_id')
            ->join('campanhas', 'campanhas.id', '=', 'agendamentos.campanha_id')
            ->join('users', 'users.id', '=', 'agendamentos.user_id')
            ->where('doacoes.status', 'confirmada')
            ->whereNotNull('doacoes.quantidade_ml')
            ->whereNotNull('users.tipo_sanguineo')
            ->orderBy('doacoes.id')
            ->select([
                'doacoes.id',
                'doacoes.data_coleta',
                'doacoes.quantidade_ml',
                'campanhas.local_coleta_id',
                'users.tipo_sanguineo',
            ])
            ->each(function (object $doacao): void {
                $dataColeta = CarbonImmutable::parse($doacao->data_coleta);

                DB::table('bolsas_sangue')->insert([
                    'doacao_id' => $doacao->id,
                    'local_coleta_id' => $doacao->local_coleta_id,
                    'tipo_sanguineo' => $doacao->tipo_sanguineo,
                    'quantidade_ml' => $doacao->quantidade_ml,
                    'data_coleta' => $dataColeta,
                    'validade_em' => $dataColeta->addDays(42),
                    'status' => 'disponivel',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        DB::table('locais_coleta')->orderBy('id')->each(function (object $local): void {
            foreach (TipoSanguineo::values() as $tipoSanguineo) {
                DB::table('estoques_sangue')->insertOrIgnore([
                    'local_coleta_id' => $local->id,
                    'tipo_sanguineo' => $tipoSanguineo,
                    'estoque_minimo_ml' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('estoques_sangue', function (Blueprint $table) {
            $table->dropColumn(['quantidade_ml', 'bolsas_disponiveis']);
        });
    }

    public function down(): void
    {
        Schema::table('estoques_sangue', function (Blueprint $table) {
            $table->unsignedInteger('quantidade_ml')->default(0);
            $table->unsignedInteger('bolsas_disponiveis')->default(0);
        });

        DB::table('bolsas_sangue')
            ->whereIn('status', ['disponivel', 'transferida'])
            ->where('validade_em', '>', now())
            ->selectRaw('local_coleta_id, tipo_sanguineo, count(*) as bolsas_disponiveis, sum(quantidade_ml) as quantidade_ml')
            ->groupBy('local_coleta_id', 'tipo_sanguineo')
            ->each(function (object $estoque): void {
                DB::table('estoques_sangue')
                    ->where('local_coleta_id', $estoque->local_coleta_id)
                    ->where('tipo_sanguineo', $estoque->tipo_sanguineo)
                    ->update([
                        'quantidade_ml' => $estoque->quantidade_ml,
                        'bolsas_disponiveis' => $estoque->bolsas_disponiveis,
                        'updated_at' => now(),
                    ]);
            });

        Schema::dropIfExists('bolsas_sangue');
    }
};
