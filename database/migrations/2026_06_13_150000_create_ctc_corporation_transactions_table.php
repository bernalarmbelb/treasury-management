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
        Schema::create('ctc_corporation_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_stock_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number');
            $table->unsignedSmallInteger('year');
            $table->string('place_of_issue')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('company_name');
            $table->string('tin')->nullable();
            $table->date('date_of_registration')->nullable();
            $table->string('address')->nullable();
            $table->string('kind_of_organization')->nullable();
            $table->string('nature_of_business')->nullable();
            $table->decimal('a_community_tax_due', 10, 2)->default(0);
            $table->decimal('item1_taxable_amount', 10, 2)->default(0);
            $table->decimal('item1_community_tax_due', 10, 2)->default(0);
            $table->decimal('item2_taxable_amount', 10, 2)->default(0);
            $table->decimal('item2_community_tax_due', 10, 2)->default(0);
            $table->decimal('total_community_tax_due', 10, 2)->default(0);
            $table->decimal('interest', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctc_corporation_transactions');
    }
};
