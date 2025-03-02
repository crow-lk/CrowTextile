<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roll extends Model
{
    use SoftDeletes;

    protected $table = 'rolls';

    protected $fillable = [
        'roll_id',
        'weight',
        'yardage',
        'color_id',
    ];

    //relationship with color
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
