<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'notify_enabled',
        'notify_user_id',
        'notify_api_key',
        'notify_sender_id',
        'notify_phone',
        'notify_phones',
        'notify_unicode',
    ];

    protected $casts = [
        'notify_enabled' => 'boolean',
        'notify_unicode' => 'boolean',
        'notify_phones' => 'array',
    ];
}
