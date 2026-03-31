<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\SubscriptionInterval;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id') -> primary();
            $table->string('type_plan') ;
            $table->integer('number_employees')-> default(1);
            $table->enum('subscription_interval', array_column(SubscriptionInterval::cases(),'value'));
            $table->decimal('price_plan', 10, 2) -> default(1);
            $table->decimal('price_subscription', 10, 2) -> default(1);
            $table->json('modules');
            $table->json('add_ons');
            $table->timestamps();
            $table->engine('InnoDB');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }

};
