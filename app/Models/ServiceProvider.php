<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_SHIPPING = 'armateur';
    const TYPE_AIRLINE = 'compagnie_aerienne';
    const TYPE_INT_TRANSPORT = 'transporteur_routier_int';
    const TYPE_LOCAL_TRANSPORT = 'transporteur_terrestre_local';
    const TYPE_AGENT = 'agent';
    const TYPE_STORE = 'magasin';
    const TYPE_OTHER = 'autre';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'service_type',
        'status',
    ];

    public static function getTypes()
    {
        return [
            self::TYPE_SHIPPING => 'Armateur',
            self::TYPE_AIRLINE => 'Compagnie Aérienne',
            self::TYPE_INT_TRANSPORT => 'Transporteur Routier International',
            self::TYPE_LOCAL_TRANSPORT => 'Transporteur Terrestre Local',
            self::TYPE_AGENT => 'Agent',
            self::TYPE_STORE => 'Magasin',
            self::TYPE_OTHER => 'Autre'
        ];
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Get the evaluations for the service provider.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the predictions for the service provider.
     */
    public function predictions()
    {
        return $this->hasMany(ProviderPrediction::class);
    }

    public function getLatestPrediction($period = 'next_month')
    {
        return $this->predictions()
            ->where('prediction_period', $period)
            ->latest('prediction_date')
            ->first();
    }

    public function getPerformanceTrend($limit = 5)
    {
        return $this->evaluations()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->pluck('total_score', 'created_at');
    }
}
