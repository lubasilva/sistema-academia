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
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverte os valores primeiro
        DB::table('users')->where('role', 'student')->update(['role' => 'aluno']);
        DB::table('users')->where('role', 'instructor')->update(['role' => 'instrutor']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('aluno')->change();
        });
    }
};
