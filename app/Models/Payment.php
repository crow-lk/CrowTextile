<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            $invoice = Invoice::find($payment->invoice_id);

            // Reduce credit balance
            $invoice->decrement('credit_balance', $payment->amount_paid);
        });

        // Restore credit balance if the payment is deleted
        static::deleting(function ($payment) {
            $payment->invoice->increment('credit_balance', $payment->amount_paid);
        });

        static::saved(function ($payment) {
            $invoice = Invoice::find($payment->invoice_id);
            $amount = $payment->amount_paid; // Use the amount paid for the message
            $creditBalance = $invoice->credit_balance; // Get the current credit balance

            if ($creditBalance > 0) {
                $invoice->payment_status = 'Partial Paid';
            } else {
                $invoice->payment_status = 'Paid';
            }
            $invoice->save(); // Save the updated invoice
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
