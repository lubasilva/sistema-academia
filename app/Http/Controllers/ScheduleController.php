<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::orderBy('starts_at', 'desc')->paginate(20);
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'capacity' => 'nullable|integer|min:1|max:20',
            'status' => 'required|in:open,closed,holiday',
        ]);

        // Combinar data e hora
        $starts_at = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $ends_at = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);

        Schedule::create([
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'capacity_override' => $validated['capacity'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('schedules.index')->with('success', 'Horário criado com sucesso!');
    }

    public function edit(Schedule $schedule)
    {
        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'capacity' => 'nullable|integer|min:1|max:20',
            'status' => 'required|in:open,closed,holiday',
        ]);

        $starts_at = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $ends_at = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);

        $schedule->update([
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'capacity_override' => $validated['capacity'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('schedules.index')->with('success', 'Horário atualizado com sucesso!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Horário excluído com sucesso!');
    }
}
