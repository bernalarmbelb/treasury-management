<?php

namespace Database\Seeders;

use App\Models\TransactionLog;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Smith, John A', 'transacted_at' => '2023-01-12 02:15:00', 'form_type' => 'Form 58', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Johnson, Emily B', 'transacted_at' => '2021-12-19 17:45:00', 'form_type' => 'BIR0017', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Williams, Michael C', 'transacted_at' => '2022-03-01 17:00:00', 'form_type' => 'Form 53', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Brown, Sarah D', 'transacted_at' => '2022-07-04 01:45:00', 'form_type' => 'Form 28A', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Jones, David E', 'transacted_at' => '2023-03-08 15:00:00', 'form_type' => 'BIR0016', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Garcia, Laura F', 'transacted_at' => '2020-02-29 10:30:00', 'form_type' => 'Form 10', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Martinez, Carlos G', 'transacted_at' => '2022-09-14 20:15:00', 'form_type' => 'Form 5IC', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Davis, Jennifer H', 'transacted_at' => '2022-10-11 07:00:00', 'form_type' => 'Form 56', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Rodriguez, Daniel I', 'transacted_at' => '2020-04-30 12:45:00', 'form_type' => 'Form 58', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Wilson, Jessica J', 'transacted_at' => '2023-05-15 21:30:00', 'form_type' => 'BIR0017', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Anderson, Brian K', 'transacted_at' => '2021-08-23 04:00:00', 'form_type' => 'Form 53', 'status' => 'Completed'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Thomas, Angela L', 'transacted_at' => '2021-06-30 23:15:00', 'form_type' => 'Form 28A', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Taylor, Kevin M', 'transacted_at' => '2022-11-05 06:00:00', 'form_type' => 'BIR0016', 'status' => 'Cancelled'],
            ['serial_number' => '324-5678-1234-02', 'payee' => 'Moore, Stephanie N', 'transacted_at' => '2023-08-18 01:30:00', 'form_type' => 'Form 10', 'status' => 'Cancelled'],
        ];

        foreach ($transactions as $transaction) {
            TransactionLog::create($transaction);
        }

        $faker = Faker::create();
        $formTypes = ['Form 58', 'BIR0017', 'Form 53', 'Form 28A', 'BIR0016', 'Form 10', 'Form 5IC', 'Form 56'];
        $statuses = ['Completed', 'Cancelled'];

        for ($i = 0; $i < 60; $i++) {
            TransactionLog::create([
                'serial_number' => sprintf('324-5678-1234-%02d', $faker->numberBetween(3, 99)),
                'payee' => $faker->lastName().', '.$faker->firstName().' '.$faker->randomLetter(),
                'transacted_at' => $faker->dateTimeBetween('-3 years', 'now'),
                'form_type' => $faker->randomElement($formTypes),
                'status' => $faker->randomElement($statuses),
            ]);
        }
    }
}
