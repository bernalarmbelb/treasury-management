<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpt_properties', function (Blueprint $table) {
            $table->id();
            $table->string('tax_declaration_number')->unique();
            $table->string('declared_owner')->nullable();
            $table->string('location')->nullable();
            $table->string('lot_block_number')->nullable();
            $table->string('municipality_province')->nullable();
            $table->string('city')->nullable();
            $table->decimal('assessed_value_land', 12, 2)->default(0);
            $table->decimal('assessed_value_improvement', 12, 2)->default(0);
            $table->decimal('assessed_value_total', 12, 2)->default(0);
            $table->decimal('annual_tax_due', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpt_properties');
    }
};
