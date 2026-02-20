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
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Adiciona valor ao tipo ENUM existente
            // Verifica se o valor já existe antes de adicionar
            $enumExists = DB::select("
                SELECT 1 FROM pg_enum 
                WHERE enumlabel = 'plan_assigned' 
                AND enumtypid = (
                    SELECT oid FROM pg_type WHERE typname = 'credit_logs_action_type_enum'
                )
            ");
            
            if (empty($enumExists)) {
                DB::statement("ALTER TYPE credit_logs_action_type_enum ADD VALUE 'plan_assigned'");
            }
        } else {
            // MySQL: Modifica o ENUM diretamente
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL: Reverte o ENUM
            DB::statement("ALTER TABLE credit_logs MODIFY COLUMN action_type ENUM(
                'credit_added',
                'extra_credit_added',
                'credit_used',
                'credit_returned',
                'plan_created',
                'plan_extended'
            ) NOT NULL");
        }
        
        // Para PostgreSQL, deixamos como está (não dá para remover valor de ENUM facilmente)
    }
};
