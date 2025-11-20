<?php
// database/migrations/2025_11_07_153516_create_polymorphic_comments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('commentable_type'); // Project, Task, etc.
            $table->unsignedBigInteger('commentable_id');
            $table->string('type')->default('general'); // general, internal, client
            $table->boolean('is_internal')->default(false); // Only visible to team, not client
            $table->timestamps();

            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
