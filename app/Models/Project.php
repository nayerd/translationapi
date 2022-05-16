<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id'
    ];


    /**
     * Related baskets of the current project
     *
     * @return HasMany
     */
    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class, 'project_id', 'id');
    }


}
