<?php

namespace App\Services;

use App\Models\ServiceProvider;
use App\Models\Evaluation;
use App\Models\ProviderPrediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    const VERSION = '2.0.0'; // Updated version to reflect advanced forecasting
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

        // Calculate prediction using advanced forecasting
        $monthsAhead = ($period === 'next_quarter') ? 3 : (($period === 'next_year') ? 12 : 1);
        list($predictedScore, $confidenceLevel, $factors) = $this->calculateForecast($evaluations, $monthsAhead);

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
     * Generate all three prediction timeframes and reset old ones
     *
     * @param ServiceProvider $provider The service provider
     * @return array Results with success status for each timeframe
     */
    public function regenerateAllPredictionsForProvider(ServiceProvider $provider)
    {
        // Retrieve historical evaluation data
        $evaluations = $provider->evaluations()
            ->orderBy('created_at')
            ->get();

        if ($evaluations->count() < self::MIN_EVALUATIONS) {
            return [
                'success' => false,
                'message' => 'Nombre d\'évaluations insuffisant'
            ];
        }

        // Define timeframes
        $timeframes = [
            'next_month' => 1,
            'next_quarter' => 3,
            'next_year' => 12
        ];

        $results = [];

        // Delete all existing predictions for this provider
        ProviderPrediction::where('service_provider_id', $provider->id)->delete();

        // Generate new predictions for each timeframe
        foreach ($timeframes as $period => $monthsAhead) {
            try {
                // Calculate the prediction using the advanced forecasting model
                list($predictedScore, $confidenceLevel, $factors) = $this->calculateForecast($evaluations, $monthsAhead);

                // Save prediction with current timestamp to ensure they're all created at the same time
                $now = Carbon::now();

                // Create the prediction record
                ProviderPrediction::create([
                    'service_provider_id' => $provider->id,
                    'predicted_score' => $predictedScore,
                    'confidence_level' => $confidenceLevel,
                    'prediction_date' => $now,
                    'prediction_period' => $period,
                    'factors' => $factors,
                    'model_version' => self::VERSION
                ]);

                $results[$period] = true;

                // Log success
                Log::info("Generated {$period} prediction for provider ID {$provider->id}: score {$predictedScore}");

            } catch (\Exception $e) {
                Log::error("Prediction failed for provider ID {$provider->id}, period {$period}: " . $e->getMessage());
                $results[$period] = false;
            }
        }

        return [
            'success' => count(array_filter($results)) === count($timeframes),
            'results' => $results
        ];
    }

    /**
     * Calculate forecast using advanced forecasting model inspired by Prophet
     * Includes trend, seasonality, and change point detection
     *
     * @param \Illuminate\Database\Eloquent\Collection $evaluations
     * @param int $monthsAhead
     * @return array [predictedScore, confidenceLevel, factors]
     */
    public function calculateForecast($evaluations, $monthsAhead = 1)
    {
        // Extract time series data
        $dates = [];
        $scores = [];
        $timestamps = [];

        $firstDate = $evaluations->first()->created_at;

        foreach ($evaluations as $evaluation) {
            $dates[] = $evaluation->created_at;
            $scores[] = $evaluation->total_score;
            $timestamps[] = $evaluation->created_at->diffInDays($firstDate) / 30; // Convert to months
        }

        $n = count($scores);

        // 1. Analyze trend component
        $trendComponent = $this->analyzeTrend($timestamps, $scores);

        // 2. Detect change points in the trend
        $changePoints = $this->detectChangePoints($timestamps, $scores);

        // 3. Analyze seasonality if we have enough data
        $seasonalityComponent = ($n >= 12) ? $this->analyzeSeasonality($timestamps, $scores) : 0;

        // 4. Generate forecast
        $lastTimestamp = end($timestamps);
        $futureTimestamp = $lastTimestamp + $monthsAhead;

        // Combine components for prediction
        $trendValue = $this->predictTrend($trendComponent, $futureTimestamp, $changePoints);
        $seasonalValue = ($n >= 12) ? $this->predictSeasonal($seasonalityComponent, $futureTimestamp) : 0;

        // Predicted score is the sum of trend and seasonal components, bounded to 0-100
        $predictedScore = max(0, min(100, $trendValue + $seasonalValue));

        // Calculate uncertainty based on historical variability and forecast horizon
        $residuals = [];
        $predicted = [];

        for ($i = 0; $i < $n; $i++) {
            $predictedTrend = $this->predictTrend($trendComponent, $timestamps[$i], $changePoints);
            $predictedSeasonal = ($n >= 12) ? $this->predictSeasonal($seasonalityComponent, $timestamps[$i]) : 0;
            $predicted[] = $predictedTrend + $predictedSeasonal;
            $residuals[] = abs($scores[$i] - $predicted[$i]);
        }

        // Calculate model quality metrics
        $mse = array_sum(array_map(function($x) { return $x * $x; }, $residuals)) / $n;
        $rmse = sqrt($mse);

        // Calculate R-squared
        $meanScore = array_sum($scores) / $n;
        $totalSumSquares = 0;
        $residualSumSquares = 0;

            for ($i = 0; $i < $n; $i++) {
            $totalSumSquares += pow($scores[$i] - $meanScore, 2);
            $residualSumSquares += pow($scores[$i] - $predicted[$i], 2);
        }

        $rSquared = $totalSumSquares > 0 ? 1 - ($residualSumSquares / $totalSumSquares) : 0;

        // Determine trend
        $trendDirection = $trendComponent['slope'] > 0 ? 'improving' : ($trendComponent['slope'] < 0 ? 'declining' : 'stable');
        $trendStrength = abs($trendComponent['slope']);

        // Calculate confidence level - decreases with forecast horizon and variability
        $confidenceBase = max(0.1, min(0.95, 0.7 * $rSquared + 0.3 * min(1, $n / 10)));
        $confidenceLevel = $confidenceBase * exp(-0.05 * $monthsAhead); // Decreases with longer forecasts but at a slower rate

        // Return prediction results
        return [
            $predictedScore,
            $confidenceLevel,
            [
                'trend' => $trendDirection,
                'trend_strength' => $trendStrength,
                'slope' => $trendComponent['slope'],
                'intercept' => $trendComponent['intercept'],
                'r_squared' => $rSquared,
                'rmse' => $rmse,
                'evaluations_count' => $n,
                'last_score' => end($scores),
                'avg_score' => $meanScore,
                'change_points' => $changePoints,
                'seasonality_detected' => $n >= 12 && abs($seasonalityComponent) > 0.5
            ]
        ];
    }

    /**
     * Analyze trend component using weighted regression
     *
     * @param array $timestamps
     * @param array $scores
     * @return array Trend component information
     */
    private function analyzeTrend($timestamps, $scores)
    {
        $n = count($timestamps);

        // Apply more weight to recent observations
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $weights[$i] = 1 + ($i / $n) * 0.5; // Weights from 1.0 to 1.5, higher for recent
        }

        // Calculate weighted means
        $weightSum = array_sum($weights);
        $weightedSumX = 0;
        $weightedSumY = 0;
        $weightedSumXY = 0;
        $weightedSumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $weightedSumX += $weights[$i] * $timestamps[$i];
            $weightedSumY += $weights[$i] * $scores[$i];
            $weightedSumXY += $weights[$i] * $timestamps[$i] * $scores[$i];
            $weightedSumXX += $weights[$i] * $timestamps[$i] * $timestamps[$i];
        }

        $meanX = $weightedSumX / $weightSum;
        $meanY = $weightedSumY / $weightSum;

        // Calculate weighted regression parameters
        $denominator = $weightedSumXX - $weightSum * $meanX * $meanX;

        if (abs($denominator) < 0.0001) {
            // If data is flat or nearly flat, use horizontal line
            $slope = 0;
            $intercept = $meanY;
        } else {
            $slope = ($weightedSumXY - $weightSum * $meanX * $meanY) / $denominator;
            $intercept = $meanY - $slope * $meanX;
        }

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'mean_x' => $meanX,
            'mean_y' => $meanY
        ];
    }

    /**
     * Detect change points in the trend
     *
     * @param array $timestamps
     * @param array $scores
     * @return array List of change points
     */
    private function detectChangePoints($timestamps, $scores)
    {
        $n = count($timestamps);
        if ($n < 10) {
            return []; // Need more data for meaningful change point detection
        }

        $changePoints = [];
        $windowSize = max(3, floor($n / 5)); // Adaptive window size

        // Look for significant changes in slope by sliding a window
        for ($i = $windowSize; $i < $n - $windowSize; $i++) {
            $leftTimestamps = array_slice($timestamps, $i - $windowSize, $windowSize);
            $leftScores = array_slice($scores, $i - $windowSize, $windowSize);

            $rightTimestamps = array_slice($timestamps, $i, $windowSize);
            $rightScores = array_slice($scores, $i, $windowSize);

            $leftTrend = $this->simpleRegression($leftTimestamps, $leftScores);
            $rightTrend = $this->simpleRegression($rightTimestamps, $rightScores);

            // Check if there's a significant change in slope
            if (abs($leftTrend['slope'] - $rightTrend['slope']) > 1.0) {
                $changePoints[] = [
                    'timestamp' => $timestamps[$i],
                    'score' => $scores[$i],
                    'left_slope' => $leftTrend['slope'],
                    'right_slope' => $rightTrend['slope']
                ];
            }
        }

        return $changePoints;
    }

    /**
     * Simple linear regression without weights
     *
     * @param array $x X values (timestamps)
     * @param array $y Y values (scores)
     * @return array Regression parameters
     */
    private function simpleRegression($x, $y)
    {
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumXX += $x[$i] * $x[$i];
        }

        $denominator = $n * $sumXX - $sumX * $sumX;

        if (abs($denominator) < 0.0001) {
            $slope = 0;
            $intercept = $sumY / $n;
        } else {
            $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
            $intercept = ($sumY - $slope * $sumX) / $n;
        }

        return [
            'slope' => $slope,
            'intercept' => $intercept
        ];
    }

    /**
     * Analyze seasonality component
     *
     * @param array $timestamps
     * @param array $scores
     * @return float Seasonal component value
     */
    private function analyzeSeasonality($timestamps, $scores)
    {
        // Simplified seasonality detection - would be more complex in a full Prophet implementation
        // This is a placeholder for the concept
        $seasonalComponent = 0;

        $n = count($timestamps);
        if ($n >= 12) { // Need at least a year of data for seasonal patterns
            // Detrend the data
            $trend = $this->analyzeTrend($timestamps, $scores);
            $detrended = [];

            for ($i = 0; $i < $n; $i++) {
                $detrended[$i] = $scores[$i] - ($trend['slope'] * $timestamps[$i] + $trend['intercept']);
            }

            // Look for seasonal patterns (simplified)
            if ($this->hasSeasonalPattern($detrended)) {
                $seasonalComponent = $this->estimateSeasonalComponent($detrended);
            }
        }

        return $seasonalComponent;
    }

    /**
     * Check if the detrended data has a seasonal pattern
     *
     * @param array $detrended
     * @return bool True if seasonal pattern is detected
     */
    private function hasSeasonalPattern($detrended)
    {
        // Simplified check - in reality would use autocorrelation or spectral analysis
        // For now, we'll just check if there's enough variation in detrended data
        $std = $this->standardDeviation($detrended);
        return $std > 2.0; // Arbitrary threshold
    }

    /**
     * Calculate standard deviation
     *
     * @param array $values
     * @return float Standard deviation
     */
    private function standardDeviation($values)
    {
        $n = count($values);
        if ($n === 0) return 0;

        $mean = array_sum($values) / $n;
        $variance = 0;

        foreach ($values as $val) {
            $variance += pow($val - $mean, 2);
        }

        return sqrt($variance / $n);
    }

    /**
     * Estimate seasonal component
     *
     * @param array $detrended
     * @return float Estimated seasonal component
     */
    private function estimateSeasonalComponent($detrended)
    {
        // Very simplified seasonal estimation
        // In a real implementation, would use Fourier series like Prophet
        return array_sum($detrended) / count($detrended);
    }

    /**
     * Predict trend value at a given timestamp
     *
     * @param array $trendComponent
     * @param float $timestamp
     * @param array $changePoints
     * @return float Predicted trend value
     */
    private function predictTrend($trendComponent, $timestamp, $changePoints)
    {
        // Basic linear prediction, but accounting for change points
        $value = $trendComponent['slope'] * $timestamp + $trendComponent['intercept'];

        // Adjust for change points
        foreach ($changePoints as $cp) {
            if ($timestamp > $cp['timestamp']) {
                // Apply the effect of change point on later predictions
                $slopeDiff = $cp['right_slope'] - $cp['left_slope'];
                $value += $slopeDiff * ($timestamp - $cp['timestamp']) * 0.5; // Dampen the effect
            }
        }

        return $value;
    }

    /**
     * Predict seasonal value at a given timestamp
     *
     * @param float $seasonalComponent
     * @param float $timestamp
     * @return float Predicted seasonal value
     */
    private function predictSeasonal($seasonalComponent, $timestamp)
    {
        // Simplified seasonal prediction
        // In a full implementation, would use Fourier decomposition like Prophet
        return $seasonalComponent; // Just return the component for now
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

        list(, $confidence, $factors) = $this->calculateForecast($evaluations);

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
            $trendInfo['icon'] = 'chart-line';
        } elseif ($factors['trend'] === 'declining') {
            $trendInfo['status'] = $factors['slope'] < -0.5 ? 'danger' : 'warning';
            $trendInfo['message'] = 'Tendance à la baisse';
            $trendInfo['icon'] = 'chart-line';
        } else {
            $trendInfo['status'] = 'info';
            $trendInfo['message'] = 'Performance stable';
            $trendInfo['icon'] = 'chart-line';
        }

        return $trendInfo;
    }
}
