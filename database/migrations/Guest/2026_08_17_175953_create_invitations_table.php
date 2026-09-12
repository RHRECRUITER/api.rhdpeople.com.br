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
        Schema::create('invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('token');
            $table->string('email');
            $table->enum('type_license', ['operacional', 'colaborador'])->default('operacional');
            $table->date('expiration_date');
            $table->timestamp('consumed_at')->nullable();
            $table->foreignUuid('plan_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }

};
