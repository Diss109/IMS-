<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderPrediction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_provider_id',
        'predicted_score',
        'confidence_level',
        'prediction_date',
        'prediction_period', // e.g., 'next_month', 'next_quarter', etc.
        'factors', // JSON field to store factors affecting prediction
        'model_version',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'predicted_score' => 'float',
        'confidence_level' => 'float',
        'prediction_date' => 'datetime',
        'factors' => 'json',
    ];

    /**
     * Get the service provider that owns the prediction.
     */
    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
