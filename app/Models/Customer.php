<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id'
    ];

    /**
     * Gets the related baskets of the customer
     *
     * @return HasMany
     */
    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class, 'customer_id', 'id');
    }
}
