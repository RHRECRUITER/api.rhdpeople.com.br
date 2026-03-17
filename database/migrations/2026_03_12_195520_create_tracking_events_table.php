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
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('session_id')->index();

            $table->foreignUuid('tenant_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->enum('event_type', [
                'page_view',
                'heartbeat',
                'form_start',
                'form_submit_success',
                'restricted_click'
            ]);

            $table->string('url');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('event_type');
            $table->index('created_at');
            
            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
