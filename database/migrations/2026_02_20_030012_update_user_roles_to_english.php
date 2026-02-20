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
        // Atualizar roles de português para inglês
        DB::table('users')
            ->where('role', 'aluno')
            ->update(['role' => 'student']);
            
        DB::table('users')
            ->where('role', 'instrutor')
            ->update(['role' => 'instructor']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter roles de inglês para português
        DB::table('users')
            ->where('role', 'student')
            ->update(['role' => 'aluno']);
            
        DB::table('users')
            ->where('role', 'instructor')
            ->update(['role' => 'instrutor']);
    }
};
