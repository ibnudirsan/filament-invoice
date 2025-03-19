<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'amount',
        'order_id',
        'virtual_account',
        'bank'
    ];
}
