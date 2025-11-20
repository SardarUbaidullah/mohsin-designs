<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // chat_message, task_assigned, project_created, file_uploaded, etc.
            $table->morphs('notifiable'); // user_id
            $table->text('data'); // JSON data
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type']);
            $table->index('read_at');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
