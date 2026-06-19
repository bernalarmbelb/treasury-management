<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::where('slug', 'admin')->first();
        $collector = Role::where('slug', 'collector')->first();
        $aro = Role::where('slug', 'abstract-reporting-officer')->first();

        // The auto-logged-in session user (id 1), repurposed as the UM admin.
        $marlaw = User::find(1);
        $marlaw->update([
            'username' => 'memata',
            'name' => 'Marlaw Sol Emata',
            'email' => 'memata@solemitsolutions.com',
            'mobile' => '+63 912 345 6780',
            'status' => User::STATUS_ACTIVATED,
            'added_by' => 'System',
        ]);
        $marlaw->roles()->sync([$admin->id]);
        $marlaw->forceFill(['created_at' => '2023-01-14 09:00:00', 'updated_at' => '2023-01-14 09:00:00'])->save();

        $demoUsers = [
            ['username' => 'jdelacruz', 'name' => 'Juan Dela Cruz', 'email' => 'jdelacruz@solemitsolutions.com', 'mobile' => '+63 912 345 6781', 'role' => $collector, 'status' => User::STATUS_ACTIVATED, 'date' => '2023-03-02 10:30:00'],
            ['username' => 'msantos', 'name' => 'Maria Santos', 'email' => 'msantos@solemitsolutions.com', 'mobile' => '+63 912 345 6782', 'role' => $collector, 'status' => User::STATUS_ACTIVATED, 'date' => '2023-08-19 11:15:00'],
            ['username' => 'preyes', 'name' => 'Pedro Reyes', 'email' => 'preyes@solemitsolutions.com', 'mobile' => '+63 912 345 6783', 'role' => $aro, 'status' => User::STATUS_ACTIVATED, 'date' => '2024-02-05 14:20:00'],
            ['username' => 'agarcia', 'name' => 'Ana Garcia', 'email' => 'agarcia@solemitsolutions.com', 'mobile' => '+63 912 345 6784', 'role' => $aro, 'status' => User::STATUS_DISABLED, 'date' => '2024-06-17 09:45:00'],
            ['username' => 'jramirez', 'name' => 'Jose Ramirez', 'email' => 'jramirez@solemitsolutions.com', 'mobile' => '+63 912 345 6785', 'role' => $collector, 'status' => User::STATUS_ACTIVATED, 'date' => '2024-11-30 13:10:00'],
            ['username' => 'clopez', 'name' => 'Carmen Lopez', 'email' => 'clopez@solemitsolutions.com', 'mobile' => '+63 912 345 6786', 'role' => $aro, 'status' => User::STATUS_ACTIVATED, 'date' => '2025-04-12 08:30:00'],
            ['username' => 'rtorres', 'name' => 'Ramon Torres', 'email' => 'rtorres@solemitsolutions.com', 'mobile' => '+63 912 345 6787', 'role' => $collector, 'status' => User::STATUS_DISABLED, 'date' => '2025-09-25 16:00:00'],
        ];

        foreach ($demoUsers as $data) {
            $user = User::create([
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'password' => 'password',
                'status' => $data['status'],
                'added_by' => 'Marlaw Sol Emata',
            ]);
            $user->roles()->sync([$data['role']->id]);
            $user->forceFill(['created_at' => $data['date'], 'updated_at' => $data['date']])->save();
        }
    }
}
