<?php

namespace Database\Seeders;

use App\Models\FormStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            ['qty' => 25, 'form_name' => 'Individual Cedula', 'form_code' => 'BIR0016', 'added_date' => '2023-01-12', 'added_time' => '09:15:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'Corporation Cedula', 'form_code' => 'BIR0017', 'added_date' => '2021-12-19', 'added_time' => '10:45:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'Certificate of Ownership of Large Cattle', 'form_code' => 'Form 53', 'added_date' => '2022-03-01', 'added_time' => '11:00:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'Certificate of Transfer of Large Cattle', 'form_code' => 'Form 28A', 'added_date' => '2022-07-04', 'added_time' => '12:30:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'Marriage License', 'form_code' => 'Form 10', 'added_date' => '2023-03-08', 'added_time' => '13:20:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 0, 'form_name' => 'Official Receipt', 'form_code' => 'Form 5IC', 'added_date' => '2020-02-29', 'added_time' => '14:50:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'OR RPT', 'form_code' => 'Form 56', 'added_date' => '2022-09-14', 'added_time' => '15:10:00', 'added_by' => 'Marlaw Sol Emata'],
            ['qty' => 25, 'form_name' => 'Burial', 'form_code' => 'Form 58', 'added_date' => '2022-10-11', 'added_time' => '16:40:00', 'added_by' => 'Marlaw Sol Emata'],
        ];

        foreach ($forms as $form) {
            FormStock::create($form);
        }
    }
}
