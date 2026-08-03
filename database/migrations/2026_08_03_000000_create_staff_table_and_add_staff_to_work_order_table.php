<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
        });

        Schema::table('work_order', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->after('status');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
        });

        Schema::dropIfExists('staff');
    }
};
