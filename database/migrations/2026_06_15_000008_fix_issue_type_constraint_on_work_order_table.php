<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SQLite tidak mendukung ALTER COLUMN, jadi kita rebuild tabel
     * untuk menghapus CHECK constraint lama pada kolom issue_type.
     */
    public function up(): void
    {
        // Salin semua data yang ada ke tabel sementara
        DB::statement('CREATE TABLE work_order_backup AS SELECT * FROM work_order');

        // Hapus tabel lama (beserta semua constraint-nya)
        Schema::drop('work_order');

        // Buat ulang tabel tanpa CHECK constraint
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->string('department', 100);
            $table->string('issue_type', 100);  // string biasa, tanpa CHECK constraint
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable()->comment('Durasi pengerjaan dalam menit');
            $table->enum('status', ['Pending', 'On Progress', 'Completed'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
        });

        // Kembalikan semua data dari backup
        DB::statement('INSERT INTO work_order SELECT * FROM work_order_backup');

        // Hapus tabel backup
        DB::statement('DROP TABLE work_order_backup');
    }

    public function down(): void
    {
        // Tidak perlu rollback khusus
    }
};