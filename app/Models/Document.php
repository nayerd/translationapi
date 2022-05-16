<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'file_name',
        'file_type',
        'file_content',
        'file_comments'
    ];

    /**
     * Related basket documents objects
     *
     * @return HasMany
     */
    public function basket_documents(): HasMany
    {
        return $this->hasMany(BasketDocument::class, 'document_id', 'id');
    }
}
