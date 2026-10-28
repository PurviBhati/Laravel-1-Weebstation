<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\Variant;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::with('variants')
            ->orderBy('name')->get();

        return view('admin.sizes.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name',
        ]);
        Size::create($validated);

        return redirect()->route('admin.sizes.index')->with('success', 'Size type added successfully');
    }

    public function storeVariant(Request $request, Size $size)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:50',
        ]);

        $exists = Variant::where('size_id', $size->id)->where('value', $validated['value'])->exists();

        if ($exists) {
            return back()
                ->withErrors(['value' => 'This value already exists for this size type'])
                ->withInput();
        }

        $size->variants()->create($validated);

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'Variant value added successfully');
    }

    public function destroyVariant(Variant $variant)
    {
        if ($variant->categories()->exists()) {
            return back()->withErrors(['variant' => 'This value is assigned to a catregory and cannot be deleted.']);
        }

        $variant->delete();

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'Variant value deleted successfully');
    }

    public function destroy(Size $size)
    {
        if ($size->categories()->exists() || $size->variants()->exists()) {
            return back()->withErrors(['size' => 'This size type is in use and cannot be deleted.']);
        }
        $size->delete();

        return redirect()->route('admin.sizes.index')->with('success', 'Size type deleted successfully');
    }
}
