<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'value',
        'size_id',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_variants',
            'variant_id',
            'category_id'
        )->withTimestamps();
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
