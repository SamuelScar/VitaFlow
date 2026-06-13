<?php

use App\Support\TipoSanguineo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf', 11)->nullable()->unique();
            $table->string('telefone', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('tipo_sanguineo', TipoSanguineo::values())->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->string('cidade')->nullable();
        });

        DB::table('carteiras_doacao')->orderBy('id')->each(function (object $carteira): void {
            DB::table('users')
                ->where('id', $carteira->user_id)
                ->update([
                    'cpf' => $carteira->cpf,
                    'telefone' => $carteira->telefone,
                    'data_nascimento' => $carteira->data_nascimento,
                    'tipo_sanguineo' => $carteira->tipo_sanguineo,
                    'peso' => $carteira->peso,
                    'cidade' => $carteira->cidade,
                ]);
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
        });

        DB::table('agendamentos')->orderBy('id')->each(function (object $agendamento): void {
            $userId = DB::table('carteiras_doacao')
                ->where('id', $agendamento->carteira_doacao_id)
                ->value('user_id');

            DB::table('agendamentos')
                ->where('id', $agendamento->id)
                ->update(['user_id' => $userId]);
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropUnique(['carteira_doacao_id', 'campanha_id']);
            $table->dropConstrainedForeignId('carteira_doacao_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->unique(['user_id', 'campanha_id']);
        });

        Schema::table('carteiras_doacao', function (Blueprint $table) {
            $table->dropColumn([
                'cpf',
                'telefone',
                'data_nascimento',
                'tipo_sanguineo',
                'peso',
                'cidade',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('carteiras_doacao', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('tipo_sanguineo', TipoSanguineo::values())->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->string('cidade')->nullable();
        });

        DB::table('carteiras_doacao')->orderBy('id')->each(function (object $carteira): void {
            $user = DB::table('users')->where('id', $carteira->user_id)->first();

            DB::table('carteiras_doacao')
                ->where('id', $carteira->id)
                ->update([
                    'cpf' => $user->cpf,
                    'telefone' => $user->telefone,
                    'data_nascimento' => $user->data_nascimento,
                    'tipo_sanguineo' => $user->tipo_sanguineo,
                    'peso' => $user->peso,
                    'cidade' => $user->cidade,
                ]);
        });

        Schema::table('carteiras_doacao', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable(false)->change();
            $table->string('telefone', 20)->nullable(false)->change();
            $table->date('data_nascimento')->nullable(false)->change();
            $table->enum('tipo_sanguineo', TipoSanguineo::values())->nullable(false)->change();
            $table->decimal('peso', 5, 2)->nullable(false)->change();
            $table->string('cidade')->nullable(false)->change();
            $table->unique('cpf');
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('carteira_doacao_id')->nullable()->constrained('carteiras_doacao')->restrictOnDelete();
        });

        DB::table('agendamentos')->orderBy('id')->each(function (object $agendamento): void {
            $carteiraId = DB::table('carteiras_doacao')
                ->where('user_id', $agendamento->user_id)
                ->value('id');

            DB::table('agendamentos')
                ->where('id', $agendamento->id)
                ->update(['carteira_doacao_id' => $carteiraId]);
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'campanha_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->unsignedBigInteger('carteira_doacao_id')->nullable(false)->change();
            $table->unique(['carteira_doacao_id', 'campanha_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cpf',
                'telefone',
                'data_nascimento',
                'tipo_sanguineo',
                'peso',
                'cidade',
            ]);
        });
    }
};
