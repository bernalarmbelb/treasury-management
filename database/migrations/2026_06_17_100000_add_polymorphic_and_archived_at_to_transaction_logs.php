<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->nullableMorphs('transaction');
            $table->timestamp('archived_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropMorphs('transaction');
            $table->dropColumn('archived_at');
        });
    }
};
