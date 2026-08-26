<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->date('deposit_date');
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('slip_number')->nullable();
            $table->string('prepared_by')->nullable();
            $table->timestamps();
        });

        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->foreignId('deposit_id')->nullable()->after('recon_status')->constrained()->nullOnDelete();
        });

        Schema::table('cheques', function (Blueprint $table) {
            // Reconciliation status separate from the issue status (Issued/Cancelled).
            $table->string('recon_status')->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deposit_id');
        });
        Schema::table('cheques', function (Blueprint $table) {
            $table->dropColumn('recon_status');
        });
        Schema::dropIfExists('deposits');
    }
};
