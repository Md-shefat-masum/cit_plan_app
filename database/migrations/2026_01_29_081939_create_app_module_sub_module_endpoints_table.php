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
        Schema::dropIfExists('app_module_sub_module_endpoints');
        
        Schema::create('app_module_sub_module_endpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_module_id')->nullable();
            $table->unsignedBigInteger('app_module_sub_module_id')->nullable();

            $table->string('uri', 200)->nullable();
            $table->string('action_key', 100)->nullable();
            $table->string('title', 100)->nullable();

            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('slug', 100)->nullable()->unique();
            $table->bigInteger('creator')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('app_module_id')->references('id')->on('app_modules')->onDelete('set null');
            $table->foreign('app_module_sub_module_id')->references('id')->on('app_module_sub_modules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_module_sub_module_endpoints');
    }
};
