<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $table = 'sizes';

    protected $fillable = ['name'];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'size_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'size_id');
    }
}
