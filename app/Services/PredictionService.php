<?php

namespace App\Services;

use App\Models\ServiceProvider;
use App\Models\Evaluation;
use App\Models\ProviderPrediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    const VERSION = '1.1.0';
    const MIN_EVALUATIONS = 5; // Minimum evaluations needed for prediction

    /**
     * Generate predictions for all service providers
     *
     * @param string $period The prediction period (next_month, next_quarter)
     * @return array Results statistics
     */
    public function generateAllPredictions($period = 'next_month')
    {
        $providers = ServiceProvider::has('evaluations', '>=', self::MIN_EVALUATIONS)->get();
        $stats = [
            'total' => $providers->count(),
            'success' => 0,
            'failed' => 0,
            'no_data' => 0
        ];

        foreach ($providers as $provider) {
            try {
                $result = $this->predictForProvider($provider, $period);
                if ($result) {
                    $stats['success']++;
                } else {
                    $stats['no_data']++;
                }
            } catch (\Exception $e) {
                Log::error("Prediction failed for provider ID {$provider->id}: " . $e->getMessage());
                $stats['failed']++;
            }
        }

        return $stats;
    }

    /**
     * Generate prediction for a specific service provider
     *
     * @param ServiceProvider $provider The service provider
     * @param string $period Prediction period
     * @return bool True if prediction was generated
     */
    public function predictForProvider(ServiceProvider $provider, $period = 'next_month')
    {
        // Retrieve historical evaluation data
        $evaluations = $provider->evaluations()
            ->orderBy('created_at')
            ->get();

        if ($evaluations->count() < self::MIN_EVALUATIONS) {
            return false;
        }

        // Calculate prediction using linear regression
        $monthsAhead = ($period === 'next_quarter') ? 3 : 1;
        list($predictedScore, $confidenceLevel, $factors) = $this->calculateLinearRegression($evaluations, $monthsAhead);

        // Save prediction
        ProviderPrediction::create([
            'service_provider_id' => $provider->id,
            'predicted_score' => $predictedScore,
            'confidence_level' => $confidenceLevel,
            'prediction_date' => Carbon::now(),
            'prediction_period' => $period,
            'factors' => $factors,
            'model_version' => self::VERSION
        ]);

        return true;
    }

    /**
     * Calculate linear regression based on historical evaluations
     *
     * @param \Illuminate\Database\Eloquent\Collection $evaluations
     * @param int $monthsAhead
     * @return array [predictedScore, confidenceLevel, factors]
     */
    private function calculateLinearRegression($evaluations, $monthsAhead = 1)
    {
        // Calculate x and y values
        $x = []; // months since first evaluation
        $y = []; // scores

        $firstDate = $evaluations->first()->created_at;

        foreach ($evaluations as $evaluation) {
            $monthsDiff = $firstDate->diffInDays($evaluation->created_at) / 30;
            $x[] = $monthsDiff;
            $y[] = $evaluation->total_score;
        }

        // Calculate linear regression parameters
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;
        $sumYY = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($x[$i] * $y[$i]);
            $sumXX += ($x[$i] * $x[$i]);
            $sumYY += ($y[$i] * $y[$i]);
        }

        // Calculate slope and y-intercept
        $denominator = ($n * $sumXX - $sumX * $sumX);
        if ($denominator == 0) {
            // If denominator is zero, use average as prediction
            $predictedScore = $sumY / $n;
            $slope = 0;
            $yIntercept = $predictedScore;
            $rSquared = 0; // No correlation
        } else {
            $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
            $yIntercept = ($sumY - $slope * $sumX) / $n;

            // Calculate future score
            $lastMonth = end($x);
            $futureMonth = $lastMonth + $monthsAhead;
            $predictedScore = $slope * $futureMonth + $yIntercept;

            // Calculate R-squared (coefficient of determination)
            // R-squared measures how well the regression line fits the data
            $ssr = 0; // Sum of squared residuals
            $sst = 0; // Total sum of squares

            $mean = $sumY / $n;

            for ($i = 0; $i < $n; $i++) {
                $predicted = $slope * $x[$i] + $yIntercept;
                $ssr += pow($y[$i] - $predicted, 2);
                $sst += pow($y[$i] - $mean, 2);
            }

            $rSquared = $sst > 0 ? 1 - ($ssr / $sst) : 0;
        }

        // Ensure score is within reasonable bounds (0-100)
        $predictedScore = max(0, min(100, $predictedScore));

        // Calculate confidence level based on R-squared and number of evaluations
        // More evaluations and higher R-squared mean higher confidence
        $confidenceLevel = min(0.95, ($rSquared * 0.7) + (min(1, $n / 10) * 0.3));

        // Determine trend
        $trend = $slope > 0 ? 'improving' : ($slope < 0 ? 'declining' : 'stable');
        $trendStrength = abs($slope);

        return [
            $predictedScore,
            $confidenceLevel,
            [
                'trend' => $trend,
                'trend_strength' => $trendStrength,
                'slope' => $slope,
                'intercept' => $yIntercept,
                'r_squared' => $rSquared,
                'evaluations_count' => $n,
                'last_score' => end($y),
                'avg_score' => $mean ?? ($sumY / $n)
            ]
        ];
    }

    /**
     * Get trend information for a provider
     *
     * @param ServiceProvider $provider
     * @return array Trend information
     */
    public function getTrendInfo(ServiceProvider $provider)
    {
        $evaluations = $provider->evaluations()
            ->orderBy('created_at')
            ->get();

        if ($evaluations->count() < self::MIN_EVALUATIONS) {
            return [
                'has_trend' => false,
                'message' => 'Pas assez de données pour établir une tendance'
            ];
        }

        list(, $confidence, $factors) = $this->calculateLinearRegression($evaluations);

        $trendInfo = [
            'has_trend' => true,
            'trend' => $factors['trend'],
            'confidence' => $confidence * 100,
            'last_score' => $factors['last_score'],
            'evaluations_count' => $factors['evaluations_count']
        ];

        // Add trend message
        if ($factors['trend'] === 'improving') {
            $trendInfo['status'] = 'success';
            $trendInfo['message'] = 'Tendance à l\'amélioration';
            $trendInfo['icon'] = 'trending_up';
        } elseif ($factors['trend'] === 'declining') {
            $trendInfo['status'] = $factors['slope'] < -0.5 ? 'danger' : 'warning';
            $trendInfo['message'] = 'Tendance à la baisse';
            $trendInfo['icon'] = 'trending_down';
        } else {
            $trendInfo['status'] = 'info';
            $trendInfo['message'] = 'Performance stable';
            $trendInfo['icon'] = 'trending_flat';
        }

        return $trendInfo;
    }
}
