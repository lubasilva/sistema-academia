<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $client;
    protected $botToken;
    protected $baseUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}";
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10,
        ]);
    }

    /**
     * Enviar mensagem para um chat
     */
    public function sendMessage($chatId, $message, $parseMode = 'HTML')
    {
        try {
            $response = $this->client->post('/sendMessage', [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => $parseMode,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem Telegram', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar nova reserva
     */
    public function notifyNewBooking($booking)
    {
        if (!$booking->user->telegram_chat_id) {
            return false;
        }

        $message = "🎯 <b>Nova Reserva Confirmada!</b>\n\n";
        $message .= "📅 Data: " . $booking->date->format('d/m/Y') . "\n";
        $message .= "🕐 Horário: " . $booking->schedule->time->format('H:i') . "\n";
        $message .= "✅ Status: Confirmado\n\n";
        $message .= "Até lá! 💪";

        return $this->sendMessage($booking->user->telegram_chat_id, $message);
    }

    /**
     * Notificar cancelamento de reserva
     */
    public function notifyCancelledBooking($booking)
    {
        if (!$booking->user->telegram_chat_id) {
            return false;
        }

        $message = "❌ <b>Reserva Cancelada</b>\n\n";
        $message .= "📅 Data: " . $booking->date->format('d/m/Y') . "\n";
        $message .= "🕐 Horário: " . $booking->schedule->time->format('H:i') . "\n";
        $message .= "💳 Seu crédito foi devolvido.\n";

        return $this->sendMessage($booking->user->telegram_chat_id, $message);
    }

    /**
     * Notificar lembrete de aula
     */
    public function notifyClassReminder($booking, $hoursAhead = 2)
    {
        if (!$booking->user->telegram_chat_id) {
            return false;
        }

        $message = "⏰ <b>Lembrete de Aula</b>\n\n";
        $message .= "Sua aula começa em {$hoursAhead}h!\n\n";
        $message .= "📅 Data: " . $booking->date->format('d/m/Y') . "\n";
        $message .= "🕐 Horário: " . $booking->schedule->time->format('H:i') . "\n\n";
        $message .= "Nos vemos lá! 🏋️";

        return $this->sendMessage($booking->user->telegram_chat_id, $message);
    }

    /**
     * Notificar pagamento confirmado
     */
    public function notifyPaymentConfirmed($payment)
    {
        if (!$payment->user->telegram_chat_id) {
            return false;
        }

        $message = "✅ <b>Pagamento Confirmado!</b>\n\n";
        $message .= "💳 Plano: {$payment->plan->name}\n";
        $message .= "💰 Valor: R$ " . number_format($payment->amount, 2, ',', '.') . "\n";
        $message .= "📅 Válido por {$payment->plan->duration_days} dias\n";
        $message .= "🎫 Créditos: {$payment->plan->credits}\n\n";
        $message .= "Aproveite seus treinos! 💪";

        return $this->sendMessage($payment->user->telegram_chat_id, $message);
    }

    /**
     * Notificar pagamento vencido
     */
    public function notifyPaymentOverdue($payment)
    {
        if (!$payment->user->telegram_chat_id) {
            return false;
        }

        $message = "⚠️ <b>Pagamento Vencido</b>\n\n";
        $message .= "Seu pagamento do plano {$payment->plan->name} está vencido.\n\n";
        $message .= "💰 Valor: R$ " . number_format($payment->amount, 2, ',', '.') . "\n";
        $message .= "📅 Vencimento: " . $payment->due_date->format('d/m/Y') . "\n\n";
        $message .= "Regularize seu pagamento para continuar aproveitando nossos serviços.";

        return $this->sendMessage($payment->user->telegram_chat_id, $message);
    }

    /**
     * Notificar plano expirando
     */
    public function notifyPlanExpiring($userPlan, $daysLeft)
    {
        if (!$userPlan->user->telegram_chat_id) {
            return false;
        }

        $message = "⏳ <b>Seu Plano Está Expirando</b>\n\n";
        $message .= "Seu plano {$userPlan->plan->name} expira em {$daysLeft} dias.\n\n";
        $message .= "💳 Créditos restantes: {$userPlan->credits_remaining}\n";
        $message .= "📅 Expira em: " . $userPlan->ends_at->format('d/m/Y') . "\n\n";
        $message .= "Renove agora para continuar treinando! 🎯";

        return $this->sendMessage($userPlan->user->telegram_chat_id, $message);
    }

    /**
     * Enviar mensagem broadcast para todos os usuários com Telegram
     */
    public function broadcast($message, $role = null)
    {
        $query = \App\Models\User::whereNotNull('telegram_chat_id');
        
        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->get();
        $sentCount = 0;

        foreach ($users as $user) {
            if ($this->sendMessage($user->telegram_chat_id, $message)) {
                $sentCount++;
            }
            usleep(50000); // 50ms delay para evitar rate limiting
        }

        return $sentCount;
    }
}
