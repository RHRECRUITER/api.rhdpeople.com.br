<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nome', 200);
            $table->string('cpf', 14)->unique();
            $table->string('rg', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('telefone', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('genero', ['M', 'F', 'Outro'])->nullable();
            $table->string('cargo', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->decimal('salario', 10, 2)->nullable();
            $table->date('data_admissao')->nullable();
            $table->date('data_demissao')->nullable();
            $table->enum('status', ['ativo', 'afastado', 'demitido'])->default('ativo');
            $table->string('foto_url')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};
