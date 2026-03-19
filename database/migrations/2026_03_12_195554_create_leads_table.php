<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_rhdpeople_landingpage';
    
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Link para comportamento na LP (ID da sessão nativa)
            $table->string('session_id')->nullable(); 
            
            $table->string('name')->nullable();
            $table->string('email')->nullable(); // nullable caso comece como draft só com tel/nome
            $table->string('phone')->nullable();
            
            $table->enum('status', ['draft', 'converted'])->default('draft');
            $table->string('last_field_filled')->nullable();
            
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            // Indexes conforme DBML
            $table->index('session_id');
            $table->index('email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};