<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SimulationSession extends Model
{
    protected $fillable = [
        'uuid',
        'current_step_id',
        'journey_log',
        'total_score'
    ];

    protected $casts = [
        'journey_log' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($session) {
            $session->uuid = (string) Str::uuid();
        });
    }

    public function currentStep()
    {
        return $this->belongsTo(Step::class, 'current_step_id');
    }
}
