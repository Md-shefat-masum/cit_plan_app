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
        Schema::create('task_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('si')->unique();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('department_section_id')->nullable();
            $table->unsignedBigInteger('department_sub_section_id')->nullable();
            $table->unsignedBigInteger('dofa_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('qty')->default(0);
            $table->unsignedBigInteger('task_type_id')->nullable();
            $table->unsignedBigInteger('task_status_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('slug', 100)->unique();
            $table->bigInteger('creator')->default(0);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('department_section_id')->references('id')->on('department_sections')->onDelete('set null');
            $table->foreign('department_sub_section_id')->references('id')->on('department_sub_sections')->onDelete('set null');
            $table->foreign('dofa_id')->references('id')->on('dofas')->onDelete('set null');
            $table->foreign('task_type_id')->references('id')->on('task_types')->onDelete('set null');
            $table->foreign('task_status_id')->references('id')->on('task_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_plans');
    }
};
