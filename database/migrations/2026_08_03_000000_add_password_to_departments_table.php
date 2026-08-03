<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('departments', 'password')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('password')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('departments', 'password')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }
};
