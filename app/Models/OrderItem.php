<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public $fillable = [
        'unit_price',
        'name',
        'quantity',
        'sub_total'
    ];

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
