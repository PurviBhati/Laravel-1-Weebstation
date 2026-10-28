<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'name',
        'for_whom',
        'audience_id',
        'size_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class, 'audience_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            Variant::class,
            'category_variants',
            'category_id',
            'variant_id'
        )->withTimestamps();
    }
}
