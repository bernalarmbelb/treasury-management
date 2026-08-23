<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('status');
            $table->string('payment_method')->default('cash')->after('amount');
            $table->string('payment_channel')->nullable()->after('payment_method');
            $table->string('payer_bank_name')->nullable()->after('payment_channel');
            $table->string('payment_reference')->nullable()->after('payer_bank_name');
            $table->date('payment_reference_date')->nullable()->after('payment_reference');
            $table->string('recon_status')->default('pending')->after('payment_reference_date');
        });

        Schema::table('marriage_certificate_transactions', function (Blueprint $table) {
            $table->decimal('fee_amount', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropColumn(['amount', 'payment_method', 'payment_channel', 'payer_bank_name', 'payment_reference', 'payment_reference_date', 'recon_status']);
        });

        Schema::table('marriage_certificate_transactions', function (Blueprint $table) {
            $table->dropColumn('fee_amount');
        });
    }
};
