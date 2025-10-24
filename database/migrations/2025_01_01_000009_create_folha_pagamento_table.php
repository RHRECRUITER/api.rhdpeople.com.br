<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folha_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->integer('mes');
            $table->integer('ano');
            $table->decimal('salario_base', 10, 2);
            $table->decimal('horas_extras', 10, 2)->default(0);
            $table->decimal('descontos', 10, 2)->default(0);
            $table->decimal('bonificacoes', 10, 2)->default(0);
            $table->decimal('salario_liquido', 10, 2);
            $table->enum('status', ['calculado', 'pago', 'cancelado'])->default('calculado');
            $table->date('data_pagamento')->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'colaborador_id', 'ano', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folha_pagamento');
    }
};
