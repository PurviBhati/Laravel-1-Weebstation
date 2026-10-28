<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class StoreCategoryController extends Controller
{
    public function index(string $audience)
    {
        abort_unless(in_array($audience, ['men', 'women', 'everyone'], true), 404);

        $categories = Category::where('for_whom', $audience)
            ->whereHas('products', function ($query) {
                $query->where('status', 'active');
            })->withCount([
                'products as active_products_count' => function ($query) {
                    $query->where('status', 'active');
                },
            ])->orderBy('name')
            ->get();

        $covers = [];
        if ($categories->isNotEmpty()) {
            $productByCategory = Product::where('status', 'active')
                ->whereIn('category_id', $categories->pluck('id'))
                ->with('images')
                ->latest()
                ->get()
                ->groupBy('category_id');

            foreach ($categories as $category) {
                $withImage = $productByCategory
                    ->get($category->id, collect())
                    ->first(fn ($product) => $product->images->isNotEmpty());
                $covers[$category->id] = $withImage?->images->first()?->path;
            }
        }

        return view('categories.index', compact('audience', 'categories', 'covers'));
    }
}
