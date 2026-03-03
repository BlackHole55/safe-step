<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['step_id', 'next_step_id', 'text', 'feedback', 'is_correct', 'score_points'];

    public function step() {
        return $this->belongsTo(Step::class);
    }

    public function nextStep() {
        return $this->belongsTo(Step::class, 'next_step_id');
    }
}
