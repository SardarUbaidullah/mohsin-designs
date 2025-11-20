<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_minutes')->default(0); // Total minutes spent
            $table->boolean('is_running')->default(false);
            $table->timestamps();

            $table->index(['task_id', 'user_id']);
            $table->index('start_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_logs');
    }
};
