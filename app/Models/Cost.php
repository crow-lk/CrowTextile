<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function itemCosts()
    {
        return $this->belongsTo(ItemCost::class);
    }

}
