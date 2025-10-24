<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ponto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->date('data');
            $table->time('entrada')->nullable();
            $table->time('saida_almoco')->nullable();
            $table->time('retorno_almoco')->nullable();
            $table->time('saida')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'colaborador_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponto');
    }
};
