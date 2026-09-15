<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dashboard & laporan ordenan oleh scanned_at (latest / orderBy desc).
        // Index ini menghindari full-table sort sehingga dashboard muda khi donat/manis duum tengok.
        if (! Schema::hasIndex('attendances', ['scanned_at'])) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->index('scanned_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['scanned_at']);
        });
    }
};
