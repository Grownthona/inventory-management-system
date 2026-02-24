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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')->where('reference_type', 'sale');
    }

    /**
     * Calculate and return all financial values for a sale
     */
    public static function calculateAmounts($quantity, $unitPrice, $discount, $vatPercent, $paidAmount): array
    {
        $grossAmount = $quantity * $unitPrice;
        $vatAmount   = round(($grossAmount - $discount) * ($vatPercent / 100), 2);
        $netAmount   = $grossAmount - $discount + $vatAmount;
        $dueAmount   = max(0, $netAmount - $paidAmount);

        return compact('grossAmount', 'vatAmount', 'netAmount', 'dueAmount');
    }
}