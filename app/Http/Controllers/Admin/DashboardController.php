<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends BaseAdminController
{
    private $complaintTypes = [
        'retard_livraison',
        'retard_chargement',
        'marchandise_endommagée',
        'mauvais_comportement',
        'autre'
    ];

    private $urgencyLevels = [
        'critique',
        'élevé',
        'moyen',
        'faible'
    ];

    public function index(Request $request)
    {
        $period = $request->get('period', 'total');
        $type = $request->get('type');
        $urgency = $request->get('urgency');
        $status = $request->get('status');
        $date = $request->get('date');

        $query = Complaint::query();

        // Apply filters
        if ($type) {
            $query->where('complaint_type', $type);
        }
        if ($urgency) {
            $query->where('urgency_level', $urgency);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Get statistics based on filtered data
        $statistics = $this->getStatistics($period, $query->clone());
        $chartData = $this->getChartData($period, $query->clone());
        $typeDistribution = $this->getComplaintTypesDistribution($query->clone());

        // Get recent complaints with filters
        $recentComplaints = $query->latest()
            ->with('assignedUser')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'statistics',
            'chartData',
            'typeDistribution',
            'recentComplaints'
        ))->with([
            'complaintTypes' => $this->complaintTypes,
            'urgencyLevels' => $this->urgencyLevels
        ]);
    }

    private function getStartDate($period)
    {
        return match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            'total' => now()->subYears(10), // A large enough range to get all records
            default => now()->subWeek(),
        };
    }

    private function getStatistics($period, $query)
    {
        $startDate = $this->getStartDate($period);

        $total = $query->clone()->where('created_at', '>=', $startDate)->count();
        $resolved = $query->clone()->where('created_at', '>=', $startDate)
            ->where('status', 'résolu')->count();
        $waiting = $query->clone()->where('created_at', '>=', $startDate)
            ->where('status', 'en_attente')->count();
        $unresolved = $query->clone()->where('created_at', '>=', $startDate)
            ->where('status', 'non_résolu')->count();

        return [
            'total' => $total,
            'resolved' => $resolved,
            'waiting' => $waiting,
            'unresolved' => $unresolved,
            'resolved_percentage' => $total > 0 ? round(($resolved / $total) * 100) : 0,
            'waiting_percentage' => $total > 0 ? round(($waiting / $total) * 100) : 0,
            'unresolved_percentage' => $total > 0 ? round(($unresolved / $total) * 100) : 0,
        ];
    }

    private function getChartData($period, $query)
    {
        $startDate = $this->getStartDate($period);
        $groupBy = match ($period) {
            'week' => 'date',
            'month' => 'date',
            'year' => 'month',
            'total' => 'month',
            default => 'date',
        };

        if ($groupBy === 'month') {
            $complaints = $query->where('created_at', '>=', $startDate)
                ->selectRaw('YEAR(created_at) as year')
                ->selectRaw('MONTH(created_at) as month')
                ->selectRaw('status')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('year', 'month', 'status')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $dates = $complaints->map(function ($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            })->unique()->sort()->values();

            $labels = $dates->map(function ($date) {
                return Carbon::createFromFormat('Y-m', $date)->format('F Y');
            });
        } else {
            $complaints = $query->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('status')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get();

            $dates = $complaints->pluck('date')->unique()->sort()->values();
            $labels = $dates->map(function ($date) {
                return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
            });
        }

        $datasets = [
            [
                'label' => 'Résolues',
                'backgroundColor' => '#28a745',
                'data' => $this->getDatasetValues($dates, $complaints, 'résolu', $groupBy),
            ],
            [
                'label' => 'En attente',
                'backgroundColor' => '#ffc107',
                'data' => $this->getDatasetValues($dates, $complaints, 'en_attente', $groupBy),
            ],
            [
                'label' => 'Non résolues',
                'backgroundColor' => '#dc3545',
                'data' => $this->getDatasetValues($dates, $complaints, 'non_résolu', $groupBy),
            ],
        ];

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function getDatasetValues($dates, $complaints, $status, $groupBy)
    {
        if ($groupBy === 'month') {
            return $dates->map(function ($date) use ($complaints, $status) {
                list($year, $month) = explode('-', $date);
                return $complaints->where('year', $year)
                    ->where('month', $month)
                    ->where('status', $status)
                    ->sum('count');
            });
        }

        return $dates->map(function ($date) use ($complaints, $status) {
            return $complaints->where('date', $date)
                ->where('status', $status)
                ->sum('count');
        });
    }

    private function getComplaintTypesDistribution($query)
    {
        $types = $query->selectRaw('complaint_type, COUNT(*) as count')
            ->groupBy('complaint_type')
            ->get();

        $colors = [
            'retard_livraison' => '#4e73df',
            'retard_chargement' => '#1cc88a',
            'marchandise_endommagée' => '#36b9cc',
            'mauvais_comportement' => '#f6c23e',
            'autre' => '#858796',
        ];

        $labels = $types->map(function ($type) {
            return match ($type->complaint_type) {
                'retard_livraison' => 'Retard de livraison',
                'retard_chargement' => 'Retard de chargement',
                'marchandise_endommagée' => 'Marchandise endommagée',
                'mauvais_comportement' => 'Mauvais comportement',
                default => 'Autre',
            };
        });

        return [
            'labels' => $labels,
            'data' => $types->pluck('count'),
            'backgroundColor' => $types->map(fn($type) => $colors[$type->complaint_type] ?? '#858796'),
        ];
    }
}
