<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ServiceProvider::query();

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $serviceProviders = $query->paginate(10);
        return view('admin.evaluations.index', compact('serviceProviders'));
    }

    public function create($serviceProviderId)
    {
        $serviceProvider = \App\Models\ServiceProvider::findOrFail($serviceProviderId);
        $evaluationHistory = Evaluation::with('user')
            ->where('service_provider_id', $serviceProviderId)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        // Centralized evaluation grids
        $evaluationGrids = [
            'armateur' => [
                'qualite' => ['main' => 0.3, 'sub' => ['consistency' => 2, 'communication' => 3, 'controle' => 3]],
                'cout'    => ['main' => 0.2, 'sub' => ['cash' => 2, 'cost' => 2]],
                'delai'   => ['main' => 0.2, 'sub' => ['commitment' => 4]],
                'qse'     => ['main' => 0.15, 'sub' => ['clean' => 4]],
                'identification' => ['main' => 0.15, 'sub' => ['competence' => 3, 'capacite' => 2, 'culture' => 1]],
            ],
            // Ajoute ici d'autres grilles pour d'autres types plus tard
        ];
        $type = $serviceProvider->service_type ?? 'armateur';
        $weights = $evaluationGrids[$type] ?? $evaluationGrids['armateur'];
        return view('admin.evaluations.create', compact('serviceProvider', 'evaluationHistory', 'weights'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'scores' => 'required|array',
        ]);

        $evaluation = Evaluation::create([
            'service_provider_id' => $request->service_provider_id,
            'user_id' => auth()->id(),
            'global_comment' => $request->global_comment,
        ]);

        // Pondérations (armateurs)
        $weights = [
            'qualite' => ['main' => 0.3, 'sub' => ['consistency' => 2, 'communication' => 3, 'controle' => 3]],
            'cout'    => ['main' => 0.2, 'sub' => ['cash' => 2, 'cost' => 2]],
            'delai'   => ['main' => 0.2, 'sub' => ['commitment' => 4]],
            'qse'     => ['main' => 0.15, 'sub' => ['clean' => 4]],
            'identification' => ['main' => 0.15, 'sub' => ['competence' => 3, 'capacite' => 2, 'culture' => 1]],
        ];

        $total = 0;
        $maxTotal = 0;
        foreach ($weights as $main => $data) {
            $mainWeight = $data['main'];
            $subSum = array_sum($data['sub']);
            foreach ($data['sub'] as $sub => $subWeight) {
                $score = $request->scores[$main][$sub] ?? 0;
                \App\Models\EvaluationScore::create([
                    'evaluation_id' => $evaluation->id,
                    'main_criterion' => $main,
                    'sub_criterion' => $sub,
                    'main_weight' => $mainWeight,
                    'sub_weight' => $subWeight,
                    'score' => $score,
                ]);
                $total += $score * $mainWeight * ($subWeight / $subSum);
                $maxTotal += 10 * $mainWeight * ($subWeight / $subSum);
            }
        }
        $evaluation->total_score = round($total * 100 / $maxTotal, 2); // score sur 100
        $evaluation->save();

        return redirect()->route('admin.evaluations.create', $evaluation->service_provider_id)
            ->with('success', 'Évaluation enregistrée avec succès !');
    }

    public function show($id)
    {
        $evaluation = \App\Models\Evaluation::with(['scores', 'user', 'serviceProvider'])->findOrFail($id);
        return view('admin.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        return view('admin.evaluations.edit', compact('evaluation'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'evaluation_date' => 'required|date',
            'global_comment' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0|max:10',
        ]);

        $evaluation->update([
            'evaluation_date' => $validated['evaluation_date'],
            'global_comment' => $validated['global_comment'],
        ]);

        $total = 0;
        $maxTotal = 0;

        foreach ($evaluation->scores as $score) {
            $newScore = $validated['scores'][$score->id] ?? $score->score;
            $score->update(['score' => $newScore]);

            // Recalculate total score (same logic as in store)
            $mainWeight = $score->main_weight;
            $subWeight = $score->sub_weight;
            $subSum = $evaluation->scores->where('main_criterion', $score->main_criterion)->sum('sub_weight');
            $total += $newScore * $mainWeight * ($subWeight / $subSum);
            $maxTotal += 10 * $mainWeight * ($subWeight / $subSum);
        }

        $evaluation->total_score = $maxTotal ? round($total * 100 / $maxTotal, 2) : 0;
        $evaluation->save();

        return redirect()->route('admin.evaluations.show', $evaluation->id)
            ->with('success', 'Évaluation mise à jour avec succès.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Évaluation supprimée avec succès.');
    }
}
