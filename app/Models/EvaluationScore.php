<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'main_criterion',
        'sub_criterion',
        'main_weight',
        'sub_weight',
        'score',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
