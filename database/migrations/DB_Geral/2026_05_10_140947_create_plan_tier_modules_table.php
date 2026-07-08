<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_tier_add_ons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plan_tier_id')->constrained('plan_tiers')->cascadeOnDelete();
            $table->foreignUuid('add_on_id')->constrained('add_ons')->cascadeOnDelete();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_tier_add_ons');
    }
};
