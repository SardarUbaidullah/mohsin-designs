<?php
// database/migrations/2025_11_07_170321_add_client_manager_to_projects.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration  // Use anonymous class to avoid name conflicts
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['client_id', 'manager_id']);
        });
    }
};
