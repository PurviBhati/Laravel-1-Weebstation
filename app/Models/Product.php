<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'product';

    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock', 'is_featured', 'status', 'for_whom', 'image'];

    protected $casts = ['is_featured' => 'boolean'];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            Variant::class,
            'product_variants',
            'product_id',
            'variant_id'
        )->withPivot('stock')->withTimestamps();
    }
}
