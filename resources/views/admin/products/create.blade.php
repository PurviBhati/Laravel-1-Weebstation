@extends('layouts.app')

@section('title', 'Add Product | Admin')

@section('content')

<style>
    .product-form {
        max-width: 850px;
        margin: 0 auto;
    }

    .product-form h1 {
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
        color: #374151;
        font-size: 14px;
    }

    .form-card input:not([type=checkbox]):not([type=radio]):not([type=file]),
    .form-card select,
    .form-card textarea {
        background: #fff;
        color: #111827;
        border: 1px solid #d1d5db;
    }

    .form-card textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-card input:focus,
    .form-card select:focus,
    .form-card textarea:focus {
        outline: 2px solid #111827;
        outline-offset: 0;
    }

    .form-card select:disabled {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }

    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 16px;
    }

    .upload-box {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        background: #fafafa;
    }

    .upload-box p {
        margin: 0 0 10px;
        color: #6b7280;
        font-size: 13px;
    }

    .preview {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .preview img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
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

    .field-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 13px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .product-form .btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid #d1d5db;
    }

    .product-form .btn-light {
        background: #fff;
        color: #374151;
    }

    .product-form .btn-primary {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    .product-form .btn-primary:hover {
        background: #000;
    }

    /* Size rows */
    .size-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 12px;
        padding: 12px;
        margin-bottom: 8px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }

    .size-row label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .size-row .variant-stock {
        width: 100%;
    }

    .sizes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .sizes-header label {
        font-weight: 600;
        margin: 0;
    }

    .sizes-help {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .category-header label {
        margin: 0;
    }

    .category-header .btn {
        padding: 5px 10px;
        font-size: 13px;
    }

    @media (max-width: 650px) {
        .two-columns {
            grid-template-columns: 1fr;
        }

        .form-card {
            padding: 18px;
        }
    }
</style>

<div class="product-form">

    <h1>Add Product</h1>
    <p class="form-subtitle">Add a new product to your store.</p>

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form
        id="productForm"
        action="{{ url('/admin/products') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="form-card">

            {{-- Product Name --}}
            <div class="field">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" placeholder="Enter product name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Audience --}}
            <div class="field">
                <label for="audience_id">Audience</label>

                <select name="audience_id" id="audience_id" required>
                    <option value="">Choose Audience</option>

                    @foreach ($audiences as $audience)
                        <option
                            value="{{ $audience->id }}"
                            @selected(old('audience_id') == $audience->id)
                        >
                            {{ $audience->name }}
                        </option>
                    @endforeach
                </select>

                @error('audience_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category --}}
            <div class="field">
                <div class="category-header">
                    <label for="category_id">Category</label>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                        + Add Category
                    </a>
                </div>

                <select name="category_id" id="category_id" required disabled>
                    <option value="">Choose an audience first</option>
                </select>

                @error('category_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sizes and Individual Stock --}}
            <div class="field" id="sizes-section" style="display:none;">

                <div class="sizes-header">
                    <label>Available Sizes &amp; Stock</label>

                    <button type="button" id="clear-sizes" class="btn btn-light">
                        Clear Sizes
                    </button>
                </div>

                <p class="sizes-help" id="sizes-help">
                    Select the sizes you want to sell and enter stock for each selected size.
                    Minimum stock per selected size is 1.
                </p>

                <div id="sizes-container"></div>

                <div id="sizes-error" class="field-error">
                    @error('variants')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            {{-- Price and Total Stock --}}
            <div class="two-columns">

                <div class="field">
                    <label for="price">Price (₹)</label>

                    <input type="number" name="price" id="price" step="0.01" min="0" placeholder="0.00" value="{{ old('price') }}" required>

                    @error('price')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="stock">Total Stock</label>

                    <input type="number" name="stock" id="stock" min="0" step="1" value="{{ old('stock', 0) }}" readonly>

                    @error('stock')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Description --}}
            <div class="field">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Enter product description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="field">
                <label>Status</label>

                <div class="status-options">
                    <label>
                        <input type="radio" name="status" value="draft" @checked(old('status', 'draft') == 'draft')>Draft</label>
                    <label>
                        <input type="radio" name="status" value="active" @checked(old('status') == 'active')>Active</label>
                    <label>
                        <input type="radio" name="status" value="archived" @checked(old('status') == 'archived')>Archived</label>
                </div>
                @error('status')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Featured --}}
            <!--<div class="check">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured'))>
                <label for="is_featured">
                    Show on the home page (featured)
                </label>
            </div> -->

            {{-- Product Images --}}
            <div class="field">
                <label for="images">Product Images</label>

                <div class="upload-box">
                    <p>JPG, PNG or WEBP. Maximum 2 MB each, up to 5 images.</p>

                    <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp">
                </div>

                <div class="preview" id="preview"></div>

                @error('images')
                    <div class="field-error">{{ $message }}</div>
                @enderror

                @error('images.*')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="actions">
                <a href="{{ url('/admin/products') }}" class="btn btn-light">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    Save Product
                </button>
            </div>

        </div>
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {

    // IMAGE PREVIEW

    const imageInput = document.getElementById('images');
    const preview    = document.getElementById('preview');

    if (imageInput && preview) {
        imageInput.addEventListener('change', function () {
            preview.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length > 5) {
                alert('You can select a maximum of 5 images.');
                this.value = '';
                return;
            }

            files.forEach(function (file) {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = 'Preview of the chosen image';
                    preview.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        });
    }


    // ELEMENTS AND DATA

    const form            = $('#productForm');
    const audienceSelect  = $('#audience_id');
    const categorySelect  = $('#category_id');
    const sizesSection    = $('#sizes-section');
    const sizesContainer  = $('#sizes-container');
    const totalStockInput = $('#stock');
    const sizesError      = $('#sizes-error');
    const sizesHelp       = $('#sizes-help');
    const clearSizesButton = $('#clear-sizes');

    const categoriesUrl = @json(
        route('admin.audiences.categories', ['audience' => '__ID__'])
    );

    // Previous selections, used after a validation error.
    const oldVariants   = @json(old('variants', []));
    const oldCategoryId = @json(old('category_id'));

    // Categories of the chosen audience (loaded once, reused on change).
    let loadedCategories = [];
    let categoryHasVariants = false;

    // TOTAL STOCK (display only, the server recalculates it)

    function updateTotalStock() {
        let total = 0;

        sizesContainer.find('.variant-stock:enabled').each(function () {
            const n = Number(this.value);

            if (this.value !== '' && Number.isInteger(n) && n >= 1) {
                total += n;
            }
        });

        totalStockInput.val(total);
    }
    // SHOW SIZE CHECKBOXES

    function renderSizes(category, restoreValues) {
        sizesContainer.empty();
        sizesError.text('');

        sizesSection.show();
        categoryHasVariants = Boolean(
            category &&
            Array.isArray(category.sizes) &&
            category.sizes.length > 0
        );

        if (!categoryHasVariants) {
            sizesHelp.hide();
            clearSizesButton.hide();
            sizesContainer.html(
                '<p>No sizes apply to this category. Enter the total stock below.</p>'
            );
            totalStockInput.prop('readonly', false).prop('required', true);
            return;
        }

        sizesHelp.show();
        clearSizesButton.show();
        totalStockInput.prop('readonly', true).prop('required', false).val(0);

        category.sizes.forEach(function (size) {
            const id = String(size.id);

            const restore =
                restoreValues &&
                Object.prototype.hasOwnProperty.call(oldVariants, id);

            // Row
            const row = document.createElement('div');
            row.className = 'size-row';

            // Checkbox + size name
            const label = document.createElement('label');

            const checkbox = document.createElement('input');
            checkbox.type      = 'checkbox';
            checkbox.className = 'variant-checkbox';
            checkbox.checked   = restore;

            const sizeText = document.createElement('span');
            sizeText.textContent = size.value;

            label.appendChild(checkbox);
            label.appendChild(sizeText);

            // Stock input (only enabled inputs are submitted)
            const quantity = document.createElement('input');
            quantity.type        = 'number';
            quantity.className   = 'variant-stock';
            quantity.name        = 'variants[' + id + ']';
            quantity.min         = '1';
            quantity.step        = '1';
            quantity.placeholder = 'Enter stock';
            quantity.required    = true;
            quantity.disabled    = !restore;

            if (restore) {
                quantity.value = oldVariants[id];
            }

            // Checkbox toggled
            checkbox.addEventListener('change', function () {
                quantity.disabled = !checkbox.checked;
                quantity.value    = '';
                quantity.setCustomValidity('');

                if (checkbox.checked) {
                    quantity.focus();
                }

                sizesError.text('');
                updateTotalStock();
            });

            // Stock typed
            quantity.addEventListener('input', function () {
                const n = Number(quantity.value);

                if (quantity.value === '' || !Number.isInteger(n) || n < 1) {
                    quantity.setCustomValidity('Enter a whole number greater than zero.');
                } else {
                    quantity.setCustomValidity('');
                }

                updateTotalStock();
            });

            row.appendChild(label);
            row.appendChild(quantity);
            sizesContainer.append(row);
        });

        updateTotalStock();
    }

    // LOAD CATEGORIES FOR THE CHOSEN AUDIENCE

    function loadCategories(audienceId, selectedCategoryId, restore) {
        loadedCategories = [];

        sizesSection.hide();
        sizesContainer.empty();
        totalStockInput.prop('readonly', true).prop('required', false);

        if (!audienceId) {
            categorySelect
                .html('<option value="">Choose an audience first</option>')
                .prop('disabled', true);
            return;
        }

        categorySelect
            .html('<option value="">Loading categories...</option>')
            .prop('disabled', true);

        $.ajax({
            url: categoriesUrl.replace('__ID__', encodeURIComponent(audienceId)),
            method: 'GET',
            dataType: 'json',

            success: function (categories) {
                loadedCategories = categories;
                categorySelect.empty();

                if (categories.length === 0) {
                    categorySelect
                        .html('<option value="">No categories for this audience</option>')
                        .prop('disabled', true);
                    return;
                }

                categorySelect.append('<option value="">Choose Category</option>');

                categories.forEach(function (category) {
                    categorySelect.append(
                        $('<option>', { value: category.id, text: category.name })
                    );
                });

                categorySelect.prop('disabled', false);

                if (selectedCategoryId) {
                    categorySelect.val(String(selectedCategoryId));

                    const selected = categories.find(function (c) {
                        return String(c.id) === String(selectedCategoryId);
                    });

                    if (selected) {
                        renderSizes(selected, restore);
                    }
                }
            },

            error: function (xhr) {
                categorySelect
                    .html('<option value="">Unable to load categories</option>')
                    .prop('disabled', true);

                console.error('Category AJAX error:', xhr.status, xhr.responseText);
            }
        });
    }

    // EVENTS

    audienceSelect.on('change', function () {
        loadCategories(this.value, '', false);
    });

    categorySelect.on('change', function () {
        const categoryId = String(this.value);

        sizesSection.hide();
        sizesContainer.empty();
        sizesError.text('');

        if (!categoryId) {
            return;
        }

        const category = loadedCategories.find(function (c) {
            return String(c.id) === categoryId;
        });

        renderSizes(category, false);
    });

    $('#clear-sizes').on('click', function () {
        sizesContainer.find('.variant-checkbox').prop('checked', false);

        sizesContainer.find('.variant-stock')
            .prop('disabled', true)
            .val('')
            .each(function () {
                this.setCustomValidity('');
            });

        sizesError.text('');
        totalStockInput.val(0);
    });

    // Block submit if no size is picked or a stock value is invalid.
    form.on('submit', function (event) {
        if (!categoryHasVariants) {
            return;
        }

        const selected = sizesContainer.find('.variant-checkbox:checked');

        if (selected.length === 0) {
            event.preventDefault();
            sizesSection.show();
            sizesError.text('Please select at least one size.');
            return;
        }

        let invalid = false;

        selected.each(function () {
            const quantity = $(this).closest('.size-row').find('.variant-stock')[0];
            const n        = Number(quantity.value);

            if (quantity.value === '' || !Number.isInteger(n) || n < 1) {
                invalid = true;
                quantity.setCustomValidity('Enter a whole number greater than zero.');
                quantity.reportValidity();
                return false;
            }

            quantity.setCustomValidity('');
        });

        if (invalid) {
            event.preventDefault();
        }
    });

    // ============================================================
    // RESTORE AFTER A VALIDATION ERROR
    // ============================================================
    if (audienceSelect.val()) {
        loadCategories(audienceSelect.val(), oldCategoryId, true);
    }
});
</script>

@endsection