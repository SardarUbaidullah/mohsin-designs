<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // migration file
public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->foreignId('milestone_id')->nullable()->constrained()->onDelete('set null');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropForeign(['milestone_id']);
        $table->dropColumn('milestone_id');
    });
}
};
