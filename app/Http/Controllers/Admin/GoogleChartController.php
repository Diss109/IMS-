<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoogleChartController extends Controller
{
    /**
     * Generate a simple line chart for complaints trend
     */
    public function trendChart()
    {
        // Get data for the last 6 months
        $data = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $count = Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $data[] = $count;
        }
        
        return view('admin.kpis.charts.trend', compact('data', 'labels'));
    }
    
    /**
     * Generate a pie chart for complaint types
     */
    public function typeChart()
    {
        // Get complaint types data
        $types = [
            'Retard livraison' => Complaint::where('complaint_type', 'retard_livraison')->count(),
            'Retard chargement' => Complaint::where('complaint_type', 'retard_chargement')->count(),
            'Marchandise endommagée' => Complaint::where('complaint_type', 'marchandise_endommagée')->count(),
            'Mauvais comportement' => Complaint::where('complaint_type', 'mauvais_comportement')->count(),
            'Autre' => Complaint::where('complaint_type', 'autre')->count()
        ];
        
        return view('admin.kpis.charts.type', compact('types'));
    }
    
    /**
     * Generate a bar chart for complaint status
     */
    public function statusChart()
    {
        // Get status distribution
        $statuses = [
            'En attente' => Complaint::where('status', 'en_attente')->count(),
            'Résolu' => Complaint::where('status', 'résolu')->count(),
            'Non résolu' => Complaint::where('status', 'non_résolu')->count()
        ];
        
        return view('admin.kpis.charts.status', compact('statuses'));
    }
    
    /**
     * Generate a doughnut chart for urgency levels
     */
    public function urgencyChart()
    {
        // Get urgency distribution
        $urgencies = [
            'Critique' => Complaint::where('urgency_level', 'critical')->count(),
            'Élevée' => Complaint::where('urgency_level', 'high')->count(),
            'Moyenne' => Complaint::where('urgency_level', 'medium')->count(),
            'Faible' => Complaint::where('urgency_level', 'low')->count()
        ];
        
        return view('admin.kpis.charts.urgency', compact('urgencies'));
    }
}
