<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target',
        'current_value',
        'period',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'target' => 'float',
        'current_value' => 'float',
        'is_active' => 'boolean',
    ];
}
