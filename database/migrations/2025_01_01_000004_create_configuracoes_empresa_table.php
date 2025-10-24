<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->unique()->constrained('empresas')->onDelete('cascade');
            $table->string('logo_url')->nullable();
            $table->string('cor_primaria', 7)->default('#009E9A');
            $table->string('cor_secundaria', 7)->default('#00CFC9');
            $table->enum('layout', ['padrao', 'compacto', 'expandido'])->default('padrao');
            $table->json('modulos_ativos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_empresa');
    }
};
