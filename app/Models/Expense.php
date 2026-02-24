<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'amount', 'category', 'expense_date', 'description'];

    protected $casts = [
        'expense_date' => 'date',
    ];
}