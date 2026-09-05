<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('receipt_token', 80)->nullable()->unique()->after('payment_status');
            $table->timestamp('receipt_sent_at')->nullable()->after('receipt_token');
            $table->timestamp('receipt_verified_at')->nullable()->after('receipt_sent_at');
            $table->foreignId('receipt_verified_by')->nullable()->after('receipt_verified_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('receipt_verified_by');
            $table->dropColumn(['receipt_token', 'receipt_sent_at', 'receipt_verified_at']);
        });
    }
};
