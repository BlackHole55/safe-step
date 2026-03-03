<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable = ['slug', 'title', 'description', 'time_limit'];

    public function options() {
        return $this->hasMany(Option::class);
    }
}
