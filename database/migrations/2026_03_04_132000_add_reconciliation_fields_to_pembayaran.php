<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->enum('metode_bayar', ['transfer', 'cash', 'va', 'qris'])->default('transfer')->after('jumlah_bayar');
            $table->string('bukti_bayar', 255)->nullable()->after('metode_bayar');
            $table->boolean('is_reconciliation_error')->default(false)->after('bukti_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['metode_bayar', 'bukti_bayar', 'is_reconciliation_error']);
        });
    }
};
