<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\ProviderPrediction;
use App\Models\Evaluation;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PredictionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Display the prediction dashboard
     */
    public function index(Request $request)
    {
        // Get providers with their latest predictions
        $query = ServiceProvider::has('evaluations', '>=', 1)
            ->withCount('evaluations')
            ->with(['predictions' => function($query) {
                $query->latest('prediction_date')->limit(1);
            }]);

        // Apply search filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply predictions join if needed for filtering
        if ($request->has('score') && !empty($request->score)) {
            $query->whereHas('predictions', function($q) use ($request) {
                $q->latest('prediction_date')->limit(1);

                switch ($request->score) {
                    case 'high':
                        $q->where('predicted_score', '>=', 75);
                        break;
                    case 'medium':
                        $q->where('predicted_score', '>=', 50)
                          ->where('predicted_score', '<', 75);
                        break;
                    case 'low':
                        $q->where('predicted_score', '<', 50);
                        break;
                }
            });
        }

        // Get paginated results
        $providers = $query->paginate(10)->withQueryString();

        // Enhance with trend information
        foreach ($providers as $provider) {
            $provider->trend = $this->predictionService->getTrendInfo($provider);
        }

        // Filter by trend if requested
        if ($request->has('trend') && !empty($request->trend)) {
            $providers = $providers->filter(function($provider) use ($request) {
                return isset($provider->trend['trend']) && $provider->trend['trend'] === $request->trend;
            });
        }

        // Get some stats for the dashboard
        $stats = [
            'provider_count' => ServiceProvider::count(),
            'predicted_count' => ServiceProvider::has('predictions')->count(),
            'high_risk_count' => ProviderPrediction::where('predicted_score', '<', 60)
                ->where('confidence_level', '>', 0.7)
                ->distinct('service_provider_id')
                ->count('service_provider_id'),
            'improving_count' => ProviderPrediction::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(factors, '$.trend')) = 'improving'")
                ->distinct('service_provider_id')
                ->count('service_provider_id'),
        ];

        return view('admin.predictions.index', compact('providers', 'stats'));
    }

    /**
     * Show prediction details for a specific provider
     */
    public function show($id)
    {
        $provider = ServiceProvider::with(['evaluations' => function($query) {
                $query->orderBy('created_at', 'asc');
            }, 'predictions' => function($query) {
                $query->orderBy('prediction_date', 'desc');
            }])
            ->findOrFail($id);

        // Format data for charts
        $evaluationDates = [];
        $evaluationScores = [];

        foreach ($provider->evaluations as $evaluation) {
            $evaluationDates[] = $evaluation->created_at->format('M d');
            $evaluationScores[] = $evaluation->total_score;
        }

        // Group predictions by period
        $predictions = [
            'next_month' => null,
            'next_quarter' => null,
            'next_year' => null
        ];

        foreach ($provider->predictions as $prediction) {
            if (!isset($predictions[$prediction->prediction_period]) ||
                $predictions[$prediction->prediction_period] === null) {
                $predictions[$prediction->prediction_period] = $prediction;
            }
        }

        // Debug prediction data with more details
        foreach ($predictions as $period => $prediction) {
            if ($prediction) {
                // Dump full prediction object to log for debugging
                \Illuminate\Support\Facades\Log::debug("Prediction for $period: ", [
                    'id' => $prediction->id,
                    'provider_id' => $prediction->service_provider_id,
                    'period' => $prediction->prediction_period,
                    'score' => $prediction->predicted_score,
                    'confidence' => $prediction->confidence_level,
                    'date' => $prediction->prediction_date->format('Y-m-d'),
                    'has_factors' => isset($prediction->factors),
                    'factors_type' => isset($prediction->factors) ? gettype($prediction->factors) : 'null',
                    'factors' => $prediction->factors
                ]);
            } else {
                \Illuminate\Support\Facades\Log::debug("No prediction for $period");
            }
        }

        // Add predictions to the chart data
        $predictionDates = [];
        $predictionScores = [];
        $predictionConfidence = [];
        $predictionPeriods = [];

        foreach ($predictions as $period => $prediction) {
            if ($prediction) {
                $predictionDates[] = $prediction->prediction_date->format('M d');
                $predictionScores[] = $prediction->predicted_score;
                $predictionConfidence[] = $prediction->confidence_level * 100;
                $predictionPeriods[] = $period;
            }
        }

        // Get trend info
        $trendInfo = $this->predictionService->getTrendInfo($provider);

        // Get detailed data for the latest prediction (monthly)
        $latestPrediction = $predictions['next_month'];

        // Calculate regression line for chart
        $regressionData = $this->calculateRegressionLine($provider->evaluations);

        return view('admin.predictions.show', compact(
            'provider',
            'latestPrediction',
            'evaluationDates',
            'evaluationScores',
            'predictionDates',
            'predictionScores',
            'predictionConfidence',
            'predictionPeriods',
            'trendInfo',
            'regressionData',
            'predictions'
        ));
    }

    /**
     * Generate new predictions for all service providers
     */
    public function generateAll(Request $request)
    {
        $period = $request->input('period', 'next_month');
        $stats = $this->predictionService->generateAllPredictions($period);

        return redirect()->route('admin.predictions.index')
            ->with('success', "Prévisions générées: {$stats['success']} avec succès, {$stats['failed']} échouées, {$stats['no_data']} sans données suffisantes.");
    }

    /**
     * Regenerate predictions for a specific provider after evaluation
     */
    public function regenerateForProvider($id)
    {
        $provider = ServiceProvider::findOrFail($id);
        $result = $this->predictionService->regenerateAllPredictionsForProvider($provider);

        if ($result['success']) {
            return redirect()->route('admin.predictions.show', $id)
                ->with('success', 'Nouvelles prévisions générées avec succès pour les trois périodes (mois, trimestre, année).');
        } else {
            return redirect()->route('admin.predictions.show', $id)
                ->with('error', $result['message'] ?? 'Erreur lors de la génération des prévisions.');
        }
    }

    /**
     * Debug data for a specific provider
     */
    public function debugChartData($id)
    {
        $provider = ServiceProvider::with(['evaluations' => function($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->findOrFail($id);

        // Format data for chart debugging
        $evaluationDates = [];
        $evaluationScores = [];

        foreach ($provider->evaluations as $evaluation) {
            $evaluationDates[] = $evaluation->created_at->format('M d');
            $evaluationScores[] = $evaluation->total_score;
        }

        // Calculate regression line data
        $regressionData = $this->calculateRegressionLine($provider->evaluations);

        // Return JSON data for debugging
        return response()->json([
            'provider_name' => $provider->name,
            'evaluations_count' => $provider->evaluations->count(),
            'evaluation_dates' => $evaluationDates,
            'evaluation_scores' => $evaluationScores,
            'regression_data' => $regressionData,
        ]);
    }

    /**
     * Calculate regression line data points for chart
     */
    private function calculateRegressionLine($evaluations)
    {
        if ($evaluations->count() < 2) {
            return null;
        }

        // Get first and last dates
        $firstDate = $evaluations->first()->created_at;
        $lastDate = $evaluations->last()->created_at;

        // Prepare data for advanced forecasting
        $dates = [];
        $scores = [];
        $timestamps = [];

        foreach ($evaluations as $evaluation) {
            $dates[] = $evaluation->created_at;
            $scores[] = $evaluation->total_score;
            $timestamps[] = $evaluation->created_at->diffInDays($firstDate) / 30; // Convert to months
        }

        // Use service to get more sophisticated trend info
        $predictionService = app(PredictionService::class);
        list(, , $factors) = $predictionService->calculateForecast($evaluations);

        // Generate regression line points for chart
        // Create evenly spaced points along the regression line
        $regLineX = []; // dates for chart
        $regLineY = []; // calculated y values

        // Generate enough points for a smooth line (use 10 points)
        $totalDays = max(90, $firstDate->diffInDays($lastDate) + 30);

        // Always include the first point (at day 0)
        $regLineX[] = $firstDate->format('M d');
        $regLineY[] = max(0, min(100, $factors['intercept'])); // constrain to 0-100

        // Add evenly spaced points for the line, accounting for change points
        for ($i = 1; $i <= 10; $i++) {
            $days = ($i / 10) * $totalDays;
            $monthValue = $days / 30; // Convert to months for prediction

            // Calculate trend value at this point
            $score = $factors['slope'] * $monthValue + $factors['intercept'];

            // Apply change point effects if available
            if (isset($factors['change_points']) && !empty($factors['change_points'])) {
                foreach ($factors['change_points'] as $cp) {
                    if ($monthValue > $cp['timestamp']) {
                        $slopeDiff = $cp['right_slope'] - $cp['left_slope'];
                        $score += $slopeDiff * ($monthValue - $cp['timestamp']) * 0.5;
                    }
                }
            }

            $regLineX[] = $firstDate->copy()->addDays($days)->format('M d');
            $regLineY[] = max(0, min(100, round($score, 1))); // constrain to 0-100
        }

        return [
            'dates' => $regLineX,
            'scores' => $regLineY,
            'slope' => $factors['slope'],
            'intercept' => $factors['intercept'],
            'change_points' => $factors['change_points'] ?? []
        ];
    }
}
