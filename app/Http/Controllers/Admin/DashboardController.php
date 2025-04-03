<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);

        // Get complaints statistics
        $statistics = $this->getStatistics($startDate);

        // Get chart data
        $chartData = $this->getChartData($startDate, $period);

        // Get complaint types distribution for donut chart
        $typeDistribution = $this->getComplaintTypesDistribution($startDate);

        // Get recent complaints
        $recentComplaints = Complaint::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', array_merge(
            $statistics,
            $chartData,
            [
                'recentComplaints' => $recentComplaints,
                'typeDistribution' => $typeDistribution
            ]
        ));
    }

    private function getStartDate($period)
    {
        return match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };
    }

    private function getStatistics($startDate)
    {
        $total = Complaint::where('created_at', '>=', $startDate)->count();
        $solved = Complaint::where('created_at', '>=', $startDate)
            ->where('status', 'résolu')
            ->count();
        $waiting = Complaint::where('created_at', '>=', $startDate)
            ->where('status', 'en_attente')
            ->count();
        $unsolved = Complaint::where('created_at', '>=', $startDate)
            ->where('status', 'non_résolu')
            ->count();

        return [
            'totalCount' => $total,
            'solvedCount' => $solved,
            'waitingCount' => $waiting,
            'unsolvedCount' => $unsolved,
            'solvedPercentage' => $total > 0 ? round(($solved / $total) * 100) : 0,
            'waitingPercentage' => $total > 0 ? round(($waiting / $total) * 100) : 0,
            'unsolvedPercentage' => $total > 0 ? round(($unsolved / $total) * 100) : 0,
        ];
    }

    private function getChartData($startDate, $period)
    {
        $format = match($period) {
            'week' => '%Y-%m-%d',
            'month' => '%Y-%m-%d',
            'year' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $complaints = Complaint::select([
            DB::raw("DATE_FORMAT(created_at, '$format') as date"),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'résolu' THEN 1 ELSE 0 END) as solved"),
            DB::raw("SUM(CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END) as waiting"),
            DB::raw("SUM(CASE WHEN status = 'non_résolu' THEN 1 ELSE 0 END) as unsolved"),
        ])
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'chartLabels' => $complaints->pluck('date')->toArray(),
            'chartData' => [
                [
                    'label' => 'Résolues',
                    'data' => $complaints->pluck('solved')->toArray(),
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'borderColor' => 'rgb(40, 167, 69)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'En attente',
                    'data' => $complaints->pluck('waiting')->toArray(),
                    'backgroundColor' => 'rgba(255, 193, 7, 0.2)',
                    'borderColor' => 'rgb(255, 193, 7)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Non résolues',
                    'data' => $complaints->pluck('unsolved')->toArray(),
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'borderColor' => 'rgb(220, 53, 69)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    private function getComplaintTypesDistribution($startDate)
    {
        $types = Complaint::select('complaint_type', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('complaint_type')
            ->get();

        $labels = [
            'retard_livraison' => 'Retard de livraison',
            'retard_chargement' => 'Retard de chargement',
            'marchandise_endommagée' => 'Marchandise endommagée',
            'mauvais_comportement' => 'Mauvais comportement',
            'autre' => 'Autre'
        ];

        $colors = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)'
        ];

        return [
            'labels' => $types->map(fn($type) => $labels[$type->complaint_type])->toArray(),
            'data' => $types->pluck('total')->toArray(),
            'backgroundColor' => array_slice($colors, 0, $types->count())
        ];
    }
}
