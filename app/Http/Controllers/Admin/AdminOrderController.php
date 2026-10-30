<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\OrderStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    private const STATUSES = [
        'pending',
        'confirmed',
        'shipped',
        'delivered',
        'cancelled',
    ];

    private const NEXT = [
        'pending' => 'confirmed',
        'confirmed' => 'shipped',
        'shipped' => 'delivered',
    ];

    public function index(Request $request): View
    {
        $status = (string) $request->input('status', '');
        $search = trim((string) $request->input('q', ''));

        $query = Order::withCount('items')
            ->when(
                in_array($status, self::STATUSES, true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                $status === 'address',
                fn ($query) => $query->whereNotNull('address_updated_at')
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($where) use ($search) {
                    $where->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            });

        $orders = (clone $query)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Order::query()
            ->selectRaw('status, COUNT(*) AS aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalCount = Order::count();

        $addressCount = Order::whereNotNull('address_updated_at')
            ->count();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'counts' => $counts,
            'totalCount' => $totalCount,
            'addressCount' => $addressCount,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', [
            'order' => $order,
            'nextStatus' => self::NEXT[$order->status] ?? null,
        ]);
    }

    public function updateStatus(
        Request $request,
        Order $order
    ): RedirectResponse {
        $request->validate([
            'status' => 'required|in:confirmed,shipped,delivered',
        ]);

        $newStatus = $request->input('status');

        $changed = DB::transaction(function () use ($order, $newStatus) {
            $locked = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((self::NEXT[$locked->status] ?? null) !== $newStatus) {
                return false;
            }

            $updates = ['status' => $newStatus];

            if (
                $newStatus === 'delivered'
                && $locked->payment_method === 'cod'
                && $locked->payment_status === 'pending'
            ) {
                $updates['payment_status'] = 'paid';
                $updates['paid_at'] = now();
            }

            $locked->update($updates);

            return true;
        });

        if (! $changed) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with(
                    'error',
                    'That status change is not allowed. The order may have changed.'
                );
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order marked as '.$newStatus.'.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $result = DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($locked->status, ['cancelled', 'delivered'], true)) {
                return null;
            }

            OrderStock::restore($locked);

            $wasPaid = $locked->payment_status === 'paid';

            $locked->update([
                'status' => 'cancelled',
                'cancelled_by' => 'admin',
                'cancelled_at' => now(),
                'payment_status' => $wasPaid
                    ? 'refunded'
                    : $locked->payment_status,
            ]);

            return $wasPaid;
        });

        if ($result === null) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'This order cannot be cancelled.');
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with(
                'success',
                'Order cancelled and its stock was put back.'
                .($result ? ' Remember to refund the payment.' : '')
            );
    }
}
