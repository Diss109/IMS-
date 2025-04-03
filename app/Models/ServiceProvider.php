<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address'
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
