<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'basket_id',
        'document_id',
    ];

    /**
     * Related document of the given basket document
     *
     * @return BelongsTo
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }

    /**
     * Related basket object of the given basket relation
     *
     * @return BelongsTo
     */
    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class, 'basket_id', 'id');
    }
}
