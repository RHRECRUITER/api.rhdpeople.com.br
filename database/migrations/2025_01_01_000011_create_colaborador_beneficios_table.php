<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaborador_beneficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->foreignId('beneficio_id')->constrained('beneficios')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['colaborador_id', 'beneficio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaborador_beneficios');
    }
};
