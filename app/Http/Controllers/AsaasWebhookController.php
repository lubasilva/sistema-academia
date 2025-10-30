<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    /**
     * Processar webhook do Asaas
     */
    public function handle(Request $request)
    {
        // Validar token do webhook
        $webhookToken = config('services.asaas.webhook_token');
        if ($webhookToken && $request->header('asaas-access-token') !== $webhookToken) {
            Log::warning('Webhook Asaas com token inválido', [
                'ip' => $request->ip(),
                'token' => $request->header('asaas-access-token')
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $paymentData = $request->input('payment');

        Log::info('Webhook Asaas recebido', [
            'event' => $event,
            'payment_id' => $paymentData['id'] ?? null
        ]);

        try {
            switch ($event) {
                case 'PAYMENT_RECEIVED':
                case 'PAYMENT_CONFIRMED':
                    $this->handlePaymentConfirmed($paymentData);
                    break;

                case 'PAYMENT_OVERDUE':
                    $this->handlePaymentOverdue($paymentData);
                    break;

                case 'PAYMENT_DELETED':
                case 'PAYMENT_REFUNDED':
                    $this->handlePaymentCancelled($paymentData);
                    break;

                default:
                    Log::info('Evento Asaas não tratado', ['event' => $event]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook Asaas', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Processar pagamento confirmado
     */
    protected function handlePaymentConfirmed($paymentData)
    {
        $payment = Payment::where('asaas_payment_id', $paymentData['id'])->first();

        if (!$payment) {
            Log::warning('Pagamento não encontrado', ['asaas_id' => $paymentData['id']]);
            return;
        }

        if ($payment->status === 'paid') {
            Log::info('Pagamento já processado', ['payment_id' => $payment->id]);
            return;
        }

        // Atualizar status do pagamento
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Ativar ou renovar plano do usuário
        $plan = $payment->plan;
        $user = $payment->user;

        $activePlan = $user->activePlan;

        if ($activePlan && $activePlan->plan_id === $plan->id && $activePlan->ends_at > now()) {
            // Renovar plano existente
            $activePlan->update([
                'ends_at' => $activePlan->ends_at->addDays($plan->duration_days),
                'credits_remaining' => $activePlan->credits_remaining + $plan->credits,
            ]);
        } else {
            // Criar novo plano
            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'credits_remaining' => $plan->credits,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
            ]);
        }

        // Registrar log de auditoria
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'payment_confirmed',
            'description' => "Pagamento confirmado - Plano {$plan->name}",
        ]);

        Log::info('Pagamento processado com sucesso', [
            'payment_id' => $payment->id,
            'user_id' => $user->id
        ]);
    }

    /**
     * Processar pagamento vencido
     */
    protected function handlePaymentOverdue($paymentData)
    {
        $payment = Payment::where('asaas_payment_id', $paymentData['id'])->first();

        if ($payment) {
            $payment->update(['status' => 'overdue']);
            
            \App\Models\AuditLog::create([
                'user_id' => $payment->user_id,
                'action' => 'payment_overdue',
                'description' => "Pagamento vencido - {$payment->plan->name}",
            ]);
        }
    }

    /**
     * Processar pagamento cancelado/estornado
     */
    protected function handlePaymentCancelled($paymentData)
    {
        $payment = Payment::where('asaas_payment_id', $paymentData['id'])->first();

        if ($payment) {
            $payment->update(['status' => 'cancelled']);
            
            \App\Models\AuditLog::create([
                'user_id' => $payment->user_id,
                'action' => 'payment_cancelled',
                'description' => "Pagamento cancelado - {$payment->plan->name}",
            ]);
        }
    }
}
