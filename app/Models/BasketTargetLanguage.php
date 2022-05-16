<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketTargetLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'basket_id',
        'language_id',
        'translation_price'
    ];

    /**
     * Related basket of the given basket translation
     *
     * @return BelongsTo
     */
    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class, 'basket_id', 'id');
    }

    /**
     * Related language of the relation basket -- translations
     *
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
