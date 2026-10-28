<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use App\Models\Category;
use App\Models\Size;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['audience', 'size'])
            ->latest()
            ->get();

        $audiences = Audience::orderBy('id')->get();
        $sizes = Size::orderBy('id')->get();

        return view(
            'admin.categories.index',
            compact('categories', 'audiences', 'sizes')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:category,name',
            'audience_id' => 'required|exists:audiences,id',
            'size_id' => 'required|exists:sizes,id',
        ]);

        $audience = Audience::findOrFail($validated['audience_id']);

        $forWhom = match (strtolower($audience->name)) {
            'men' => 'men',
            'women' => 'women',
            'unisex' => 'everyone',
            default => null,
        };

        if ($forWhom === null) {
            return response()->json([
                'message' => 'Please select a valid audience.',
            ], 422);
        }

        $category = Category::create([
            'name' => $validated['name'],
            'audience_id' => $audience->id,
            'size_id' => $validated['size_id'],
            'for_whom' => $forWhom,
        ]);

        $category->load(['audience', 'size']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Category added successfully!',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'audience_name' => $category->audience->name,
                    'size_name' => $category->size->name,
                    'variants' => [],
                ],
            ], 201);
        }

        return redirect('/admin/categories')
            ->with('success', 'Category added successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            $message = 'This category has products. Reassign or delete those products first.';

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return redirect('/admin/categories')
                ->with('error', $message);
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Category deleted successfully!',
            ]);
        }

        return redirect('/admin/categories')
            ->with('success', 'Category deleted successfully!');
    }
}
