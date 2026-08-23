<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \App\Support\PaymentBackfill::run();
    }

    public function down(): void
    {
        // Non-reversible data backfill; the column drop lives in the schema migration.
    }
};
