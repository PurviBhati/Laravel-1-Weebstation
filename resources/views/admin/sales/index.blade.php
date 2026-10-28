@extends('layouts.app')

@section('title', 'Sales | Admin')

@section('content')

<style>
    .sales-page {
        max-width: 1000px;
        margin: 0 auto;
    }

    .sales-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .sales-header h1 {
        margin: 0;
    }

    .sales-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        color: #111827;
    }

    .filters {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
        margin-bottom: 20px;
    }

    .filters .filter-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filters label {
        font-size: 13px;
        color: #374151;
    }

    .filters input {
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        color: #111827;
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

    .table-wrap {
        overflow-x: auto;
    }

    .sales-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sales-table th {
        text-align: left;
        padding: 10px;
        font-size: 13px;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .sales-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
    }

    .sales-table .num {
        text-align: right;
    }

    .empty-row {
        padding: 24px 10px;
        text-align: center;
        color: #6b7280;
    }

    .error-box {
        margin-bottom: 20px;
        padding: 12px 15px;
        border-radius: 8px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 14px;
    }

    .row-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .row-actions form {
        margin: 0;
    }

    .link-danger {
        padding: 0;
        border: none;
        background: none;
        color: #b91c1c;
        font: inherit;
        cursor: pointer;
        text-decoration: underline;
    }

    .pagination-wrap {
        margin-top: 18px;
    }
</style>

<div class="sales-page">

    <div class="sales-header">
        <h1>Sales</h1>

        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary">
            + New Invoice
        </a>
    </div>

    @if (session('error'))
        <div class="error-box">{{ session('error') }}</div>
    @endif

    <div class="sales-card">

        <form method="GET" action="{{ route('admin.sales.index') }}" class="filters">
            <div class="filter-field">
                <label for="q">Search</label>
                <input
                    type="text"
                    name="q"
                    id="q"
                    value="{{ request('q') }}"
                    placeholder="Invoice no. or customer"
                >
            </div>

            <div class="filter-field">
                <label for="from">From</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}">
            </div>

            <div class="filter-field">
                <label for="to">To</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}">
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>

            @if (request()->hasAny(['q', 'from', 'to']))
                <a href="{{ route('admin.sales.index') }}" class="btn btn-light">Clear</a>
            @endif
        </form>

        <div class="table-wrap">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="num">Items</th>
                        <th class="num">Total</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td class="num">{{ $invoice->items_count }}</td>
                            <td class="num">₹{{ number_format($invoice->total, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.sales.show', $invoice) }}">View</a>
                                    <a href="{{ route('admin.sales.edit', $invoice) }}">Edit</a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.sales.destroy', $invoice) }}"
                                        onsubmit="return confirm('Delete {{ $invoice->invoice_number }}? Its stock will be put back.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $invoices->links() }}
        </div>

    </div>
</div>

@endsection