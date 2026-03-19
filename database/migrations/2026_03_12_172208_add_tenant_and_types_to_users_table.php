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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->after('id')->constrained('companies')->onDelete('cascade');
            
            $table->string('name')->after('tenant_id');
            $table->string('email')->unique()->after('name');
            $table->string('password')->after('email');
            
            // Enums
            $table->enum('type', ['super_admin', 'system_admin', 'company_admin', 'company_user'])->after('password');
            $table->enum('status', ['active', 'blocked', 'pending'])->default('pending')->after('type');
            
            $table->timestamp('email_verified_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'name', 'email', 'password', 'type', 'status', 'email_verified_at']);
        });
    }
   
};
