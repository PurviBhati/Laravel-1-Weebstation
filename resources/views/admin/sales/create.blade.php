@extends('layouts.app')

@section('title', $invoice ? 'Edit Invoice | Admin' : 'New Invoice | Admin')

@section('content')

<style>
    .invoice-form {
        max-width: 950px;
        margin: 0 auto;
    }

    .invoice-form h1 {
        margin: 0 0 5px;
    }

    .form-subtitle {
        margin: 0 0 25px;
        color: var(--ws-pewter);
    }

    .form-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        color: #111827;
    }

    .form-card label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        color: #374151;
    }

    .form-card input,
    .form-card select,
    .form-card textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        color: #111827;
    }

    .form-card input[readonly] {
        background: #f3f4f6;
    }

    .form-card select:disabled,
    .form-card input:disabled {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }

    .form-card input:focus,
    .form-card select:focus,
    .form-card textarea:focus {
        outline: 2px solid #111827;
        outline-offset: 0;
    }

    .form-card textarea {
        min-height: 80px;
        resize: vertical;
        font-family: inherit;
    }

    .section-title {
        margin: 26px 0 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 16px;
        font-weight: 600;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .form-card input[type="checkbox"],
    .form-card input[type="radio"] {
        width: auto;
        padding: 0;
        accent-color: #111827;
    }

    .gst-options {
        display: flex;
        gap: 10px;
    }

    .gst-options .gst-toggle {
        justify-content: flex-start;
        white-space: nowrap;
    }

    .gst-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-weight: 500;
        cursor: pointer;
    }

    .gst-hint {
        margin-top: 5px;
        font-size: 12px;
        color: #6b7280;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .field {
        margin-bottom: 16px;
    }

    /* Item rows */
    .item-row {
        display: grid;
        grid-template-columns: 2fr 1.3fr 1fr 0.9fr 0.8fr 1fr 40px;
        align-items: end;
        gap: 12px;
        padding: 12px;
        margin-bottom: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }

    .item-row label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .item-remove {
        width: 40px;
        height: 40px;
        padding: 0;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fee2e2;
        color: #991b1b;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }

    .item-remove:hover {
        background: #fecaca;
    }

    /* Totals */
    .totals {
        max-width: 340px;
        margin: 20px 0 0 auto;
    }

    .totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        font-size: 14px;
    }

    .totals-row input {
        max-width: 140px;
        text-align: right;
    }

    .totals-row.grand {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 2px solid #111827;
        font-size: 17px;
        font-weight: 700;
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

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
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

    .btn-primary:hover {
        background: #000;
    }

    @media (max-width: 800px) {
        .grid-4,
        .grid-3,
        .grid-2 {
            grid-template-columns: 1fr;
        }

        .item-row {
            grid-template-columns: 1fr 1fr;
        }

        .item-remove {
            width: 100%;
        }

        .form-card {
            padding: 18px;
        }
    }
</style>

<div class="invoice-form">

    @if ($invoice)
        <h1>Edit Invoice</h1>
        <p class="form-subtitle">
            {{ $invoice->invoice_number }}: saving puts back the stock of the old version
            and takes stock again for the items below.
        </p>
    @else
        <h1>New Invoice</h1>
        <p class="form-subtitle">Create a sales invoice. Stock is reduced when you save.</p>
    @endif

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form
        id="invoiceForm"
        action="{{ $invoice ? route('admin.sales.update', $invoice) : route('admin.sales.store') }}"
        method="POST"
    >
        @csrf

        @if ($invoice)
            @method('PUT')
        @endif

        <div class="form-card">

            {{-- Invoice details --}}
            <div class="section-title">Invoice details</div>

            <div class="grid-4">
                <div class="field">
                    <label for="invoice_date">Invoice date</label>
                    <input
                        type="date"
                        name="invoice_date"
                        id="invoice_date"
                        value="{{ old('invoice_date', $invoice ? $invoice->invoice_date->toDateString() : now()->toDateString()) }}"
                        required
                    >
                </div>

                <div class="field">
                    <label for="payment_method">Payment method</label>
                    <select name="payment_method" id="payment_method" required>
                        @foreach (['cash' => 'Cash', 'upi' => 'UPI', 'card' => 'Card', 'bank_transfer' => 'Bank transfer'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('payment_method', $invoice->payment_method ?? 'cash') == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="border: none;">
                    <label>GST</label>
                    <input type="radio" name="gst_enabled" value="1" @checked(old('gst_enabled', $invoice->gst_enabled ?? false))>
                    <div class="gst-hint">Prices are before GST. GST is added on top.</div>
                </div>

                <div class="field">
                    <label>Invoice number</label>
                    <input type="text" value="{{ $invoice->invoice_number ?? 'Generated when saved' }}" readonly>
                </div>
            </div>

            {{-- Customer --}}
            <div class="section-title">Customer</div>

            <div class="grid-2">
                <div class="field">
                    <label for="customer_name">Customer name</label>
                    <input
                        type="text"
                        name="customer_name"
                        id="customer_name"
                        value="{{ old('customer_name', $invoice->customer_name ?? '') }}"
                        placeholder="Enter customer name"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="field">
                    <label for="customer_phone">Phone (optional)</label>
                    <input
                        type="text"
                        name="customer_phone"
                        id="customer_phone"
                        value="{{ old('customer_phone', $invoice->customer_phone ?? '') }}"
                        placeholder="Phone number"
                        maxlength="20"
                    >
                </div>
            </div>

            {{-- Items --}}
            <div class="section-title">Items</div>

            <div id="items-container"></div>

            <button type="button" id="add-item" class="btn btn-light">+ Add item</button>

            {{-- Totals --}}
            <div class="totals">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <strong id="subtotal-text">₹0.00</strong>
                </div>

                <div class="totals-row">
                    <label for="discount" style="margin:0;">Discount (₹)</label>
                    <input
                        type="number"
                        name="discount"
                        id="discount"
                        min="0"
                        step="0.01"
                        value="{{ old('discount', $invoice->discount ?? 0) }}"
                    >
                </div>

                <div class="totals-row" id="gst-row" style="display:none;">
                    <span>GST</span>
                    <strong id="gst-text">₹0.00</strong>
                </div>

                <div class="totals-row grand">
                    <span>Grand total</span>
                    <span id="grand-text">₹0.00</span>
                </div>
            </div>

            {{-- Notes --}}
            <div class="section-title">Notes</div>

            <div class="field">
                <textarea
                    name="notes"
                    id="notes"
                    placeholder="Optional notes"
                    maxlength="1000"
                >{{ old('notes', $invoice->notes ?? '') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="actions">
                <a
                    href="{{ $invoice ? route('admin.sales.show', $invoice) : route('admin.sales.index') }}"
                    class="btn btn-light"
                >Cancel</a>

                <button type="submit" class="btn btn-primary">
                    {{ $invoice ? 'Update Invoice' : 'Save Invoice' }}
                </button>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const products = @json($products);

    // Rows restored after a validation error.
    const oldItems = @json(array_values(old('items', $prefill ?? [])));

    const container    = document.getElementById('items-container');
    const addButton    = document.getElementById('add-item');
    const discountEl   = document.getElementById('discount');
    const subtotalText = document.getElementById('subtotal-text');
    const grandText    = document.getElementById('grand-text');
    const form         = document.getElementById('invoiceForm');

    let rowIndex = 0;

    // GST
    const gstToggle  = document.querySelector('input[name="gst_enabled"]');

    // True when "With GST" is selected.
    function gstIsOn() {
        const chosen = document.querySelector('input[name="gst_enabled"]:checked');

        return chosen !== null && chosen.value === '1';
    }
    const gstRow     = document.getElementById('gst-row');
    const gstText    = document.getElementById('gst-text');
    const taxRates   = [0, 5, 12, 18, 28];
    const defaultTax = 5;   // preselected GST % for a new row

    const taxOptions = taxRates.map(function (rate) {
        return '<option value="' + rate + '">' + rate + '%</option>';
    }).join('');

    function money(n) {
        return '₹' + Number(n).toFixed(2);
    }

    function findProduct(id) {
        return products.find(function (p) {
            return String(p.id) === String(id);
        });
    }

    function updateTotals() {
        const rows = container.querySelectorAll('.item-row');
        const gstOn = gstIsOn();

        let subtotal = 0;

        rows.forEach(function (row) {
            subtotal += Number(row.dataset.total || 0);
        });

        let discount = Number(discountEl.value || 0);

        if (discount < 0) {
            discount = 0;
        }

        if (discount > subtotal) {
            discount = subtotal;
            discountEl.value = subtotal.toFixed(2);
        }

        // GST: the discount is shared between the lines by value,
        // then each line is taxed on what is left (same as the server).
        let tax = 0;

        if (gstOn && subtotal > 0) {
            rows.forEach(function (row) {
                const lineTotal = Number(row.dataset.total || 0);
                const rate      = Number(row.querySelector('.item-tax').value || 0);

                if (lineTotal > 0 && rate > 0) {
                    const taxable = lineTotal - discount * (lineTotal / subtotal);
                    tax += Math.round(taxable * rate) / 100;
                }
            });
        }

        tax = Math.round(tax * 100) / 100;

        gstRow.style.display = gstOn ? 'flex' : 'none';
        gstText.textContent  = money(tax);

        subtotalText.textContent = money(subtotal);
        grandText.textContent    = money(subtotal - discount + tax);
    }

    // GST ticked or unticked: switch every row's GST dropdown on or off.
    function applyGstState() {
        container.querySelectorAll('.item-tax').forEach(function (select) {
            select.disabled = !gstIsOn();
        });

        updateTotals();
    }

    function updateRowTotal(row) {
        const product  = findProduct(row.querySelector('.item-product').value);
        const quantity = Number(row.querySelector('.item-qty').value || 0);

        const total = product && quantity > 0 ? product.price * quantity : 0;

        row.dataset.total = total;
        row.querySelector('.item-total').value = total > 0 ? money(total) : '';

        updateTotals();
    }

    function fillSizes(row, product, selectedId) {
        const sizeSelect = row.querySelector('.item-size');

        sizeSelect.innerHTML = '<option value="">Select size</option>';

        product.sizes.forEach(function (size) {
            const option = document.createElement('option');

            option.value         = size.id;
            option.dataset.stock = size.stock;
            option.disabled      = size.stock <= 0;
            option.textContent   = size.stock > 0
                ? size.value + ' (' + size.stock + ' left)'
                : size.value + ' (out of stock)';

            sizeSelect.appendChild(option);
        });

        sizeSelect.disabled = false;

        if (selectedId) {
            sizeSelect.value = String(selectedId);
        }
    }

    // Products with sizes get a size dropdown, products without sizes do not.
    function setupSizes(row, product, selectedId) {
        const sizeSelect = row.querySelector('.item-size');

        if (product.sizes.length === 0) {
            sizeSelect.innerHTML = '<option value="">No sizes</option>';
            sizeSelect.disabled  = true;
            sizeSelect.required  = false;
            return;
        }

        sizeSelect.required = true;
        fillSizes(row, product, selectedId);
    }

    function applySizeLimit(row) {
        const sizeSelect = row.querySelector('.item-size');
        const qtyInput   = row.querySelector('.item-qty');
        const option     = sizeSelect.options[sizeSelect.selectedIndex];
        const product    = findProduct(row.querySelector('.item-product').value);

        // Product without sizes: limit by the product's own stock.
        if (product && product.sizes.length === 0) {
            qtyInput.disabled = product.stock <= 0;
            qtyInput.max      = product.stock;

            if (Number(qtyInput.value) > product.stock) {
                qtyInput.value = product.stock;
            }

            if (Number(qtyInput.value) < 1) {
                qtyInput.value = 1;
            }

            return;
        }

        if (!option || !option.value) {
            qtyInput.disabled = true;
            qtyInput.value    = 1;
            qtyInput.removeAttribute('max');
            return;
        }

        const stock = Number(option.dataset.stock);

        qtyInput.disabled = false;
        qtyInput.max      = stock;

        if (Number(qtyInput.value) > stock) {
            qtyInput.value = stock;
        }

        if (Number(qtyInput.value) < 1) {
            qtyInput.value = 1;
        }
    }

    function addRow(preset) {
        const index = rowIndex++;
        const row   = document.createElement('div');

        row.className = 'item-row';
        row.dataset.total = 0;

        row.innerHTML =
            '<div>' +
                '<label>Product</label>' +
                '<select class="item-product" name="items[' + index + '][product_id]" required>' +
                    '<option value="">Select product</option>' +
                '</select>' +
            '</div>' +
            '<div>' +
                '<label>Size</label>' +
                '<select class="item-size" name="items[' + index + '][variant_id]" required disabled>' +
                    '<option value="">Select size</option>' +
                '</select>' +
            '</div>' +
            '<div>' +
                '<label>Price</label>' +
                '<input type="text" class="item-price" readonly tabindex="-1">' +
            '</div>' +
            '<div>' +
                '<label>GST %</label>' +
                '<select class="item-tax" name="items[' + index + '][tax_rate]" disabled>' +
                    taxOptions +
                '</select>' +
            '</div>' +
            '<div>' +
                '<label>Qty</label>' +
                '<input type="number" class="item-qty" name="items[' + index + '][quantity]" ' +
                    'min="1" step="1" value="1" required disabled>' +
            '</div>' +
            '<div>' +
                '<label>Line total</label>' +
                '<input type="text" class="item-total" readonly tabindex="-1">' +
            '</div>' +
            '<button type="button" class="item-remove" title="Remove item">&times;</button>';

        const productSelect = row.querySelector('.item-product');
        const sizeSelect    = row.querySelector('.item-size');
        const priceInput    = row.querySelector('.item-price');
        const qtyInput      = row.querySelector('.item-qty');
        const taxSelect     = row.querySelector('.item-tax');

        taxSelect.value    = String(defaultTax);
        taxSelect.disabled = !gstIsOn();
        taxSelect.addEventListener('change', updateTotals);

        products.forEach(function (product) {
            const option = document.createElement('option');
            const soldOut = product.sizes.length === 0 && product.stock <= 0;

            option.value       = product.id;
            option.disabled    = soldOut;
            option.textContent = soldOut ? product.name + ' (out of stock)' : product.name;
            productSelect.appendChild(option);
        });

        // Product chosen: fill price and sizes.
        productSelect.addEventListener('change', function () {
            const product = findProduct(productSelect.value);

            qtyInput.value = 1;

            if (!product) {
                sizeSelect.innerHTML = '<option value="">Select size</option>';
                sizeSelect.disabled  = true;
                priceInput.value     = '';
                applySizeLimit(row);
                updateRowTotal(row);
                return;
            }

            priceInput.value = money(product.price);
            setupSizes(row, product, null);
            applySizeLimit(row);
            updateRowTotal(row);
        });

        // Size chosen: limit quantity to the stock left.
        sizeSelect.addEventListener('change', function () {
            qtyInput.value = 1;
            applySizeLimit(row);
            updateRowTotal(row);
        });

        // Quantity typed: keep it between 1 and the stock left.
        qtyInput.addEventListener('input', function () {
            const max = Number(qtyInput.max || 0);

            if (max && Number(qtyInput.value) > max) {
                qtyInput.value = max;
            }

            updateRowTotal(row);
        });

        row.querySelector('.item-remove').addEventListener('click', function () {
            row.remove();

            if (container.querySelectorAll('.item-row').length === 0) {
                addRow();
            }

            updateTotals();
        });

        container.appendChild(row);

        // Restore a row after a validation error.
        if (preset && preset.product_id) {
            const product = findProduct(preset.product_id);

            if (product) {
                // Keep the saved GST % (only meaningful when the invoice has GST).
                if (gstIsOn() && preset.tax_rate !== undefined && preset.tax_rate !== null) {
                    taxSelect.value = String(Number(preset.tax_rate));
                }

                productSelect.value = String(product.id);
                priceInput.value    = money(product.price);
                setupSizes(row, product, preset.variant_id);
                applySizeLimit(row);
                qtyInput.value = preset.quantity || 1;
                applySizeLimit(row);
                updateRowTotal(row);
            }
        }
    }

    addButton.addEventListener('click', function () {
        addRow();
    });

    discountEl.addEventListener('input', updateTotals);

    form.addEventListener('submit', function (event) {
        let subtotal = 0;

        container.querySelectorAll('.item-row').forEach(function (row) {
            subtotal += Number(row.dataset.total || 0);
        });

        if (subtotal <= 0) {
            event.preventDefault();
            alert('Please add at least one item.');
        }
    });
    if (oldItems.length > 0) {
        oldItems.forEach(function (item) {
            addRow(item);
        });
    } else {
        addRow();
    }

    gstToggle.addEventListener('change', applyGstState);

    applyGstState();
});
</script>

@endsection