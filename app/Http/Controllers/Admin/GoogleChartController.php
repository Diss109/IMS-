<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ServiceProvider;
use App\Models\Evaluation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoogleChartController extends Controller
{
    /**
     * Generate a simple line chart for complaints trend
     */
    public function trendChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get data for the last 6 months
        $data = [];
        $labels = [];

        // Adjusted date range based on filters
        $queryEndDate = now();
        $queryStartDate = now()->subMonths(5)->startOfMonth();

        // Apply filters if they exist
        if ($startDate && $endDate) {
            $queryStartDate = Carbon::parse($startDate);
            $queryEndDate = Carbon::parse($endDate)->endOfDay();
        } elseif ($period) {
            switch ($period) {
                case 'week':
                    $queryStartDate = Carbon::now()->subWeek();
                    break;
                case 'month':
                    $queryStartDate = Carbon::now()->subMonth();
                    break;
            }
        }

        // Generate monthly data points
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Apply date filters to the query
            $query = Complaint::query();

            if ($startDate && $endDate) {
                // Only include data points that fall within the filter range
                if ($startOfMonth->gt($queryEndDate) || $endOfMonth->lt($queryStartDate)) {
                    $data[] = 0; // Out of filter range
                    continue;
                }

                // Adjust start/end of month if filter is more restrictive
                $actualStart = $startOfMonth->lt($queryStartDate) ? $queryStartDate : $startOfMonth;
                $actualEnd = $endOfMonth->gt($queryEndDate) ? $queryEndDate : $endOfMonth;

                $count = Complaint::whereBetween('created_at', [$actualStart, $actualEnd])->count();
            } elseif ($period) {
                if ($startOfMonth->lt($queryStartDate)) {
                    $data[] = 0; // Before filter period
                    continue;
                }
                $count = Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->where('created_at', '>=', $queryStartDate)
                    ->count();
            } else {
                $count = Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            }

            // Ensure count is always non-negative
            $data[] = max(0, $count);
        }

        return view('admin.kpis.charts.trend', compact('data', 'labels'));
    }

    /**
     * Generate a pie chart for complaint types
     */
    public function typeChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build base query with filters
        $query = Complaint::query();

        // Apply date filters
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } elseif ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        // Get complaint types data with filters
        $types = [
            'Retard livraison' => (clone $query)->where('complaint_type', 'retard_livraison')->count(),
            'Retard chargement' => (clone $query)->where('complaint_type', 'retard_chargement')->count(),
            'Marchandise endommagée' => (clone $query)->where('complaint_type', 'marchandise_endommagée')->count(),
            'Mauvais comportement' => (clone $query)->where('complaint_type', 'mauvais_comportement')->count(),
            'Autre' => (clone $query)->where('complaint_type', 'autre')->count()
        ];

        return view('admin.kpis.charts.type', compact('types'));
    }

    /**
     * Generate a bar chart for complaint status
     */
    public function statusChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build base query with filters
        $query = Complaint::query();

        // Apply date filters
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } elseif ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        // Get status distribution with filters
        $statuses = [
            'En attente' => (clone $query)->where('status', 'en_attente')->count(),
            'Résolu' => (clone $query)->where('status', 'résolu')->count(),
            'Non résolu' => (clone $query)->where('status', 'non_résolu')->count()
        ];

        return view('admin.kpis.charts.status', compact('statuses'));
    }

    /**
     * Generate a doughnut chart for urgency levels
     */
    public function urgencyChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build base query with filters
        $query = Complaint::query();

        // Apply date filters
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } elseif ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        // Get urgency distribution with filters
        $urgencies = [
            'Critique' => (clone $query)->where('urgency_level', 'critical')->count(),
            'Élevée' => (clone $query)->where('urgency_level', 'high')->count(),
            'Moyenne' => (clone $query)->where('urgency_level', 'medium')->count(),
            'Faible' => (clone $query)->where('urgency_level', 'low')->count()
        ];

        return view('admin.kpis.charts.urgency', compact('urgencies'));
    }

    /**
     * Generate a chart for service provider types distribution
     */
    public function providerTypesChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build base query with filters
        $query = ServiceProvider::query();

        // Apply date filters
        if ($startDate && $endDate) {
            $endDateWithTime = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
        } elseif ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        // Get service provider types distribution with filters
        $types = [
            'Armateur' => (clone $query)->where('service_type', ServiceProvider::TYPE_SHIPPING)->count(),
            'Compagnie Aérienne' => (clone $query)->where('service_type', ServiceProvider::TYPE_AIRLINE)->count(),
            'Transport International' => (clone $query)->where('service_type', ServiceProvider::TYPE_INT_TRANSPORT)->count(),
            'Transport Local' => (clone $query)->where('service_type', ServiceProvider::TYPE_LOCAL_TRANSPORT)->count(),
            'Agent' => (clone $query)->where('service_type', ServiceProvider::TYPE_AGENT)->count(),
            'Magasin' => (clone $query)->where('service_type', ServiceProvider::TYPE_STORE)->count(),
            'Autre' => (clone $query)->where('service_type', ServiceProvider::TYPE_OTHER)->count()
        ];

        return view('admin.kpis.charts.provider_types', compact('types'));
    }

    /**
     * Generate a chart for evaluations trend
     */
    public function evaluationsTrendChart(Request $request)
    {
        // Get filter parameters
        $period = $request->get('period');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get data for the last 6 months
        $data = [];
        $labels = [];

        // Generate monthly data points
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Apply date filters to the query
            $query = Evaluation::query();

            if ($startDate && $endDate) {
                $queryStartDate = Carbon::parse($startDate);
                $queryEndDate = Carbon::parse($endDate)->endOfDay();

                // Only include data points that fall within the filter range
                if ($startOfMonth->gt($queryEndDate) || $endOfMonth->lt($queryStartDate)) {
                    $data[] = 0; // Out of filter range
                    continue;
                }

                // Adjust start/end of month if filter is more restrictive
                $actualStart = $startOfMonth->lt($queryStartDate) ? $queryStartDate : $startOfMonth;
                $actualEnd = $endOfMonth->gt($queryEndDate) ? $queryEndDate : $endOfMonth;

                $count = Evaluation::whereBetween('created_at', [$actualStart, $actualEnd])->count();
            } elseif ($period) {
                $queryStartDate = null;
                switch ($period) {
                    case 'week':
                        $queryStartDate = Carbon::now()->subWeek();
                        break;
                    case 'month':
                        $queryStartDate = Carbon::now()->subMonth();
                        break;
                }

                if ($queryStartDate && $startOfMonth->lt($queryStartDate)) {
                    $data[] = 0; // Before filter period
                    continue;
                }

                $count = Evaluation::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                if ($queryStartDate) {
                    $count = $count->where('created_at', '>=', $queryStartDate);
                }
                $count = $count->count();
            } else {
                $count = Evaluation::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            }

            // Ensure count is always non-negative
            $data[] = max(0, $count);
        }

        return view('admin.kpis.charts.evaluations_trend', compact('data', 'labels'));
    }

    /**
     * Generate a chart for user role distribution
     */
    public function userRolesChart(Request $request)
    {
        // Get user role distribution
        $roles = [];
        $roleLabels = User::getRoles();

        foreach ($roleLabels as $role => $label) {
            $roles[$label] = User::where('role', $role)->count();
        }

        return view('admin.kpis.charts.user_roles', compact('roles'));
    }
}
