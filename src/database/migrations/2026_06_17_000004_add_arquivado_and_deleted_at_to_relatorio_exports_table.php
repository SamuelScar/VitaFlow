<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatorio_exports', function (Blueprint $table) {
            $table->boolean('is_arquivado')->default(false)->after('status');
            $table->softDeletes()->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('relatorio_exports', function (Blueprint $table) {
            $table->dropColumn('is_arquivado');
            $table->dropSoftDeletes();
        });
    }
};
