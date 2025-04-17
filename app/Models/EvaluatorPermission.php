<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluatorPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_type',
        'role',
    ];
}
