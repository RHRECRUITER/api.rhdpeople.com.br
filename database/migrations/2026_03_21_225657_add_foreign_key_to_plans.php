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
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignUuid('company_id') -> references('id')->on('companies')->onDelete('restrict');
            $table->foreignUuid('contract_id') -> references('id')->on('contracts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropaForeign(['company_id']);
            $table->dropForeign(['contract_id']);
        });
    }
};
