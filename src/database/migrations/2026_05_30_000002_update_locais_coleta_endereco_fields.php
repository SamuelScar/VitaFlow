<?php

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
        Schema::table('locais_coleta', function (Blueprint $table) {
            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 30)->nullable();
            $table->string('bairro')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('complemento')->nullable();
        });

        DB::statement('UPDATE locais_coleta SET logradouro = endereco WHERE logradouro IS NULL');

        Schema::table('locais_coleta', function (Blueprint $table) {
            $table->dropColumn('endereco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locais_coleta', function (Blueprint $table) {
            $table->string('endereco')->nullable();
        });

        DB::statement("UPDATE locais_coleta SET endereco = COALESCE(logradouro, '')");

        Schema::table('locais_coleta', function (Blueprint $table) {
            $table->dropColumn([
                'cep',
                'logradouro',
                'numero',
                'bairro',
                'uf',
                'complemento',
            ]);
        });
    }
};
