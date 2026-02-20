<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE credit_logs MODIFY COLUMN action_type ENUM(
            'credit_added',
            'extra_credit_added',
            'credit_used',
            'credit_returned',
            'plan_created',
            'plan_extended',
            'plan_assigned'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE credit_logs MODIFY COLUMN action_type ENUM(
            'credit_added',
            'extra_credit_added',
            'credit_used',
            'credit_returned',
            'plan_created',
            'plan_extended'
        ) NOT NULL");
    }
};
