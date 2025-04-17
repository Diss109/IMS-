<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $serviceProviders = \App\Models\ServiceProvider::all();
        return view('admin.evaluations.index', compact('serviceProviders'));
    }

    public function create()
    {
        return view('admin.evaluations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transporter_id' => 'required|exists:transporters,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'score' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'evaluation_date' => 'required|date',
        ]);

        Evaluation::create($validated);

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Évaluation créée avec succès.');
    }

    public function edit(Evaluation $evaluation)
    {
        return view('admin.evaluations.edit', compact('evaluation'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'transporter_id' => 'required|exists:transporters,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'score' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'evaluation_date' => 'required|date',
        ]);

        $evaluation->update($validated);

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Évaluation mise à jour avec succès.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Évaluation supprimée avec succès.');
    }
}
