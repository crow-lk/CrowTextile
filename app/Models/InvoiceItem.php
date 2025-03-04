<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_id',
        'unit_cost',
        'total_amount',
        'quantity',
    ];

    protected static function booted()
    {
        static::created(function ($invoiceItem) {
            // Update the invoice totals when an invoice item is created
            $invoiceItem->updateInvoiceTotals();
        });

        static::updated(function ($invoiceItem) {
            // Update the invoice totals when an invoice item is updated
            $invoiceItem->updateInvoiceTotals();
        });

        static::deleted(function ($invoiceItem) {
            // Update the invoice totals when an invoice item is deleted
            $invoiceItem->updateInvoiceTotals();
        });
    }

    /**
     * Get the invoice that owns the invoice item.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the item associated with the invoice item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Update the invoice totals based on the related invoice items.
     */
    protected function updateInvoiceTotals()
    {
        $invoice = $this->invoice;

        if ($invoice) {
            // Calculate the total amount of all related invoice items
            $totalAmount = $invoice->invoiceItems()->sum('total_amount');

            // Update the invoice's amount and credit_balance
            $invoice->amount = $totalAmount;
            $invoice->credit_balance = $totalAmount; // Adjust this logic as needed
            $invoice->save();
        }
    }
}
