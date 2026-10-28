<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();

        $orders = Order::where('customer_id', $customer->id);
        $wishCount = WishlistItem::where('customer_id', $customer->id);

        return view('account.index', [
            'customer' => $customer,
            'orderCount' => (clone $orders)->count(),
            'activeCount' => (clone $orders)->whereIn('status', ['pending', 'confirmed', 'shipped'])->count(),
            'wishCount' => WishlistItem::count(),
            'recent' => (clone $orders)->withCount('items')->latest()->limit(3)->get(),
        ]);
    }

    public function profile()
    {
        return view('account.profile', [
            'customer' => auth('customer')->user(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|digits:6',
        ]);

        auth('customer')->user()->update($data);

        return redirect()->route('account.profile')->with('success', 'Your details were saved');
    }
}
