<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'purchase_price', 'sell_price',
        'opening_stock', 'current_stock', 'description'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSoldAttribute()
    {
        return $this->sales()->sum('quantity');
    }

    public function getCostOfGoodsSoldAttribute()
    {
        return $this->total_sold * $this->purchase_price;
    }

}