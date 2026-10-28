@extends('layouts.app')

@section('title', $invoice->invoice_number . ' | Admin')

@section('content')

<style>
    .invoice-page {
        max-width: 850px;
        margin: 0 auto;
    }

    .invoice-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .invoice-sheet {
        background: #fff;
        border-radius: 12px;
        padding: 36px;
        color: #111827;
    }

    .invoice-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #111827;
    }

    .invoice-top h1 {
        margin: 0;
        font-size: 26px;
        letter-spacing: 1px;
    }

    .invoice-top .meta {
        text-align: right;
        font-size: 14px;
        line-height: 1.7;
    }

    .invoice-top .meta strong {
        font-size: 18px;
    }

    .bill-to {
        margin: 22px 0;
        font-size: 14px;
        line-height: 1.7;
    }

    .bill-to .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .items-table th {
        text-align: left;
        padding: 10px 8px;
        background: #f3f4f6;
        border-bottom: 1px solid #d1d5db;
        font-size: 13px;
    }

    .items-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .items-table .num {
        text-align: right;
    }

    .invoice-totals {
        max-width: 280px;
        margin: 20px 0 0 auto;
        font-size: 14px;
    }

    .invoice-totals .row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
    }

    .invoice-totals .grand {
        margin-top: 6px;
        padding-top: 10px;
        border-top: 2px solid #111827;
        font-size: 17px;
        font-weight: 700;
    }

    .invoice-notes {
        margin-top: 24px;
        font-size: 13px;
        color: #374151;
    }

    .invoice-footer {
        margin-top: 36px;
        text-align: center;
        font-size: 13px;
        color: #6b7280;
    }

    .error-box {
        margin-bottom: 16px;
        padding: 12px 15px;
        border-radius: 8px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 14px;
    }

    .toolbar-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .toolbar-actions form {
        margin: 0;
    }

    .btn-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .success-box {
        margin-bottom: 16px;
        padding: 12px 15px;
        border-radius: 8px;
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-size: 14px;
    }

    .btn {
        display: inline-block;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid #d1d5db;
    }

    .btn-light {
        background: #fff;
        color: #374151;
    }

    .btn-primary {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    @media (max-width: 650px) {
        .invoice-sheet {
            padding: 20px;
        }

        .invoice-top {
            flex-direction: column;
        }

        .invoice-top .meta {
            text-align: left;
        }
    }

    /* Print only the invoice sheet */
    @media print {
        body * {
            visibility: hidden;
        }

        .invoice-sheet,
        .invoice-sheet * {
            visibility: visible;
        }

        .invoice-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border-radius: 0;
            padding: 0;
        }
    }
</style>

<div class="invoice-page">


    <div class="invoice-toolbar">
        <a href="{{ route('admin.sales.index') }}" class="btn btn-light">&larr; Back to Sales</a>

        <div class="toolbar-actions">
            <a href="{{ route('admin.sales.edit', $invoice) }}" class="btn btn-light">Edit</a>

            <form
                method="POST"
                action="{{ route('admin.sales.destroy', $invoice) }}"
                onsubmit="return confirm('Delete {{ $invoice->invoice_number }}? Its stock will be put back.');"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>

            <button type="button" class="btn btn-primary" onclick="window.print()">
                Print Invoice
            </button>
        </div>
    </div>

    <div class="invoice-sheet">

        <div class="invoice-top">
            <h1>WEEBSTATION</h1>

            <div class="meta">
                <strong>{{ $invoice->invoice_number }}</strong><br>
                @if ($invoice->gst_enabled)
                    Tax invoice<br>
                @endif
                Date: {{ $invoice->invoice_date->format('d M Y') }}<br>
                Payment: {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            </div>
        </div>

        <div class="bill-to">
            <div class="label">Bill to</div>
            <strong>{{ $invoice->customer_name }}</strong><br>

            @if ($invoice->customer_phone)
                {{ $invoice->customer_phone }}
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Size</th>
                    <th class="num">Price</th>
                    <th class="num">Qty</th>
                    @if ($invoice->gst_enabled)
                        <th class="num">GST</th>
                    @endif
                    <th class="num">Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->size ?? '-' }}</td>
                        <td class="num">₹{{ number_format($item->price, 2) }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        @if ($invoice->gst_enabled)
                            <td class="num">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>
                        @endif
                        <td class="num">₹{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="invoice-totals">
            <div class="row">
                <span>Subtotal</span>
                <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>

            @if ($invoice->discount > 0)
                <div class="row">
                    <span>Discount</span>
                    <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                </div>
            @endif

            @if ($invoice->gst_enabled)
                <div class="row">
                    <span>GST</span>
                    <span>₹{{ number_format($invoice->tax_total, 2) }}</span>
                </div>
            @endif

            <div class="row grand">
                <span>Total</span>
                <span>₹{{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="invoice-notes">
                <strong>Notes:</strong> {{ $invoice->notes }}
            </div>
        @endif

        <div class="invoice-footer">Thank you for shopping with us!</div>

    </div>
</div>

@endsection