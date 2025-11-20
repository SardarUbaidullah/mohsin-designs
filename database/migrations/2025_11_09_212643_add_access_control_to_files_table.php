<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('description');
            $table->json('accessible_users')->nullable()->after('is_public'); // Store user IDs as JSON array
        });
    }

    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'accessible_users']);
        });
    }
};
