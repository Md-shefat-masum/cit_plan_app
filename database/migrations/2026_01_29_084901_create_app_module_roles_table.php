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
        Schema::dropIfExists('app_module_roles');
        
        Schema::create('app_module_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_module_id')->nullable();
            $table->unsignedBigInteger('app_module_sub_module_id')->nullable();
            $table->unsignedBigInteger('app_module_sub_module_endpoint_id')->nullable();
            $table->string('uri', 200)->nullable();
            $table->unsignedBigInteger('user_role_id')->nullable();
            
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('slug', 100)->nullable()->unique();
            $table->bigInteger('creator')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('app_module_id')->references('id')->on('app_modules')->onDelete('set null');
            $table->foreign('app_module_sub_module_id')->references('id')->on('app_module_sub_modules')->onDelete('set null');
            $table->foreign('app_module_sub_module_endpoint_id')->references('id')->on('app_module_sub_module_endpoints')->onDelete('set null');
            $table->foreign('user_role_id')->references('id')->on('user_roles')->onDelete('set null');
            
            // Unique constraint to prevent duplicate permissions by URI (since endpoint IDs can change)
            $table->unique(['uri', 'user_role_id'], 'unique_permission_by_uri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_module_roles');
    }
};
