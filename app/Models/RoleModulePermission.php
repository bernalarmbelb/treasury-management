<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleModulePermission extends Model
{
    /**
     * Module identifiers (matching route names) mapped to their display
     * labels, in the order shown on the Roles & Permission matrix.
     */
    public const MODULES = [
        'collections' => 'Collection Management',
        'official-receipts-accountable-forms' => 'Official Receipts & Accountable Forms',
        'reporting-abstract' => 'Reporting & Abstract',
        'bank-deposit-reconciliation' => 'Bank Deposit & Reconciliation',
        'cheque-management' => 'Cheque Management',
        'user-management' => 'User Management',
        'archives' => 'Archive',
        'records' => 'Records',
    ];

    protected $fillable = [
        'role_id',
        'module',
        'view',
        'add',
        'generate_report',
        'print',
        'export',
        'request_admin_cancellation',
        'reset_password',
        'change_permission',
    ];

    protected $casts = [
        'view' => 'boolean',
        'add' => 'boolean',
        'generate_report' => 'boolean',
        'print' => 'boolean',
        'export' => 'boolean',
        'request_admin_cancellation' => 'boolean',
        'reset_password' => 'boolean',
        'change_permission' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
