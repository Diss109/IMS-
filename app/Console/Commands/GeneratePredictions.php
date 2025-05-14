<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PredictionService;

class GeneratePredictions extends Command
{
    protected $signature = 'predictions:generate {--period=next_month : The prediction period (next_month, next_quarter)}';
    protected $description = 'Generate performance predictions for service providers';

    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        parent::__construct();
        $this->predictionService = $predictionService;
    }

    public function handle()
    {
        $period = $this->option('period');
        $this->info("Generating predictions for period: {$period}");

        $stats = $this->predictionService->generateAllPredictions($period);

        $this->info("Prediction generation completed:");
        $this->info("- Total providers: {$stats['total']}");
        $this->info("- Successful predictions: {$stats['success']}");
        $this->info("- Failed predictions: {$stats['failed']}");
        $this->info("- Insufficient data: {$stats['no_data']}");

        return Command::SUCCESS;
    }
}
