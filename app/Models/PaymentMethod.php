<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'account_holder',
        'bank_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
