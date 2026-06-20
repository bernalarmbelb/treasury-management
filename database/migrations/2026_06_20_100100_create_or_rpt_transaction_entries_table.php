<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('or_rpt_transaction_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('or_rpt_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rpt_property_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_scheme', ['full', 'installment']);
            $table->unsignedTinyInteger('installment_quarter')->nullable();
            $table->decimal('tax_due', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('penalty_percent', 5, 2)->default(0);
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('or_rpt_transaction_entries');
    }
};
