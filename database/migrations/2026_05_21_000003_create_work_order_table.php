<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->string('department', 100);
            $table->string('issue_type', 100);
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'On Progress', 'Completed'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};
