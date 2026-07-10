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
        Schema::table('work_order', function (Blueprint $table) {
            $table->text('resolution_note')->nullable()->after('image');
            $table->timestamp('completed_at')->nullable()->after('resolution_note');
        });
    }

    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->dropColumn(['resolution_note', 'completed_at']);
        });
    }
};
