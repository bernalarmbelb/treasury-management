<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_stocks', function (Blueprint $table) {
            $table->time('added_time')->nullable()->after('added_date');
        });

        $defaultTimes = [
            'BIR0016' => '09:15:00',
            'BIR0017' => '10:45:00',
            'Form 53' => '11:00:00',
            'Form 28A' => '12:30:00',
            'Form 10' => '13:20:00',
            'Form 5IC' => '14:50:00',
            'Form 56' => '15:10:00',
            'Form 58' => '16:40:00',
        ];

        foreach ($defaultTimes as $formCode => $time) {
            DB::table('form_stocks')->where('form_code', $formCode)->update(['added_time' => $time]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_stocks', function (Blueprint $table) {
            $table->dropColumn('added_time');
        });
    }
};
