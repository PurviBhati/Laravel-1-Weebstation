<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::withCount('items')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->input('q');

                $query->where(function ($where) use ($search) {
                    $where->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('invoice_date', '>=', $request->input('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('invoice_date', '<=', $request->input('to'));
            })
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.sales.index', compact('invoices'));
    }

    public function create()
    {
        return view('admin.sales.form', [
            'invoice' => null,
            'prefill' => [],
            'products' => $this->productsForForm(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateInvoice($request);
        $wanted = $this->mergeItems($validated['items']);

        $invoice = DB::transaction(function () use ($validated, $wanted, $request) {
            [$lines, $subtotal] = $this->buildLines($wanted);
            $discount = $this->discountFor($subtotal, $validated);

            $gst = ! empty($validated['gst_enabled']);
            [$lines, $taxTotal] = $this->applyTax($lines, $subtotal, $discount, $gst);

            $invoice = Invoice::create([
                'invoice_number' => $this->nextInvoiceNumber(),
                'invoice_date' => $validated['invoice_date'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_enabled' => $gst,
                'tax_total' => $taxTotal,
                'total' => round($subtotal - $discount + $taxTotal, 2),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()?->id,
            ]);

            $this->saveItems($invoice, $lines);

            return $invoice;
        });

        return redirect()
            ->route('admin.sales.show', $invoice)
            ->with('success', 'Invoice created!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');

        return view('admin.sales.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        // An item whose product was deleted cannot be edited safely.
        if ($invoice->items->contains(fn ($item) => ! $item->product_id)) {
            return redirect()
                ->route('admin.sales.show', $invoice)
                ->with('error', 'This invoice has an item whose product was deleted, so it cannot be edited. You can still delete it.');
        }

        $held = [];   // stock this invoice is already holding
        $prices = [];   // price each product was sold at
        $prefill = [];

        foreach ($invoice->items as $item) {
            $key = $item->product_id.'-'.($item->variant_id ?? 0);

            $held[$key] = ($held[$key] ?? 0) + $item->quantity;
            $prices[$item->product_id] = (float) $item->price;

            $prefill[] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'tax_rate' => (float) $item->tax_rate,
            ];
        }

        // Saved prices are kept, so old invoices do not change with the product price.
        $products = $this->productsForForm($held, array_keys($prices))
            ->map(function ($product) use ($prices) {
                if (isset($prices[$product['id']])) {
                    $product['price'] = $prices[$product['id']];
                }

                return $product;
            })
            ->values();

        return view('admin.sales.form', compact('invoice', 'prefill', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $this->validateInvoice($request);
        $wanted = $this->mergeItems($validated['items']);

        DB::transaction(function () use ($validated, $wanted, $invoice) {
            // Lock the invoice while it is being changed.
            $invoice = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $invoice->load('items');

            $oldProductIds = $invoice->items->pluck('product_id')->filter()->values()->all();
            $oldPrices = $invoice->items->whereNotNull('product_id')->pluck('price', 'product_id')->all();

            // 1. Put back the stock the old version took.
            $this->restoreStock($invoice);
            $invoice->items()->delete();

            // 2. Check and take stock for the new items.
            [$lines, $subtotal] = $this->buildLines($wanted, $oldProductIds, $oldPrices);
            $discount = $this->discountFor($subtotal, $validated);

            $gst = ! empty($validated['gst_enabled']);
            [$lines, $taxTotal] = $this->applyTax($lines, $subtotal, $discount, $gst);

            $invoice->update([
                'invoice_date' => $validated['invoice_date'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_enabled' => $gst,
                'tax_total' => $taxTotal,
                'total' => round($subtotal - $discount + $taxTotal, 2),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->saveItems($invoice, $lines);
        });

        return redirect()
            ->route('admin.sales.show', $invoice)
            ->with('success', 'Invoice updated!');
    }

    public function destroy(Invoice $invoice)
    {
        $number = $invoice->invoice_number;

        DB::transaction(function () use ($invoice) {
            $invoice->load('items');

            $this->restoreStock($invoice);

            $invoice->items()->delete();
            $invoice->delete();
        });

        return redirect()
            ->route('admin.sales.index')
            ->with('success', "Invoice {$number} deleted and its stock was put back.");
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'invoice_date' => 'required|date',
            'payment_method' => 'required|in:cash,upi,card,bank_transfer',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'gst_enabled' => 'nullable|boolean',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:product,id',
            'items.*.variant_id' => 'nullable|integer|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax_rate' => 'nullable|numeric|in:0,5,12,18,28',
        ]);
    }

    private function mergeItems(array $items): array
    {
        $wanted = [];

        foreach ($items as $item) {
            $variantId = ! empty($item['variant_id']) ? (int) $item['variant_id'] : null;
            $key = $item['product_id'].'-'.($variantId ?? 0);

            if (! isset($wanted[$key])) {
                $wanted[$key] = [
                    'product_id' => (int) $item['product_id'],
                    'variant_id' => $variantId,
                    'quantity' => 0,
                    'tax_rate' => (float) ($item['tax_rate'] ?? 0),   // GST % of the first row
                ];
            }

            $wanted[$key]['quantity'] += (int) $item['quantity'];
        }

        return $wanted;
    }

    /**
     * Check stock and prepare the invoice lines. Does not change any stock yet.
     *
     * @param  array  $allowInactiveIds  products already on this invoice (editing)
     * @param  array  $priceOverrides  product_id => price it was originally sold at
     * @return array{0: array, 1: float} [lines, subtotal]
     */
    private function buildLines(array $wanted, array $allowInactiveIds = [], array $priceOverrides = []): array
    {
        $lines = [];
        $subtotal = 0;

        foreach ($wanted as $row) {
            // Lock the rows so two sales cannot take the same stock.
            $product = Product::where('id', $row['product_id'])
                ->where(function ($query) use ($allowInactiveIds) {
                    $query->where('status', 'active');

                    if ($allowInactiveIds) {
                        $query->orWhereIn('id', $allowInactiveIds);
                    }
                })
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'items' => 'One of the selected products is not available.',
                ]);
            }

            $variant = null;

            if ($row['variant_id']) {
                // Product with sizes: check the stock of that size.
                $variant = Variant::find($row['variant_id']);

                $stockRow = DB::table('product_variants')
                    ->where('product_id', $row['product_id'])
                    ->where('variant_id', $row['variant_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $variant || ! $stockRow) {
                    throw ValidationException::withMessages([
                        'items' => "That size is not available for {$product->name}.",
                    ]);
                }

                $available = (int) $stockRow->stock;
                $label = "{$product->name} ({$variant->value})";
            } else {
                // Product without sizes: it must really have none.
                $hasSizes = DB::table('product_variants')
                    ->where('product_id', $row['product_id'])
                    ->exists();

                if ($hasSizes) {
                    throw ValidationException::withMessages([
                        'items' => "Please choose a size for {$product->name}.",
                    ]);
                }

                $available = (int) $product->stock;
                $label = $product->name;
            }

            if ($available < $row['quantity']) {
                throw ValidationException::withMessages([
                    'items' => "Not enough stock for {$label}. Only {$available} left.",
                ]);
            }

            // Price comes from the database (or the saved invoice price), never from the form.
            $price = (float) ($priceOverrides[$product->id] ?? $product->price);
            $lineTotal = round($price * $row['quantity'], 2);
            $subtotal += $lineTotal;

            $lines[] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'size' => $variant?->value,
                'price' => $price,
                'quantity' => $row['quantity'],
                'line_total' => $lineTotal,
                'tax_rate' => $row['tax_rate'],
                'tax_amount' => 0,
            ];
        }

        return [$lines, round($subtotal, 2)];
    }

    private function discountFor(float $subtotal, array $validated): float
    {
        $discount = round((float) ($validated['discount'] ?? 0), 2);

        if ($discount > $subtotal) {
            throw ValidationException::withMessages([
                'discount' => 'Discount cannot be more than the subtotal.',
            ]);
        }

        return $discount;
    }

    /**
     * Work out the GST of every line.
     *
     * Prices are before GST. The discount is shared between the lines by value,
     * then GST is charged on what is left of each line. With GST off, every line
     * gets 0%.
     *
     * @return array{0: array, 1: float} [lines with tax, total GST]
     */
    private function applyTax(array $lines, float $subtotal, float $discount, bool $gst): array
    {
        $taxTotal = 0.0;

        foreach ($lines as $i => $line) {
            if (! $gst || $subtotal <= 0) {
                $lines[$i]['tax_rate'] = 0;
                $lines[$i]['tax_amount'] = 0;

                continue;
            }

            $share = $discount * ($line['line_total'] / $subtotal);
            $taxable = $line['line_total'] - $share;
            $tax = round($taxable * $line['tax_rate'] / 100, 2);

            $lines[$i]['tax_amount'] = $tax;
            $taxTotal += $tax;
        }

        return [$lines, round($taxTotal, 2)];
    }

    private function saveItems(Invoice $invoice, array $lines): void
    {
        foreach ($lines as $line) {
            $invoice->items()->create($line);

            // Reduce the size stock (if the product has sizes) and the product's stock.
            if ($line['variant_id']) {
                DB::table('product_variants')
                    ->where('product_id', $line['product_id'])
                    ->where('variant_id', $line['variant_id'])
                    ->decrement('stock', $line['quantity']);
            }

            Product::where('id', $line['product_id'])
                ->decrement('stock', $line['quantity']);
        }
    }

    private function restoreStock(Invoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            // Product was deleted: nothing to put back.
            if (! $item->product_id) {
                continue;
            }

            if ($item->variant_id) {
                $updated = DB::table('product_variants')
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->increment('stock', $item->quantity);

                // The size was removed from the product later: skip it.
                if (! $updated) {
                    continue;
                }
            }

            Product::where('id', $item->product_id)
                ->increment('stock', $item->quantity);
        }
    }

    /**
     * Products, prices and stock for the invoice form.
     *
     * @param  array  $held  "productId-variantId" => quantity this invoice already holds
     * @param  array  $includeIds  product ids to list even if they are no longer active
     */
    private function productsForForm(array $held = [], array $includeIds = [])
    {
        return Product::where(function ($query) use ($includeIds) {
            $query->where('status', 'active');

            if ($includeIds) {
                $query->orWhereIn('id', $includeIds);
            }
        })
            ->with('variants')
            ->orderBy('name')
            ->get()
            ->map(function ($product) use ($held) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'stock' => (int) $product->stock + ($held[$product->id.'-0'] ?? 0),
                    'sizes' => $product->variants->map(fn ($variant) => [
                        'id' => $variant->id,
                        'value' => $variant->value,
                        'stock' => (int) $variant->pivot->stock
                            + ($held[$product->id.'-'.$variant->id] ?? 0),
                    ])->values(),
                ];
            })
            ->values();
    }

    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->year.'-';

        $last = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('invoice_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
