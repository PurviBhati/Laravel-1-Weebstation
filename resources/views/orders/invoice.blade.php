<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->order_number }} | WEEB STATION</title>
 
    <style>
        * { box-sizing: border-box; }
 
            body {
                margin: 0;
                padding: 30px 16px;
                background: #f3f4f6;
                color: #111827;
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
                font-size: 14px;
                line-height: 1.6;
            }
    
            .sheet {
                max-width: 800px;
                margin: 0 auto;
                padding: 36px;
                background: #fff;
                border-radius: 12px;
            }
    
            .toolbar {
                max-width: 800px;
                margin: 0 auto 14px;
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }
    
            .btn {
                padding: 9px 16px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                background: #fff;
                color: #111827;
                font: inherit;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
            }
    
            .btn.primary {
                border-color: #111827;
                background: #111827;
                color: #fff;
            }
    
            .top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 20px;
                padding-bottom: 18px;
                border-bottom: 2px solid #111827;
            }
    
            h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
    
            .meta { text-align: right; }
            .meta strong { font-size: 17px; }
    
            .cols {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                margin: 22px 0;
            }
    
            .label {
                color: #6b7280;
                font-size: 11px;
                letter-spacing: 1px;
                text-transform: uppercase;
            }
    
            table { width: 100%; border-collapse: collapse; }
    
            th {
                padding: 9px 8px;
                background: #f3f4f6;
                border-bottom: 1px solid #d1d5db;
                font-size: 12px;
                text-align: left;
            }
    
            td { padding: 9px 8px; border-bottom: 1px solid #e5e7eb; }
    
            .num { text-align: right; }
    
            .totals { max-width: 280px; margin: 18px 0 0 auto; }
            .totals .row { display: flex; justify-content: space-between; padding: 5px 0; }
            .totals .grand {
                margin-top: 6px;
                padding-top: 9px;
                border-top: 2px solid #111827;
                font-size: 16px;
                font-weight: 700;
            }
    
            .foot { margin-top: 30px; color: #6b7280; font-size: 12px; text-align: center; }
    
            @media (max-width: 600px) {
                .sheet { padding: 20px; }
                .top, .cols { display: block; }
                .meta { margin-top: 10px; text-align: left; }
                .cols > div { margin-bottom: 16px; }
            }
    
            @media print {
                body { padding: 0; background: #fff; }
                .toolbar { display: none; }
                .sheet { max-width: none; padding: 0; border-radius: 0; }
            }
        </style>
    </head>
    <body>
        <div class="toolbar">
            <a href="{{ route('orders.show', $order) }}" class="btn">&larr; Back to order</a>
            <button type="button" class="btn primary" onclick="window.print()">Print</button>
        </div>

        <div class="sheet">
            <div class="top">
                <h1>WEEBSTATION</h1>
                <div class="meta">
                    <strong>{{ $order->order_number }}</strong><br>
                    Date: {{ $order->created_at->format('d M Y') }} <br>
                    Payment: {{ $order->payment_method == 'cod' ? 'Cash on delivery' : 'Online (Demo)' }}
                    ({{ ucfirst($order->payment_status) }})
                </div>
            </div>

            <div class="cols">
                <div>
                    <div class="label">Billed to: </div>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->customer_phone }}
                    @if ($order->customer_email)
                        <br>{{ $order->customer_email }}
                    @endif
                </div>

                <div>
                    <div class="label">Delivered to</div>
                    {{ $order->address }} <br>
                    {{ $order->city }} - {{ $order->pincode }}
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Size</th>
                        <th class="num">Price</th>
                        <th class="num">Qty</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->size ?? '-' }}</td>
                            <td class="num">₹ {{ number_format($item->price, 2) }}</td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="row">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($order->subtotal,2) }}</span>
                </div>

                <div class="row">
                    <span>Shipping</span>
                    <span>{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'Free' }}</span>
                </div>
                 @if ($order->gst_amount > 0)
                <div class="row">
                    <span>GST ({{ rtrim(rtrim(number_format($order->gst_rate, 2), '0'), '.') }}%)</span>
                    <span>₹{{ number_format($order->gst_amount, 2) }}</span>
                </div>
            @endif
            <div class="row grand">
                <span>Total</span>
                <span>₹{{ number_format($order->total, 2) }}</span>
            </div>
 
            </div>
        </div>

        <div class="foot">Thank you for shopping with WEEB STATION. Let you style do the DRAMA.</div>
    </body>
</html>