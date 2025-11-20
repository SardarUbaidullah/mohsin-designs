<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // Migration file
public function up()
{
    Schema::table('projects', function (Blueprint $table) {
        $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        
        // Optional: Add index for better performance
        $table->index('created_by');
    });
}

public function down()
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropForeign(['created_by']);
        $table->dropColumn('created_by');
    });
}
};
