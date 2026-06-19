<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            ['action' => 'User Management - Add User', 'date' => '2023-01-14 09:00:00'],
            ['action' => 'Collection Management - Add Entry', 'date' => '2023-03-02 10:30:00'],
            ['action' => 'Collection Management - Add Entry', 'date' => '2023-08-19 11:15:00'],
            ['action' => 'Collection Management - Add Entry', 'date' => '2024-02-05 14:20:00'],
            ['action' => 'User Management - Add User', 'date' => '2024-06-17 09:45:00'],
            ['action' => 'Collection Management - Add Entry', 'date' => '2024-11-30 13:10:00'],
            ['action' => 'User Management - Add User', 'date' => '2025-04-12 08:30:00'],
            ['action' => 'Collection Management - Add Entry', 'date' => '2025-09-25 16:00:00'],
        ];

        foreach ($entries as $entry) {
            $log = ActivityLog::create([
                'user_id' => 1,
                'action' => $entry['action'],
            ]);
            $log->forceFill(['created_at' => $entry['date'], 'updated_at' => $entry['date']])->save();
        }
    }
}
