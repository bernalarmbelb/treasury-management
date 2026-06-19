<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->boolean('view')->default(false);
            $table->boolean('add')->default(false);
            $table->boolean('generate_report')->default(false);
            $table->boolean('print')->default(false);
            $table->boolean('export')->default(false);
            $table->boolean('request_admin_cancellation')->default(false);
            $table->boolean('reset_password')->default(false);
            $table->boolean('change_permission')->default(false);
            $table->timestamps();

            $table->unique(['role_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_module_permissions');
    }
};
