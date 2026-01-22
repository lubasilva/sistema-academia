<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_capacity_per_class' => 'required|integer|min:1|max:50',
            'booking_advance_days' => 'required|integer|min:1|max:90',
            'cancellation_hours' => 'required|integer|min:1|max:72',
            'app_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Atualizar horários de funcionamento
     */
    public function updateOperatingHours(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'enabled' => 'nullable|boolean',
            'start' => 'required_if:enabled,1|nullable|date_format:H:i',
            'end' => 'required_if:enabled,1|nullable|date_format:H:i|after:start',
        ]);

        // Buscar configuração existente
        $setting = Setting::where('key', 'operating_hours')->first();
        $operatingHours = $setting ? json_decode($setting->value, true) : [];

        // Atualizar o dia específico
        $operatingHours[$validated['day']] = [
            'enabled' => isset($validated['enabled']) && $validated['enabled'],
            'start' => $validated['start'] ?? '06:00',
            'end' => $validated['end'] ?? '22:00',
        ];

        // Salvar
        Setting::updateOrCreate(
            ['key' => 'operating_hours'],
            ['value' => json_encode($operatingHours)]
        );

        return back()->with('success', '✅ Horário de funcionamento atualizado com sucesso!');
    }
}
