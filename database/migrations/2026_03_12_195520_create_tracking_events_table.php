<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_rhdpeople_landingpage';
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('session_id')->index();

            $table->uuid('tenant_id')->nullable()->index();
            $table->uuid('user_id')->nullable()->index();

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
