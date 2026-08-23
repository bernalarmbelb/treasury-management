<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('account_name');            // snapshot at time of issue
            $table->date('cheque_date');
            $table->string('check_number');
            $table->string('pay_to_order_of');
            $table->decimal('amount', 12, 2);
            $table->string('amount_in_words');
            $table->string('nature_of_payment')->nullable();
            $table->string('status')->default('Issued'); // Issued | Cancelled
            $table->string('created_by')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            // A cheque number is unique within a bank account.
            $table->unique(['bank_account_id', 'check_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
        Schema::dropIfExists('bank_accounts');
    }
};
