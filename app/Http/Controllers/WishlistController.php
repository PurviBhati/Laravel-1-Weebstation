<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $ids = session('wishlist', []);

        $products = Product::with(['images', 'category'])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get()
            ->sortByDesc(fn ($product) => array_search($product->id, $ids))
            ->values();

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:product,id',
        ]);

        $id = (int) $data['product_id'];
        $ids = array_map('intval', session('wishlist', []));

        if (in_array($id, $ids, true)) {
            $ids = array_values(array_diff($ids, [$id]));
            $message = 'Removed from your wishlist';
        } else {
            $ids[] = $id;
            $message = 'Added to your wishlist';
        }

        session(['wishlist' => $ids]);

        return back()->with('success', $message);
    }
}
