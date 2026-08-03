<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_rhdpeople_geral';

    public function up(): void
    {
        Schema::connection($this->connection)->create('sys_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->uuid('tenant_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('correlation_id')->nullable();

            $table->string('type');
            $table->string('action');
            $table->enum('severity', ['debug', 'info', 'warning', 'error', 'critical'])
                ->default('info');

            $table->string('auditable_type')->nullable();
            $table->uuid('auditable_id')->nullable();

            $table->json('payload')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('tenant_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index(['tenant_id', 'type', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('correlation_id');
            $table->index('action');
            $table->index('created_at');
        });

        DB::connection($this->connection)->unprepared('
            CREATE TRIGGER sys_logs_prevent_update
            BEFORE UPDATE ON sys_logs
            FOR EACH ROW
            SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'You can not update on sys_log. sys_logs is append-only. If this data is realy important to update, talk to Jeff or Luan\';
        ');

        DB::connection($this->connection)->unprepared('
            CREATE TRIGGER sys_logs_prevent_delete
            BEFORE DELETE ON sys_logs
            FOR EACH ROW
            SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'You can not delete on sys_log. sys_logs is append-only. If this data is realy important to be deleted, talk to Jeff or Luan\';
        ');
    }

    public function down(): void
    {
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS sys_logs_prevent_update');
        DB::connection($this->connection)->unprepared('DROP TRIGGER IF EXISTS sys_logs_prevent_delete');

        Schema::connection($this->connection)->dropIfExists('sys_logs');
    }
};
