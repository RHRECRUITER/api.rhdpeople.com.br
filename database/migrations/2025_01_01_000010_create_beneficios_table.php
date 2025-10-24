<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->enum('tipo', ['vale_transporte', 'vale_refeicao', 'plano_saude', 'plano_odonto', 'outro'])->default('outro');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficios');
    }
};
