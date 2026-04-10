<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSale extends Model
{
    use HasFactory;

    // 1. Allow Mass Assignment for these columns
    protected $fillable = [
        'shift_id',
        'product_id',
        'quantity',
        'total_amount'
    ];

    // 2. Link this sale back to the specific Shift
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // 3. Link this sale back to the specific Product (for the Admin Modal)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}