<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom user_id ke tabel pelanggan.
        // nullOnDelete: bila akun user dihapus, pelanggan tetap ada (user_id jadi NULL).
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Membalik perubahan: hapus foreign key beserta kolomnya.
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
