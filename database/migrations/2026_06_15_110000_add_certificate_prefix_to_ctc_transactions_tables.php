<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ctc_individual_transactions', function (Blueprint $table) {
            $table->string('certificate_prefix')->nullable()->after('form_stock_id');
        });

        Schema::table('ctc_corporation_transactions', function (Blueprint $table) {
            $table->string('certificate_prefix')->nullable()->after('form_stock_id');
        });
    }

    public function down(): void
    {
        Schema::table('ctc_individual_transactions', function (Blueprint $table) {
            $table->dropColumn('certificate_prefix');
        });

        Schema::table('ctc_corporation_transactions', function (Blueprint $table) {
            $table->dropColumn('certificate_prefix');
        });
    }
};
