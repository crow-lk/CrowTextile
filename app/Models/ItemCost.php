<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'Cost_id',
        'price',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function cost()
    {
        return $this->belongsTo(Cost::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class); // Relationship with Invoice
    }
}
