<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::withCount('items');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->paymennt_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sortOptions = [
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'amount_high' => ['total', 'desc'],
            'amount_low' => ['total', 'asc'],
            'order_asc' => ['order_number', 'asc'],
            'order_desc' => ['order_number', 'desc'],
        ];

        $sort = $request->input('sort', 'newest');
        [$column, $direction] = $sortOptions[$sort]
            ?? $sortOptions['newest'];

        $orders = $query
            ->orderBy($column, $direction)
            ->paginate(15)
            ->withQueryString();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $paidOrders = Order::where('payment_status', 'paid')->count();

        return view('admin.orders.index', compact(
            'orders', 'totalOrders', 'pendingOrders', 'paidOrders', 'sort'
        ));
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }
}
