<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ScheduleBlock;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Obtém os horários disponíveis para uma data específica
     */
    public function getAvailableSlots($date)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = strtolower($date->englishDayOfWeek);
        
        // Busca configurações de horário de funcionamento
        $operatingHours = json_decode(Setting::get('operating_hours', '{}'), true);
        
        // Verifica se a academia está aberta neste dia
        if (!isset($operatingHours[$dayOfWeek]) || !$operatingHours[$dayOfWeek]['enabled']) {
            return [];
        }
        
        $dayConfig = $operatingHours[$dayOfWeek];
        $slotDuration = (int) Setting::get('slot_duration', 60);
        $defaultCapacity = (int) Setting::get('max_capacity_per_class', 20);
        
        // Gera todos os slots do dia
        $slots = [];
        $currentTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayConfig['start']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $dayConfig['end']);
        
        while ($currentTime->lt($endTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $currentTime->copy()->addMinutes($slotDuration);
            
            // Verifica se este horário está bloqueado
            $isBlocked = ScheduleBlock::isBlocked(
                $date->format('Y-m-d'),
                $slotStart->format('H:i:s')
            );
            
            if (!$isBlocked) {
                $slots[] = [
                    'starts_at' => $slotStart,
                    'ends_at' => $slotEnd,
                    'capacity' => $defaultCapacity,
                    'status' => 'open',
                ];
            }
            
            $currentTime->addMinutes($slotDuration);
        }
        
        return $slots;
    }

    /**
     * Verifica se um horário está disponível
     */
    public function isSlotAvailable($datetime): bool
    {
        $date = Carbon::parse($datetime);
        $dayOfWeek = strtolower($date->englishDayOfWeek);
        
        // Busca configurações
        $operatingHours = json_decode(Setting::get('operating_hours', '{}'), true);
        
        // Verifica se está dentro do horário de funcionamento
        if (!isset($operatingHours[$dayOfWeek]) || !$operatingHours[$dayOfWeek]['enabled']) {
            return false;
        }
        
        $dayConfig = $operatingHours[$dayOfWeek];
        $time = $date->format('H:i');
        
        if ($time < $dayConfig['start'] || $time >= $dayConfig['end']) {
            return false;
        }
        
        // Verifica se está bloqueado
        return !ScheduleBlock::isBlocked(
            $date->format('Y-m-d'),
            $date->format('H:i:s')
        );
    }

    /**
     * Bloqueia um horário específico
     */
    public function blockSlot($date, $startTime, $endTime, $reason = 'other', $notes = null, $createdBy = null)
    {
        return ScheduleBlock::create([
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $reason,
            'notes' => $notes,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Remove um bloqueio
     */
    public function unblockSlot($blockId)
    {
        $block = ScheduleBlock::findOrFail($blockId);
        return $block->delete();
    }

    /**
     * Obtém os bloqueios de uma data
     */
    public function getBlocksForDate($date)
    {
        return ScheduleBlock::getBlocksForDate($date);
    }
}
