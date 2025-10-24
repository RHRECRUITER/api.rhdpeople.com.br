<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_acesso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->string('ip', 45);
            $table->string('user_agent')->nullable();
            $table->enum('acao', ['login', 'logout', 'acesso_negado'])->default('login');
            $table->timestamp('data_hora')->useCurrent();
            
            $table->index('user_id');
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_acesso');
    }
};
