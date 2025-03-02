<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colors extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'color_code',
        'color',
    ];

    public function rolls()
    {
        return $this->hasMany(Roll::class, 'color_id');
    }
}
