<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, string $audience)
    {
        abort_unless(
            in_array($audience, ['men', 'women', 'everyone'], true),
            404
        );

        $titles = [
            'men' => 'For all Dwags',
            'women' => 'For all Divas',
            'everyone' => "For y'all",
        ];

        // Products belonging to this audience, plus products
        // explicitly marked as suitable for everyone.
        $productQuery = Product::query()
            ->where('status', 'active')
            ->where(function ($query) use ($audience) {
                $query->where('for_whom', $audience);

                if ($audience !== 'everyone') {
                    $query->orWhere('for_whom', 'everyone');
                }
            });

        // Only show categories that have matching active products.
        $categories = Category::whereHas('products', function ($query) use ($audience) {
            $query->where('status', 'active')
                ->where(function ($inner) use ($audience) {
                    $inner->where('for_whom', $audience);

                    if ($audience !== 'everyone') {
                        $inner->orWhere('for_whom', 'everyone');
                    }
                });
        })->orderBy('name')->get();

        // Apply the selected category when one is provided.
        if ($request->filled('category')) {
            $productQuery->where('category_id', $request->query('category'));
        }

        $products = $productQuery
            ->with(['category', 'images'])
            ->latest()
            ->get();

        $selected = $categories->firstWhere(
            'id',
            $request->query('category')
        );

        return view('shop.index', [
            'title' => $titles[$audience],
            'audience' => $audience,
            'categories' => $categories,
            'selected' => $selected,
            'products' => $products,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active', 404);

        $product->load(['category', 'images']);

        return view('shop.show', [
            'product' => $product,
        ]);
    }
}
