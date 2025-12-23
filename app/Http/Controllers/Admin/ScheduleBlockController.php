<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleBlock;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleBlockController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $blocks = ScheduleBlock::where('date', '>=', $date)
            ->orderBy('date')
            ->orderBy('start_time')
            ->with('creator')
            ->paginate(20);

        return view('admin.schedule-blocks.index', compact('blocks', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schedule-blocks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|in:maintenance,holiday,event,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->scheduleService->blockSlot(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['reason'],
            $validated['notes'] ?? null,
            Auth::id()
        );

        return redirect()
            ->route('admin.schedule-blocks.index')
            ->with('success', 'Horário bloqueado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduleBlock $scheduleBlock)
    {
        $scheduleBlock->load('creator');
        return view('admin.schedule-blocks.show', compact('scheduleBlock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScheduleBlock $scheduleBlock)
    {
        return view('admin.schedule-blocks.edit', compact('scheduleBlock'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduleBlock $scheduleBlock)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|in:maintenance,holiday,event,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $scheduleBlock->update($validated);

        return redirect()
            ->route('admin.schedule-blocks.index')
            ->with('success', 'Bloqueio atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduleBlock $scheduleBlock)
    {
        $scheduleBlock->delete();

        return redirect()
            ->route('admin.schedule-blocks.index')
            ->with('success', 'Bloqueio removido com sucesso!');
    }
}
