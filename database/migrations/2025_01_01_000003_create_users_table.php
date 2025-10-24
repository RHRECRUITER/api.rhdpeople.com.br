<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            
            // Campos customizados para multi-tenancy
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->enum('tipo', ['super_admin', 'admin_empresa', 'user_dp'])->default('user_dp');
            $table->boolean('ativo')->default(true);
            $table->string('telefone', 20)->nullable();
            $table->timestamp('ultimo_acesso')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('empresa_id');
            $table->index('tipo');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
