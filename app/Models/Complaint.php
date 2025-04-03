<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\Transporter;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'en_attente';
    public const STATUS_SOLVED = 'résolu';
    public const STATUS_UNSOLVED = 'non_résolu';

    protected $fillable = [
        'company_name',
        'email',
        'first_name',
        'last_name',
        'complaint_type',
        'urgency_level',
        'description',
        'status',
        'resolved_at',
        'resolution_notes',
        'assigned_to',
        'service_provider_id',
        'transporter_id',
        'admin_notes'
    ];

    protected $dates = [
        'resolved_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SOLVED,
            self::STATUS_UNSOLVED
        ];
    }
}
