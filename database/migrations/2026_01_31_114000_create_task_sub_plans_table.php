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
        Schema::create('task_sub_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_plan_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('time_duration_id')->nullable();
            $table->unsignedBigInteger('time_sub_duration_id')->nullable();
            $table->unsignedBigInteger('task_completor_category_id')->nullable();
            $table->unsignedBigInteger('task_completor_sub_category_id')->nullable();
            $table->unsignedBigInteger('umbrella_department_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('slug', 100)->unique();
            $table->bigInteger('creator')->default(0);
            $table->timestamps();

            $table->foreign('task_plan_id')->references('id')->on('task_plans')->onDelete('set null');
            $table->foreign('time_duration_id')->references('id')->on('time_durations')->onDelete('set null');
            $table->foreign('time_sub_duration_id')->references('id')->on('time_sub_durations')->onDelete('set null');
            $table->foreign('task_completor_category_id')->references('id')->on('task_completor_categories')->onDelete('set null');
            $table->foreign('task_completor_sub_category_id')->references('id')->on('task_completor_sub_categories')->onDelete('set null');
            $table->foreign('umbrella_department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_sub_plans');
    }
};
