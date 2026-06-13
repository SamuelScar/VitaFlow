<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convites_admin', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('token_hash', 64)->unique();
            $table->foreignId('convidado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expira_em');
            $table->timestamp('aceito_em')->nullable();
            $table->timestamp('cancelado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convites_admin');
    }
};
