<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendClassReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:classes {--hours=2 : Hours ahead to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send class reminders via Telegram';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursAhead = $this->option('hours');
        $targetTime = now()->addHours($hoursAhead);

        $bookings = Booking::with(['user', 'schedule'])
            ->whereIn('status', ['booked', 'attended'])
            ->whereHas('schedule', function ($query) use ($targetTime) {
                $query->whereBetween('starts_at', [
                    $targetTime->copy()->subMinutes(30),
                    $targetTime->copy()->addMinutes(30)
                ]);
            })
            ->get();

        $this->info("Encontradas {$bookings->count()} reservas para lembrete.");

        $sentCount = 0;
        foreach ($bookings as $booking) {
            if ($this->telegramService->notifyClassReminder($booking, $hoursAhead)) {
                $sentCount++;
                $this->info("Lembrete enviado para {$booking->user->name}");
            }
        }

        $this->info("Total de lembretes enviados: {$sentCount}");
        return Command::SUCCESS;
    }
}

