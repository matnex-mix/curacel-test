<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'batching_strategy',
        'hmo_id'
    ];

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }
}
