<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleModulePermission;
use Illuminate\Database\Seeder;

class RoleModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $full = ['view' => true, 'add' => true, 'generate_report' => true, 'print' => true, 'export' => true, 'request_admin_cancellation' => true];
        $none = ['view' => false, 'add' => false, 'generate_report' => false, 'print' => false, 'export' => false, 'request_admin_cancellation' => false];
        $viewOnly = ['view' => true, 'add' => false, 'generate_report' => true, 'print' => true, 'export' => true, 'request_admin_cancellation' => false];

        $permissions = [
            'admin' => [
                'collections' => ['view' => false, 'add' => false, 'generate_report' => false, 'print' => true, 'export' => true, 'request_admin_cancellation' => true],
                'official-receipts-accountable-forms' => $full,
                'reporting-abstract' => $full,
                'bank-deposit-reconciliation' => $full,
                'cheque-management' => $full,
                'user-management' => array_merge($full, ['add' => false, 'reset_password' => true, 'change_permission' => true]),
                'archives' => $full,
                'records' => $full,
            ],
            'collector' => [
                'collections' => $full,
                'official-receipts-accountable-forms' => $full,
                'reporting-abstract' => $full,
                'bank-deposit-reconciliation' => $full,
                'cheque-management' => $full,
                'user-management' => $none,
                'archives' => $full,
                'records' => $full,
            ],
            'abstract-reporting-officer' => [
                'collections' => $viewOnly,
                'official-receipts-accountable-forms' => $viewOnly,
                'reporting-abstract' => $full,
                'bank-deposit-reconciliation' => $viewOnly,
                'cheque-management' => $viewOnly,
                'user-management' => $none,
                'archives' => $viewOnly,
                'records' => $viewOnly,
            ],
        ];

        foreach ($permissions as $roleSlug => $modules) {
            $role = Role::where('slug', $roleSlug)->first();

            foreach ($modules as $module => $values) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module' => $module],
                    array_merge(['reset_password' => false, 'change_permission' => false], $values),
                );
            }
        }
    }
}
