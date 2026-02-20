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
            // PostgreSQL: Remover constraint antiga e criar nova com valores em inglês
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            
            // Criar nova constraint com ambos os valores (português e inglês) temporariamente
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
                CHECK (role::text = ANY (ARRAY[
                    'admin'::character varying,
                    'instructor'::character varying,
                    'student'::character varying,
                    'aluno'::character varying,
                    'instrutor'::character varying
                ]::text[]))");
        }
        
        // Atualizar roles de português para inglês
        DB::table('users')
            ->where('role', 'aluno')
            ->update(['role' => 'student']);
            
        DB::table('users')
            ->where('role', 'instrutor')
            ->update(['role' => 'instructor']);
            
        if ($driver === 'pgsql') {
            // Remover constraint temporária e criar a final apenas com valores em inglês
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
                CHECK (role::text = ANY (ARRAY[
                    'admin'::character varying,
                    'instructor'::character varying,
                    'student'::character varying
                ]::text[]))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Remover constraint e criar com ambos os valores
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
                CHECK (role::text = ANY (ARRAY[
                    'admin'::character varying,
                    'instructor'::character varying,
                    'student'::character varying,
                    'aluno'::character varying,
                    'instrutor'::character varying
                ]::text[]))");
        }
        
        // Reverter roles de inglês para português
        DB::table('users')
            ->where('role', 'student')
            ->update(['role' => 'aluno']);
            
        DB::table('users')
            ->where('role', 'instructor')
            ->update(['role' => 'instrutor']);
            
        if ($driver === 'pgsql') {
            // Recriar constraint com valores em português
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
                CHECK (role::text = ANY (ARRAY[
                    'admin'::character varying,
                    'aluno'::character varying,
                    'instrutor'::character varying
                ]::text[]))");
        }
    }
};
