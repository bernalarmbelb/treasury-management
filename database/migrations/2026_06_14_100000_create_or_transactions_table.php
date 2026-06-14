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
        Schema::create('or_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_stock_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number');
            $table->date('date_issued')->nullable();
            $table->string('agency')->nullable();
            $table->string('fund')->nullable();
            $table->string('payor');
            $table->json('items');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->string('payment_method')->default('cash');
            $table->string('drawee_bank')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('or_transactions');
    }
};
