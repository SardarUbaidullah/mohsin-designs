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
    Schema::table('files', function (Blueprint $table) {
        if (!Schema::hasColumn('files', 'file_size')) {
            $table->bigInteger('file_size')->nullable();
        }
        if (!Schema::hasColumn('files', 'mime_type')) {
            $table->string('mime_type')->nullable();
        }
        if (!Schema::hasColumn('files', 'version')) {
            $table->integer('version')->default(1);
        }
        if (!Schema::hasColumn('files', 'parent_id')) {
            $table->unsignedBigInteger('parent_id')->nullable();
        }
        if (!Schema::hasColumn('files', 'description')) {
            $table->text('description')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('files', function (Blueprint $table) {
        $table->dropColumn(['file_size', 'mime_type', 'version', 'parent_id', 'description']);
    });
}

};
