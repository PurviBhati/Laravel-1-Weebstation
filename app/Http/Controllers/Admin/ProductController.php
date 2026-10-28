<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::with(['category', 'images'])
                ->latest()
                ->get(),
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $audiences = Audience::orderBy('id')->get();

        return view('admin.products.create', compact('categories', 'audiences'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $audiences = Audience::orderBy('id')->get();
        $product->load('category', 'images', 'variants');

        $currentVariants = $product->variants->pluck('pivot.stock', 'id');

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'audiences',
            'currentVariants'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|exists:product_images,id',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $hasVariants = $category->size_id !== null
            && Variant::where('size_id', $category->size_id)->exists();
        $inventory = $request->validate([
            'stock' => $hasVariants ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'variants' => $hasVariants ? 'required|array|min:1' : 'nullable|array',
            'variants.*' => 'required|integer|min:1',
        ]);
        $variantStocks = $inventory['variants'] ?? [];

        $allowedIds = Variant::where('size_id', $category->size_id)
            ->whereIn('id', array_keys($variantStocks))
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $submittedIds = array_map('strval', array_keys($variantStocks));
        if (count($allowedIds) !== count($submittedIds) || array_diff($submittedIds, $allowedIds)) {
            return back()->withInput()->withErrors([
                'variants' => 'One or more selected sizes do not belong to this category.',
            ]);
        }

        $totalStock = $variantStocks
            ? array_sum(array_map('intval', $variantStocks))
            : (int) $inventory['stock'];

        DB::transaction(function () use ($request, $product, $category, $validated, $variantStocks, $totalStock) {
            $product->update([
                'name' => $validated['name'],
                'category_id' => $category->id,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $totalStock,
                'status' => $validated['status'],
                'is_featured' => $request->boolean('is_featured'),
                'for_whom' => $category->for_whom,
            ]);

            // sync() adds new sizes, updates stock, and removes unchecked ones.
            $syncData = [];
            foreach ($variantStocks as $variantId => $stock) {
                $syncData[$variantId] = ['stock' => (int) $stock];
            }
            $product->variants()->sync($syncData);

            // Remove ticked images
            if ($request->filled('remove_images')) {
                $images = $product->images()
                    ->whereIn('id', $request->input('remove_images'))
                    ->get();

                foreach ($images as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            // Upload new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('products', 'public');
                        $product->images()->create(['path' => $path]);
                    }
                }
            }
        });

        return redirect('/admin/products')->with('success', 'Product updated!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,archived',
            'is_featured' => 'nullable|boolean',

            'images' => 'nullable|array|max:5',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $hasVariants = $category->size_id !== null
            && Variant::where('size_id', $category->size_id)->exists();
        $inventory = $request->validate([
            'stock' => $hasVariants ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'variants' => $hasVariants ? 'required|array|min:1' : 'nullable|array',
            'variants.*' => 'required|integer|min:1',
        ]);
        $variantStocks = $inventory['variants'] ?? [];

        // Verify that every submitted size belongs to this category.
        $allowedVariantIds = Variant::where('size_id', $category->size_id)
            ->whereIn('id', array_keys($variantStocks))
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $submittedVariantIds = array_map(
            'strval',
            array_keys($variantStocks)
        );

        if (
            count($allowedVariantIds) !== count($submittedVariantIds) ||
            array_diff($submittedVariantIds, $allowedVariantIds)
        ) {
            return back()->withInput()->withErrors([
                'variants' => 'One or more selected sizes do not belong to this category.',
            ]);
        }

        $totalStock = $variantStocks
            ? array_sum(array_map('intval', $variantStocks))
            : (int) $inventory['stock'];

        DB::transaction(function () use (
            $validated,
            $category,
            $variantStocks,
            $totalStock,
            $request
        ) {
            $product = Product::create([
                'name' => $validated['name'],
                'category_id' => $category->id,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $totalStock,
                'status' => $validated['status'],
                'is_featured' => $request->boolean('is_featured'),
                'for_whom' => $category->for_whom,
            ]);

            foreach ($variantStocks as $variantId => $stock) {
                $product->variants()->attach($variantId, [
                    'stock' => (int) $stock,
                ]);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('products', 'public');

                        $product->images()->create([
                            'path' => $path,
                        ]);
                    }
                }
            }
        });

        return redirect('/admin/products')
            ->with('success', 'Product added!');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect('/admin/products')
            ->with('success', 'Product deleted!');
    }

    public function getCategoriesByAudience(Audience $audience)
    {
        $categories = Category::with('size')
            ->where('audience_id', $audience->id)
            ->orderBy('name')
            ->get();

        return response()->json(
            $categories->map(function ($category) {
                $sizes = $category->size_id
                    ? Variant::where('size_id', $category->size_id)
                        ->orderBy('id')
                        ->get(['id', 'value'])
                    : collect();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'size_type' => $category->size?->name,
                    'sizes' => $sizes->values(),
                ];
            })->values()
        );
    }
}
