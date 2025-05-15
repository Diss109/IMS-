<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Complaint;
use App\Models\ServiceProvider;
use App\Models\Evaluation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends BaseAdminController
{ // No constructor needed; all middleware logic is handled by BaseAdminController.
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
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Complaint::query();

        // Apply date filters (custom date range takes precedence over period)
        if ($startDate && $endDate) {
            // Convert end date to include the entire day
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
            // Clear period when custom date range is used
            $period = 'custom';
        } else {
            // Apply default period filter if no custom date range
            $periodStartDate = $this->getStartDate($period);
            $query->where('created_at', '>=', $periodStartDate);
        }

        // Apply other filters
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

        // Get service providers, evaluations, and users statistics
        $providerStats = $this->getProviderStatistics($period, $startDate, $endDate);
        $evaluationStats = $this->getEvaluationStatistics($period, $startDate, $endDate);
        $userStats = $this->getUserStatistics();

        // Get recent evaluations
        $recentEvaluations = Evaluation::with(['serviceProvider', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Get top-rated service providers
        $topProviders = $this->getTopRatedProviders();

        return view('admin.dashboard', compact(
            'statistics',
            'chartData',
            'typeDistribution',
            'recentComplaints',
            'providerStats',
            'evaluationStats',
            'userStats',
            'recentEvaluations',
            'topProviders'
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
        // Determine grouping level based on period
        // For custom date ranges, determine the grouping based on range length
        $groupBy = match ($period) {
            'week' => 'date',
            'month' => 'date',
            'year' => 'month',
            'total' => 'month',
            'custom' => $this->determineGroupByForCustomRange($query),
            default => 'date',
        };

        // No need to apply date filtering again as it's already applied to $query in index method

        if ($groupBy === 'month') {
            $complaints = $query->clone()
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
            $complaints = $query->clone()
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

    /**
     * Determine appropriate grouping level for custom date ranges
     * based on the date range size
     */
    private function determineGroupByForCustomRange($query)
    {
        // Get the min and max dates from the query to calculate range
        $dateRange = $query->clone()->selectRaw('MIN(created_at) as min_date, MAX(created_at) as max_date')->first();

        if (!$dateRange->min_date || !$dateRange->max_date) {
            return 'date'; // Default to daily if no data
        }

        $minDate = Carbon::parse($dateRange->min_date);
        $maxDate = Carbon::parse($dateRange->max_date);
        $diffInDays = $maxDate->diffInDays($minDate);

        // Use appropriate grouping based on range size
        if ($diffInDays <= 31) {
            return 'date'; // Daily for ranges up to a month
        } else if ($diffInDays <= 365) {
            return 'month'; // Monthly for ranges up to a year
        } else {
            return 'month'; // Monthly for everything over a year
        }
    }

    private function getComplaintTypesDistribution($query)
    {
        $types = $query->clone()->selectRaw('complaint_type, COUNT(*) as count')
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

    // New methods to get additional statistics

    private function getProviderStatistics($period, $startDate, $endDate)
    {
        $query = ServiceProvider::query();

        // Apply date filters if needed
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } else {
            // Apply period filter
            $periodStartDate = $this->getStartDate($period);
            $query->where('created_at', '>=', $periodStartDate);
        }

        $total = $query->count();

        // Count by service type
        $typeDistribution = ServiceProvider::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->get()
            ->pluck('count', 'service_type')
            ->toArray();

        // Get providers with evaluations
        $withEvaluations = ServiceProvider::has('evaluations')->count();

        // Get new providers in the last month
        $newLastMonth = ServiceProvider::where('created_at', '>=', now()->subMonth())->count();

        return [
            'total' => $total,
            'type_distribution' => $typeDistribution,
            'with_evaluations' => $withEvaluations,
            'new_last_month' => $newLastMonth
        ];
    }

    private function getEvaluationStatistics($period, $startDate, $endDate)
    {
        $query = Evaluation::query();

        // Apply date filters if needed
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } else {
            // Apply period filter
            $periodStartDate = $this->getStartDate($period);
            $query->where('created_at', '>=', $periodStartDate);
        }

        $total = $query->count();

        // Count evaluations by month
        $lastSixMonths = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $count = Evaluation::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $lastSixMonths->put($month->format('M Y'), $count);
        }

        // Average score calculation - assuming there's a total_score column or scores relation
        $avgScore = Evaluation::whereHas('scores')
            ->with('scores')
            ->get()
            ->avg(function($evaluation) {
                return $evaluation->scores->avg('score');
            });

        return [
            'total' => $total,
            'monthly_trend' => $lastSixMonths,
            'avg_score' => round($avgScore, 1) ?? 0
        ];
    }

    private function getUserStatistics()
    {
        $totalUsers = User::count();
        $adminUsers = User::where('role', User::ROLE_ADMIN)->count();
        $activeLastMonth = User::where('updated_at', '>=', now()->subMonth())->count();

        // Role distribution
        $roleDistribution = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();

        return [
            'total' => $totalUsers,
            'admins' => $adminUsers,
            'active_last_month' => $activeLastMonth,
            'role_distribution' => $roleDistribution
        ];
    }

    private function getTopRatedProviders($limit = 5)
    {
        return ServiceProvider::has('evaluations')
            ->withCount('evaluations')
            ->with(['evaluations' => function($query) {
                $query->with('scores');
            }])
            ->get()
            ->map(function($provider) {
                // Calculate average score across all evaluations
                $avgScore = $provider->evaluations->flatMap(function($eval) {
                    return $eval->scores;
                })->avg('score') ?? 0;

                $provider->average_score = round($avgScore, 1);
                return $provider;
            })
            ->sortByDesc('average_score')
            ->take($limit)
            ->values();
    }
}
