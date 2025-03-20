<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'part_id',
        'price',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class); // Relationship with Invoice
    }
}
