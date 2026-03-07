<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'direction',
        'cheque_number',
        'bank_name',
        'party_name',
        'amount',
        'cheque_date',
        'due_date',
        'remind_at',
        'reminder_sent_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cheque_date' => 'date',
        'due_date' => 'date',
        'remind_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];
}
