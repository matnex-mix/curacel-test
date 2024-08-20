<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'encounter_date' => 'date'
    ];

    protected $fillable = [
        'total',
        'provider_name',
        'encounter_date',
        'hmo_id',
        'batch_id'
    ];

    public function hmo() : BelongsTo
    {
        return $this->belongsTo(Hmo::class);
    }

    public function batch() : BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function items() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
