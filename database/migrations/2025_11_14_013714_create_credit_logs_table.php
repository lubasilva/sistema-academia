<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Aluno
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // Admin/Instrutor que fez a ação
            
            // Tipo de ação
            $table->enum('action_type', [
                'credit_added',        // Crédito regular adicionado
                'extra_credit_added',  // Crédito extra adicionado (reposição)
                'credit_used',         // Crédito usado em agendamento
                'credit_returned',     // Crédito devolvido (cancelamento)
                'plan_created',        // Plano criado
                'plan_extended',       // Plano prorrogado
            ]);
            
            // Tipo de crédito afetado
            $table->enum('credit_type', ['regular', 'extra'])->default('regular');
            
            // Quantidade alterada (+/-)
            $table->integer('amount');
            
            // Saldo após a operação
            $table->integer('balance_after');
            
            // Observações/Justificativa
            $table->text('reason')->nullable();
            
            // Referência a booking (quando aplicável)
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'created_at']);
            $table->index(['user_plan_id', 'action_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_logs');
    }
};
