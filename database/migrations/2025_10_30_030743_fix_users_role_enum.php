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
        // Primeiro, atualiza os valores existentes
        DB::table('users')->where('role', 'aluno')->update(['role' => 'student']);
        DB::table('users')->where('role', 'instrutor')->update(['role' => 'instructor']);
        
        // Depois altera o ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'instructor', 'student') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'instrutor', 'aluno') DEFAULT 'aluno'");
    }
};
