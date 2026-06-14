<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('or_rpt_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_stock_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number');
            $table->string('previous_receipt_number')->nullable();
            $table->string('previous_receipt_date')->nullable();
            $table->string('previous_receipt_year')->nullable();
            $table->string('municipality_province')->nullable();
            $table->string('city')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('client_name');
            $table->string('payment_in_words')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('treasurer_deputy')->nullable();
            $table->boolean('basic_tax')->default(false);
            $table->boolean('special_education_fund')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('or_rpt_transactions');
    }
};
