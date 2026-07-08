<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_range_multipliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plan_tier_id')->constrained('plan_tiers')->cascadeOnDelete();
            $table->string('range_label');
            $table->integer('min_employees');
            $table->integer('max_employees')->nullable();
            $table->decimal('price_multiplier', 4, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_range_multipliers');
    }
};
