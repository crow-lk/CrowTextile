<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'item_id',
        'amount',
        'credit_balance',
        'payment_status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function itemParts()
    {
        return $this->hasMany(ItemPart::class); // Relationship with ItemPart
    }
}
