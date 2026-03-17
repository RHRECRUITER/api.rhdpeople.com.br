<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id')->index();
            
            $table->foreignUuid('tenant_id')->nullable()->constrained('companies');
            
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->string('landing_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->boolean('is_converted')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_blocks');
    }
};