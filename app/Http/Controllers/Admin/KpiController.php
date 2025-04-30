<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiController extends Controller
{
    public function index()
    {
        // Return the iframe-based Google Charts dashboard
        return view('admin.kpis.iframe_dashboard');
    }

    public function create()
    {
        return view('admin.kpis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target' => 'required|numeric',
            'current_value' => 'required|numeric',
            'period' => 'required|string|max:255',
        ]);

        Kpi::create($validated);

        return redirect()->route('admin.kpis.index')
            ->with('success', 'KPI créé avec succès.');
    }

    public function edit(Kpi $kpi)
    {
        return view('admin.kpis.edit', compact('kpi'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target' => 'required|numeric',
            'current_value' => 'required|numeric',
            'period' => 'required|string|max:255',
        ]);

        $kpi->update($validated);

        return redirect()->route('admin.kpis.index')
            ->with('success', 'KPI mis à jour avec succès.');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();

        return redirect()->route('admin.kpis.index')
            ->with('success', 'KPI supprimé avec succès.');
    }
    
    /**
     * Display the enhanced KPI dashboard with dynamic charts
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Return the simple dashboard view with guaranteed chart rendering
        return view('admin.kpis.simple');
    }
}
