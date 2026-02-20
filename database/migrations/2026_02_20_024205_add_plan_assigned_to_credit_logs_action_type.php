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
            // PostgreSQL: Primeiro verifica se o tipo ENUM existe
            $typeExists = DB::select("
                SELECT 1 FROM pg_type 
                WHERE typname = 'credit_logs_action_type_enum'
            ");
            
            if (!empty($typeExists)) {
                // Se o tipo ENUM existe, adiciona o novo valor
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
                // Se não existe tipo ENUM, provavelmente usa CHECK constraint
                // Então precisamos recriar a constraint
                DB::statement("ALTER TABLE credit_logs DROP CONSTRAINT IF EXISTS credit_logs_action_type_check");
                
                // Criar nova constraint com o valor adicional
                DB::statement("ALTER TABLE credit_logs ADD CONSTRAINT credit_logs_action_type_check 
                    CHECK (action_type::text = ANY (ARRAY[
                        'credit_added'::character varying,
                        'extra_credit_added'::character varying,
                        'credit_used'::character varying,
                        'credit_returned'::character varying,
                        'plan_created'::character varying,
                        'plan_extended'::character varying,
                        'plan_assigned'::character varying
                    ]::text[]))");
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
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Se usa CHECK constraint, reverte ela
            DB::statement("ALTER TABLE credit_logs DROP CONSTRAINT IF EXISTS credit_logs_action_type_check");
            
            DB::statement("ALTER TABLE credit_logs ADD CONSTRAINT credit_logs_action_type_check 
                CHECK (action_type::text = ANY (ARRAY[
                    'credit_added'::character varying,
                    'extra_credit_added'::character varying,
                    'credit_used'::character varying,
                    'credit_returned'::character varying,
                    'plan_created'::character varying,
                    'plan_extended'::character varying
                ]::text[]))");
            
            // Nota: Não é possível remover valores de um tipo ENUM no PostgreSQL facilmente
        }
    }
};
