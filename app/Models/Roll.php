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
        'batch_code',
        'weight',
        'yardage',
        'supplier_id',
        'color_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    //relationship with color
    public function color()
    {
        return $this->belongsTo(Colors::class, 'color_id');
    }
}
