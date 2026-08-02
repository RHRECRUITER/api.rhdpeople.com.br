<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_rhdpeople_geral';

    public function up(): void
    {
        Schema::connection($this->connection)->create('sys_logs_archive', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->uuid('tenant_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('correlation_id')->nullable();

            $table->string('type');
            $table->string('action');
            $table->enum('severity', ['debug', 'info', 'warning', 'error', 'critical']);

            $table->string('auditable_type')->nullable();
            $table->uuid('auditable_id')->nullable();

            $table->json('payload')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at');
            $table->timestamp('archived_at')->useCurrent();

            $table->index(['tenant_id', 'type', 'created_at']);
            $table->index('correlation_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('sys_logs_archive');
    }
};
