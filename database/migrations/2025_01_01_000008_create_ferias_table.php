<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ferias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->enum('tipo', ['ferias', 'licenca_medica', 'licenca_maternidade', 'licenca_paternidade', 'outro'])->default('ferias');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->integer('dias')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'reprovado', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'colaborador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ferias');
    }
};
