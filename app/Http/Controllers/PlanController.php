<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('price_cents')->get();
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency_per_week' => 'required|integer|min:1|max:5',
            'billing_cycle' => 'required|in:monthly,quarterly,semiannual,annual',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Converter preço para centavos
            $validated['price_cents'] = $validated['price'] * 100;
            unset($validated['price']);

            // Gerar slug
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');

            Plan::create($validated);

            return redirect()->route('plans.index')->with('success', 'Plano criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar plano: ' . $e->getMessage());
        }
    }

    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency_per_week' => 'required|integer|min:1|max:5',
            'billing_cycle' => 'required|in:monthly,quarterly,semiannual,annual',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Converter preço para centavos
        $validated['price_cents'] = $validated['price'] * 100;
        unset($validated['price']);

        // Gerar slug
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $plan->update($validated);

        return redirect()->route('plans.index')->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Plano excluído com sucesso!');
    }
}
