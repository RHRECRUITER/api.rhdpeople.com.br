<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conn_rhdpeople_landingpage';

    public function up(): void
    {
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->date('date');
            $table->string('page_path');

            $table->integer('total_views')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->float('bounce_rate')->default(0);

            $table->unique(['date', 'page_path']);
            $table->index('date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_metrics');
    }
};
