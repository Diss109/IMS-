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

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
