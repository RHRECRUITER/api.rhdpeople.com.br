<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignUuid('plan_tier_id')->constrained('plan_tiers')->cascadeOnDelete();
            $table->foreignUuid('employee_range_id')->constrained('employee_range_multipliers')->cascadeOnDelete();
            $table->enum('billing_cycle', ['monthly', 'annual']);
            $table->decimal('price_base', 10, 2);
            $table->decimal('price_with_multiplier', 10, 2);
            $table->decimal('price_modules', 10, 2)->default(0);
            $table->decimal('price_add_ons', 10, 2)->default(0);
            $table->decimal('price_subtotal', 10, 2);
            $table->decimal('price_discount', 10, 2)->default(0);
            $table->decimal('price_total', 10, 2);
            $table->enum('status', ['active', 'suspended', 'cancelled', 'trial'])->default('trial');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
