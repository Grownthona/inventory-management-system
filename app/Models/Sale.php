<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'product_id', 'customer_name', 'quantity',
        'unit_price', 'gross_amount', 'discount', 'vat_percent',
        'vat_amount', 'net_amount', 'paid_amount', 'due_amount',
        'sale_date', 'note'
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];
}