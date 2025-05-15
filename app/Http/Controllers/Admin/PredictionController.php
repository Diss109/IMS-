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
    public function index()
    {
        // Get providers with their latest predictions
        $providers = ServiceProvider::has('evaluations', '>=', 1)
            ->withCount('evaluations')
            ->with(['predictions' => function($query) {
                $query->latest('prediction_date')->limit(1);
            }])
            ->paginate(10);

        // Enhance with trend information
        foreach ($providers as $provider) {
            $provider->trend = $this->predictionService->getTrendInfo($provider);
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
                $query->orderBy('prediction_date', 'desc')->limit(5);
            }])
            ->findOrFail($id);

        // Format data for charts
        $evaluationDates = [];
        $evaluationScores = [];

        foreach ($provider->evaluations as $evaluation) {
            $evaluationDates[] = $evaluation->created_at->format('M d');
            $evaluationScores[] = $evaluation->total_score;
        }

        // Add predictions to the chart data
        $predictionDates = [];
        $predictionScores = [];
        $predictionConfidence = [];

        foreach ($provider->predictions as $prediction) {
            $predictionDates[] = $prediction->prediction_date->format('M d');
            $predictionScores[] = $prediction->predicted_score;
            $predictionConfidence[] = $prediction->confidence_level * 100;
        }

        // Get trend info
        $trendInfo = $this->predictionService->getTrendInfo($provider);

        // Get detailed data for the latest prediction
        $latestPrediction = $provider->predictions->first();

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
            'trendInfo',
            'regressionData'
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
     * Generate regression line data points for chart
     */
    private function calculateRegressionLine($evaluations)
    {
        if ($evaluations->count() < 2) {
            return null;
        }

        // Get first and last dates
        $firstDate = $evaluations->first()->created_at;
        $lastDate = $evaluations->last()->created_at;

        // Calculate x and y values for linear regression
        $x = []; // days since first evaluation
        $y = []; // scores
        $dates = []; // actual dates for chart

        foreach ($evaluations as $evaluation) {
            $days = $firstDate->diffInDays($evaluation->created_at);
            $x[] = $days;
            $y[] = $evaluation->total_score;
            $dates[] = $evaluation->created_at->format('M d');
        }

        // Calculate linear regression parameters
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($x[$i] * $y[$i]);
            $sumXX += ($x[$i] * $x[$i]);
        }

        // Calculate slope and y-intercept
        $denominator = ($n * $sumXX - $sumX * $sumX);
        if ($denominator == 0) {
            // If denominator is zero, use horizontal line at average
            $slope = 0;
            $yIntercept = $sumY / $n;
        } else {
            $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
            $yIntercept = ($sumY - $slope * $sumX) / $n;
        }

        // Generate regression line points for chart
        // Create points along the line
        $regLineX = []; // dates for chart
        $regLineY = []; // calculated y values

        // Add days for future projection (at least 90 days)
        $totalDays = max(90, $firstDate->diffInDays($lastDate) + 90);
        $interval = max(1, floor($totalDays / 10)); // ensure interval is at least 1

        // Always include the first date (with the actual first evaluation date)
        $regLineX[] = $firstDate->format('M d');
        $regLineY[] = max(0, min(100, $yIntercept)); // constraints to 0-100

        // Add evenly spaced points for the line
        for ($i = 1; $i <= 10; $i++) {
            $days = $i * $interval;
            $regLineX[] = $firstDate->copy()->addDays($days)->format('M d');
            $score = max(0, min(100, $slope * $days + $yIntercept)); // constrain to 0-100
            $regLineY[] = round($score, 1);
        }

        return [
            'dates' => $regLineX,
            'scores' => $regLineY,
            'slope' => $slope,
            'intercept' => $yIntercept
        ];
    }
}
