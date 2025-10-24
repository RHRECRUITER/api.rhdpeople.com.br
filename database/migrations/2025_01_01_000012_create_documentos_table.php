<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('colaborador_id')->nullable()->constrained('colaboradores')->onDelete('cascade');
            $table->string('titulo', 200);
            $table->text('descricao')->nullable();
            $table->string('arquivo_url');
            $table->string('tipo_arquivo', 50)->nullable();
            $table->integer('tamanho')->nullable(); // em bytes
            $table->enum('categoria', ['contrato', 'folha', 'atestado', 'outro'])->default('outro');
            $table->timestamps();
            
            $table->index(['empresa_id', 'colaborador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
