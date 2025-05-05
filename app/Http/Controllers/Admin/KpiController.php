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
    public function index(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Initialize the base query
        $query = Complaint::query();
        
        // Apply date filters
        if ($startDate && $endDate) {
            // Convert end date to include the entire day
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } elseif ($period) {
            // Apply predefined period filters
            $periodStartDate = null;
            
            switch ($period) {
                case 'week':
                    $periodStartDate = Carbon::now()->subWeek();
                    break;
                case 'month':
                    $periodStartDate = Carbon::now()->subMonth();
                    break;
                // Add more period options as needed
            }
            
            if ($periodStartDate) {
                $query->where('created_at', '>=', $periodStartDate);
            }
        }
        
        // Gather counts for filtered data to pass to the view
        $filteredCounts = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'en_attente')->count(),
            'critical' => (clone $query)->where('urgency_level', 'critical')->count(),
            'resolution_rate' => $this->calculateResolutionRate($query),
        ];
        
        // Return the iframe-based Google Charts dashboard with filtered data
        return view('admin.kpis.iframe_dashboard', [
            'filteredData' => $filteredCounts
        ]);
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
     * Calculate resolution rate based on a complaint query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return float
     */
    private function calculateResolutionRate($query)
    {
        $total = (clone $query)->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $resolved = (clone $query)->whereIn('status', ['resolu', 'cloture'])->count();
        return round(($resolved / $total) * 100);
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
