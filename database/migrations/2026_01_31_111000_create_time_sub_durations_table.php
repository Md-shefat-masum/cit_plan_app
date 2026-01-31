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
        Schema::create('time_sub_durations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_duration_id')->nullable();
            $table->string('title', 100);
            $table->tinyInteger('status')->default(1);
            $table->string('slug', 100)->unique();
            $table->bigInteger('creator')->default(0);
            $table->timestamps();

            $table->foreign('time_duration_id')->references('id')->on('time_durations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sub_durations');
    }
};
