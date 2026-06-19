<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marriage_certificate_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_stock_id')->constrained('form_stocks');
            $table->string('certificate_number');
            $table->string('husband_name');
            $table->unsignedTinyInteger('husband_age_years')->nullable();
            $table->unsignedTinyInteger('husband_age_months')->nullable();
            $table->string('husband_address')->nullable();
            $table->string('wife_name');
            $table->unsignedTinyInteger('wife_age_years')->nullable();
            $table->unsignedTinyInteger('wife_age_months')->nullable();
            $table->string('wife_address')->nullable();
            $table->string('witness_day')->nullable();
            $table->string('witness_month')->nullable();
            $table->string('witness_year')->nullable();
            $table->string('instructions_day')->nullable();
            $table->string('instructions_month')->nullable();
            $table->string('instructions_year')->nullable();
            $table->string('registry_number')->nullable();
            $table->string('local_civil_registrar_of')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marriage_certificate_transactions');
    }
};
