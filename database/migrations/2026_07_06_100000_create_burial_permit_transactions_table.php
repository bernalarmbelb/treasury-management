<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('burial_permit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_stock_id')->constrained('form_stocks');
            $table->string('certificate_number');
            $table->string('series_letter')->nullable();

            // Applicant / issuing LGU
            $table->string('applicant_name')->nullable();      // "Mr."
            $table->string('city_municipality')->nullable();   // To the City/Municipality of
            $table->string('province')->nullable();

            // Permission granted: Inter / Disinter / Remove
            $table->string('permission_type')->nullable();

            // Deceased details (paper lines 1–6)
            $table->string('deceased_name');                   // 1
            $table->string('nationality')->nullable();         // 2
            $table->unsignedSmallInteger('age')->nullable();   // 3
            $table->string('sex')->nullable();                 // 3
            $table->date('date_of_death')->nullable();         // 4
            $table->string('cause_of_death')->nullable();      // 5
            $table->string('cemetery_name')->nullable();       // 6

            // In case of disinterment (paper lines 7–9)
            $table->string('infectious')->nullable();          // 7
            $table->string('embalmed')->nullable();            // 8
            $table->string('disposition')->nullable();         // 9

            // Fee receipt (paper line 10 + certification)
            $table->decimal('fee_amount', 12, 2)->nullable();  // 10
            $table->date('date_issued')->nullable();
            $table->string('municipal_secretary')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('burial_permit_transactions');
    }
};
